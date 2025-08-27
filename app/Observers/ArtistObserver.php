<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Artist;
use App\Services\SearchService;
use Illuminate\Support\Facades\Log;

class ArtistObserver
{
    public function __construct(
        private SearchService $searchService
    ) {}

    /**
     * Handle the Artist "created" event.
     */
    public function created(Artist $artist): void
    {
        try {
            $this->searchService->indexArtist($artist);
            Log::info('Artist indexed in Elasticsearch', ['artist_id' => $artist->id]);
        } catch (\Exception $e) {
            Log::error('Failed to index artist in Elasticsearch', [
                'artist_id' => $artist->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the Artist "updated" event.
     */
    public function updated(Artist $artist): void
    {
        try {
            $this->searchService->indexArtist($artist);
            Log::info('Artist updated in Elasticsearch', ['artist_id' => $artist->id]);
        } catch (\Exception $e) {
            Log::error('Failed to update artist in Elasticsearch', [
                'artist_id' => $artist->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the Artist "deleted" event.
     */
    public function deleted(Artist $artist): void
    {
        try {
            $this->searchService->removeArtist($artist->id);
            Log::info('Artist removed from Elasticsearch', ['artist_id' => $artist->id]);
        } catch (\Exception $e) {
            Log::error('Failed to remove artist from Elasticsearch', [
                'artist_id' => $artist->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the Artist "restored" event.
     */
    public function restored(Artist $artist): void
    {
        try {
            $this->searchService->indexArtist($artist);
            Log::info('Artist restored in Elasticsearch', ['artist_id' => $artist->id]);
        } catch (\Exception $e) {
            Log::error('Failed to restore artist in Elasticsearch', [
                'artist_id' => $artist->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
