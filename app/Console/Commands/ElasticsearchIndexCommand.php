<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Song;
use App\Services\SearchService;
use Elastic\Elasticsearch\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ElasticsearchIndexCommand extends Command
{
    protected $signature = 'elasticsearch:index 
                            {action : The action to perform (create, delete, reindex, status)}
                            {--type= : Type of content to index (songs, albums, artists, all)}
                            {--force : Force the operation without confirmation}';

    protected $description = 'Manage Elasticsearch indices and data';

    public function __construct(
        private Client $elasticsearch,
        private SearchService $searchService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $action = $this->argument('action');
        $type = $this->option('type') ?? 'all';
        $force = $this->option('force');

        try {
            return match ($action) {
                'create' => $this->createIndices($force),
                'delete' => $this->deleteIndices($force),
                'reindex' => $this->reindexData($type, $force),
                'status' => $this->showStatus(),
                default => $this->error("Unknown action: {$action}") ?? 1,
            };
        } catch (\Exception $e) {
            $this->error("Error: {$e->getMessage()}");
            Log::error('Elasticsearch command failed', [
                'action' => $action,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
            
            return 1;
        }
    }

    private function createIndices(bool $force): int
    {
        if (!$force && !$this->confirm('This will create Elasticsearch indices. Continue?')) {
            return 0;
        }

        $this->info('Creating Elasticsearch indices...');

        $indices = config('elasticsearch.indices');
        $created = 0;

                    foreach ($indices as $indexName => $indexConfig) {
                try {
                    $response = $this->elasticsearch->indices()->create([
                        'index' => $indexConfig['name'],
                        'body' => [
                            'settings' => $indexConfig['settings'],
                            'mappings' => $indexConfig['mappings'],
                        ],
                    ]);

                    if ($response->getStatusCode() === 200) {
                        $this->info("✓ Created index: {$indexConfig['name']}");
                        $created++;
                    }
                } catch (\Exception $e) {
                    if (str_contains($e->getMessage(), 'resource_already_exists_exception')) {
                        $this->warn("Index {$indexConfig['name']} already exists");
                    } else {
                        $this->error("Failed to create index {$indexConfig['name']}: {$e->getMessage()}");
                    }
                }
            }

        $this->info("Created {$created} indices successfully.");
        return 0;
    }

    private function deleteIndices(bool $force): int
    {
        if (!$force && !$this->confirm('This will delete all Elasticsearch indices. This action cannot be undone. Continue?')) {
            return 0;
        }

        $this->info('Deleting Elasticsearch indices...');

        $indices = config('elasticsearch.indices');
        $deleted = 0;

        foreach ($indices as $indexName => $indexConfig) {
            try {
                $this->elasticsearch->indices()->delete([
                    'index' => $indexConfig['name'],
                ]);

                $this->info("✓ Deleted index: {$indexConfig['name']}");
                $deleted++;
            } catch (\Exception $e) {
                if (str_contains($e->getMessage(), 'index_not_found_exception')) {
                    $this->warn("Index {$indexConfig['name']} does not exist");
                } else {
                    $this->error("Failed to delete index {$indexConfig['name']}: {$e->getMessage()}");
                }
            }
        }

        $this->info("Deleted {$deleted} indices successfully.");
        return 0;
    }

    private function reindexData(string $type, bool $force): int
    {
        if (!$force && !$this->confirm('This will reindex all data in Elasticsearch. Continue?')) {
            return 0;
        }

        $this->info('Reindexing data in Elasticsearch...');

        $progressBar = $this->output->createProgressBar();
        $progressBar->start();

        $indexed = 0;

        try {
            // Index songs
            if ($type === 'all' || $type === 'songs') {
                $this->info("\nIndexing songs...");
                $songs = Song::with(['artist', 'album'])->chunk(100, function ($songs) use (&$indexed, $progressBar) {
                    foreach ($songs as $song) {
                        if ($this->searchService->indexSong($song)) {
                            $indexed++;
                        }
                        $progressBar->advance();
                    }
                });
            }

            // Index albums
            if ($type === 'all' || $type === 'albums') {
                $this->info("\nIndexing albums...");
                $albums = Album::with('artist')->chunk(100, function ($albums) use (&$indexed, $progressBar) {
                    foreach ($albums as $album) {
                        if ($this->searchService->indexAlbum($album)) {
                            $indexed++;
                        }
                        $progressBar->advance();
                    }
                });
            }

            // Index artists
            if ($type === 'all' || $type === 'artists') {
                $this->info("\nIndexing artists...");
                $artists = Artist::chunk(100, function ($artists) use (&$indexed, $progressBar) {
                    foreach ($artists as $artist) {
                        if ($this->searchService->indexArtist($artist)) {
                            $indexed++;
                        }
                        $progressBar->advance();
                    }
                });
            }

            $progressBar->finish();
            $this->info("\n✓ Successfully indexed {$indexed} items.");

            return 0;
        } catch (\Exception $e) {
            $this->error("\nFailed to reindex data: {$e->getMessage()}");
            return 1;
        }
    }

    private function showStatus(): int
    {
        $this->info('Elasticsearch Status:');
        $this->info('==================');

        try {
            // Check cluster health
            $health = $this->elasticsearch->cluster()->health();
            $this->info("Cluster Status: {$health['status']}");
            $this->info("Number of Nodes: {$health['number_of_nodes']}");
            $this->info("Active Shards: {$health['active_shards']}");

            $this->info("\nIndices:");
            $indices = config('elasticsearch.indices');
            
            foreach ($indices as $indexName => $indexConfig) {
                try {
                    $stats = $this->elasticsearch->indices()->stats([
                        'index' => $indexConfig['name'],
                    ]);

                    $docCount = $stats['indices'][$indexConfig['name']]['total']['docs']['count'] ?? 0;
                    $this->info("  {$indexConfig['name']}: {$docCount} documents");
                } catch (\Exception $e) {
                    $this->warn("  {$indexConfig['name']}: Not found");
                }
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to get Elasticsearch status: {$e->getMessage()}");
            return 1;
        }
    }
}
