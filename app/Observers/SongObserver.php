<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Song;
use App\Services\SearchService;
use Illuminate\Support\Facades\Log;

class SongObserver
{
    public function __construct(
        private SearchService $searchService
    ) {}

    /**
     * Handle the Song "created" event.
     */
    public function created(Song $song): void
    {
        try {
            $this->searchService->indexSong($song);
            Log::info('Song indexed in Elasticsearch', ['song_id' => $song->id]);
        } catch (\Exception $e) {
            Log::error('Failed to index song in Elasticsearch', [
                'song_id' => $song->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the Song "updated" event.
     */
    public function updated(Song $song): void
    {
        try {
            $this->searchService->indexSong($song);
            Log::info('Song updated in Elasticsearch', ['song_id' => $song->id]);
        } catch (\Exception $e) {
            Log::error('Failed to update song in Elasticsearch', [
                'song_id' => $song->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the Song "deleted" event.
     */
    public function deleted(Song $song): void
    {
        try {
            $this->searchService->removeSong($song->id);
            Log::info('Song removed from Elasticsearch', ['song_id' => $song->id]);
        } catch (\Exception $e) {
            Log::error('Failed to remove song from Elasticsearch', [
                'song_id' => $song->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the Song "restored" event.
     */
    public function restored(Song $song): void
    {
        try {
            $this->searchService->indexSong($song);
            Log::info('Song restored in Elasticsearch', ['song_id' => $song->id]);
        } catch (\Exception $e) {
            Log::error('Failed to restore song in Elasticsearch', [
                'song_id' => $song->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
