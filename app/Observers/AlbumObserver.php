<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Album;
use App\Services\SearchService;
use Illuminate\Support\Facades\Log;

class AlbumObserver
{
    public function __construct(
        private SearchService $searchService
    ) {}

    /**
     * Handle the Album "created" event.
     */
    public function created(Album $album): void
    {
        try {
            $this->searchService->indexAlbum($album);
            Log::info('Album indexed in Elasticsearch', ['album_id' => $album->id]);
        } catch (\Exception $e) {
            Log::error('Failed to index album in Elasticsearch', [
                'album_id' => $album->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the Album "updated" event.
     */
    public function updated(Album $album): void
    {
        try {
            $this->searchService->indexAlbum($album);
            Log::info('Album updated in Elasticsearch', ['album_id' => $album->id]);
        } catch (\Exception $e) {
            Log::error('Failed to update album in Elasticsearch', [
                'album_id' => $album->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the Album "deleted" event.
     */
    public function deleted(Album $album): void
    {
        try {
            $this->searchService->removeAlbum($album->id);
            Log::info('Album removed from Elasticsearch', ['album_id' => $album->id]);
        } catch (\Exception $e) {
            Log::error('Failed to remove album from Elasticsearch', [
                'album_id' => $album->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the Album "restored" event.
     */
    public function restored(Album $album): void
    {
        try {
            $this->searchService->indexAlbum($album);
            Log::info('Album restored in Elasticsearch', ['album_id' => $album->id]);
        } catch (\Exception $e) {
            Log::error('Failed to restore album in Elasticsearch', [
                'album_id' => $album->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
