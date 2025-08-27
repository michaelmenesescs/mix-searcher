<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Song;
use Elastic\Elasticsearch\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class SearchService
{
    public function __construct(
        private Client $elasticsearch
    ) {}

    /**
     * Search across all content types
     */
    public function search(string $query, int $limit = 20): array
    {
        $results = [
            'songs' => $this->searchSongs($query, $limit),
            'albums' => $this->searchAlbums($query, $limit),
            'artists' => $this->searchArtists($query, $limit),
        ];

        return $results;
    }

    /**
     * Search songs with autocomplete
     */
    public function searchSongs(string $query, int $limit = 20): array
    {
        try {
            $response = $this->elasticsearch->search([
                'index' => 'songs',
                'body' => [
                    'size' => $limit,
                    'query' => [
                        'multi_match' => [
                            'query' => $query,
                            'fields' => ['title^3', 'artist_name^2', 'album_title', 'genre', 'lyrics'],
                            'type' => 'best_fields',
                            'fuzziness' => 'AUTO',
                        ],
                    ],
                    'highlight' => [
                        'fields' => [
                            'title' => new \stdClass(),
                            'artist_name' => new \stdClass(),
                            'album_title' => new \stdClass(),
                        ],
                    ],
                ],
            ]);

            return $this->formatSearchResults($response->asArray(), Song::class);
        } catch (\Exception $e) {
            Log::error('Elasticsearch song search failed', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);
            
            // Fallback to database search
            return $this->fallbackSongSearch($query, $limit);
        }
    }

    /**
     * Search albums with autocomplete
     */
    public function searchAlbums(string $query, int $limit = 20): array
    {
        try {
            $response = $this->elasticsearch->search([
                'index' => 'albums',
                'body' => [
                    'size' => $limit,
                    'query' => [
                        'multi_match' => [
                            'query' => $query,
                            'fields' => ['title^3', 'artist_name^2', 'genre'],
                            'type' => 'best_fields',
                            'fuzziness' => 'AUTO',
                        ],
                    ],
                    'highlight' => [
                        'fields' => [
                            'title' => new \stdClass(),
                            'artist_name' => new \stdClass(),
                        ],
                    ],
                ],
            ]);

            return $this->formatSearchResults($response->asArray(), Album::class);
        } catch (\Exception $e) {
            Log::error('Elasticsearch album search failed', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);
            
            // Fallback to database search
            return $this->fallbackAlbumSearch($query, $limit);
        }
    }

    /**
     * Search artists with autocomplete
     */
    public function searchArtists(string $query, int $limit = 20): array
    {
        try {
            $response = $this->elasticsearch->search([
                'index' => 'artists',
                'body' => [
                    'size' => $limit,
                    'query' => [
                        'multi_match' => [
                            'query' => $query,
                            'fields' => ['name^3', 'bio'],
                            'type' => 'best_fields',
                            'fuzziness' => 'AUTO',
                        ],
                    ],
                    'highlight' => [
                        'fields' => [
                            'name' => new \stdClass(),
                            'bio' => new \stdClass(),
                        ],
                    ],
                ],
            ]);

            return $this->formatSearchResults($response->asArray(), Artist::class);
        } catch (\Exception $e) {
            Log::error('Elasticsearch artist search failed', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);
            
            // Fallback to database search
            return $this->fallbackArtistSearch($query, $limit);
        }
    }

    /**
     * Autocomplete suggestions for songs
     */
    public function autocompleteSongs(string $query, int $limit = 10): array
    {
        try {
            $response = $this->elasticsearch->search([
                'index' => 'songs',
                'body' => [
                    'size' => 0,
                    'suggest' => [
                        'song_suggestions' => [
                            'prefix' => $query,
                            'completion' => [
                                'field' => 'title_suggest',
                                'size' => $limit,
                                'skip_duplicates' => true,
                            ],
                        ],
                    ],
                ],
            ]);

            return Arr::get($response->asArray(), 'suggest.song_suggestions.0.options', []);
        } catch (\Exception $e) {
            Log::error('Elasticsearch song autocomplete failed', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);
            
            return [];
        }
    }

    /**
     * Index a song in Elasticsearch
     */
    public function indexSong(Song $song): bool
    {
        try {
            $response = $this->elasticsearch->index([
                'index' => 'songs',
                'id' => $song->id,
                'body' => [
                    'id' => $song->id,
                    'title' => $song->title,
                    'artist_name' => $song->artist?->name,
                    'album_title' => $song->album?->title,
                    'genre' => $song->genre,
                    'duration' => $song->duration,
                    'track_number' => $song->track_number,
                    'disc_number' => $song->disc_number,
                    'release_year' => $song->album?->release_year,
                    'lyrics' => $song->lyrics,
                    'external_id' => $song->external_id,
                    'external_link' => $song->external_link,
                    'preview_url' => $song->preview_url,
                    'created_at' => $song->created_at?->toISOString(),
                    'updated_at' => $song->updated_at?->toISOString(),
                ],
            ]);

            return $response->getStatusCode() === 200 || $response->getStatusCode() === 201;
        } catch (\Exception $e) {
            Log::error('Failed to index song in Elasticsearch', [
                'song_id' => $song->id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Index an album in Elasticsearch
     */
    public function indexAlbum(Album $album): bool
    {
        try {
            $response = $this->elasticsearch->index([
                'index' => 'albums',
                'id' => $album->id,
                'body' => [
                    'id' => $album->id,
                    'title' => $album->title,
                    'artist_name' => $album->artist?->name,
                    'genre' => $album->genre,
                    'release_year' => $album->release_year,
                    'total_duration' => $album->total_duration,
                    'track_count' => $album->track_count,
                    'cover_image_url' => $album->cover_image_url,
                    'external_id' => $album->external_id,
                    'external_link' => $album->external_link,
                    'created_at' => $album->created_at?->toISOString(),
                    'updated_at' => $album->updated_at?->toISOString(),
                ],
            ]);

            return $response->getStatusCode() === 200 || $response->getStatusCode() === 201;
        } catch (\Exception $e) {
            Log::error('Failed to index album in Elasticsearch', [
                'album_id' => $album->id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Index an artist in Elasticsearch
     */
    public function indexArtist(Artist $artist): bool
    {
        try {
            $response = $this->elasticsearch->index([
                'index' => 'artists',
                'id' => $artist->id,
                'body' => [
                    'id' => $artist->id,
                    'name' => $artist->name,
                    'bio' => $artist->bio,
                    'image_url' => $artist->image_url,
                    'external_id' => $artist->external_id,
                    'external_link' => $artist->external_link,
                    'song_count' => $artist->song_count,
                    'album_count' => $artist->album_count,
                    'total_duration' => $artist->total_duration,
                    'created_at' => $artist->created_at?->toISOString(),
                    'updated_at' => $artist->updated_at?->toISOString(),
                ],
            ]);

            return $response->getStatusCode() === 200 || $response->getStatusCode() === 201;
        } catch (\Exception $e) {
            Log::error('Failed to index artist in Elasticsearch', [
                'artist_id' => $artist->id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Remove a song from Elasticsearch
     */
    public function removeSong(int $songId): bool
    {
        try {
            $response = $this->elasticsearch->delete([
                'index' => 'songs',
                'id' => $songId,
            ]);

            return $response->getStatusCode() === 200 || $response->getStatusCode() === 404;
        } catch (\Exception $e) {
            Log::error('Failed to remove song from Elasticsearch', [
                'song_id' => $songId,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Remove an album from Elasticsearch
     */
    public function removeAlbum(int $albumId): bool
    {
        try {
            $response = $this->elasticsearch->delete([
                'index' => 'albums',
                'id' => $albumId,
            ]);

            return $response->getStatusCode() === 200 || $response->getStatusCode() === 404;
        } catch (\Exception $e) {
            Log::error('Failed to remove album from Elasticsearch', [
                'album_id' => $albumId,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Remove an artist from Elasticsearch
     */
    public function removeArtist(int $artistId): bool
    {
        try {
            $response = $this->elasticsearch->delete([
                'index' => 'artists',
                'id' => $artistId,
            ]);

            return $response->getStatusCode() === 200 || $response->getStatusCode() === 404;
        } catch (\Exception $e) {
            Log::error('Failed to remove artist from Elasticsearch', [
                'artist_id' => $artistId,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Format Elasticsearch search results
     */
    private function formatSearchResults(array $response, string $modelClass): array
    {
        $hits = Arr::get($response, 'hits.hits', []);
        $total = Arr::get($response, 'hits.total.value', 0);
        
        $results = [];
        foreach ($hits as $hit) {
            $source = $hit['_source'];
            $highlight = $hit['_highlight'] ?? [];
            
            $results[] = [
                'id' => $source['id'],
                'score' => $hit['_score'],
                'highlight' => $highlight,
                'source' => $source,
            ];
        }

        return [
            'total' => $total,
            'results' => $results,
        ];
    }

    /**
     * Fallback song search using database
     */
    private function fallbackSongSearch(string $query, int $limit): array
    {
        $songs = Song::with(['artist', 'album'])
            ->where('title', 'like', "%{$query}%")
            ->orWhereHas('artist', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->orWhereHas('album', function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%");
            })
            ->limit($limit)
            ->get();

        return [
            'total' => $songs->count(),
            'results' => $songs->map(function ($song) {
                return [
                    'id' => $song->id,
                    'score' => 1.0,
                    'highlight' => [],
                    'source' => $song->toArray(),
                ];
            })->toArray(),
        ];
    }

    /**
     * Fallback album search using database
     */
    private function fallbackAlbumSearch(string $query, int $limit): array
    {
        $albums = Album::with('artist')
            ->where('title', 'like', "%{$query}%")
            ->orWhereHas('artist', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->limit($limit)
            ->get();

        return [
            'total' => $albums->count(),
            'results' => $albums->map(function ($album) {
                return [
                    'id' => $album->id,
                    'score' => 1.0,
                    'highlight' => [],
                    'source' => $album->toArray(),
                ];
            })->toArray(),
        ];
    }

    /**
     * Fallback artist search using database
     */
    private function fallbackArtistSearch(string $query, int $limit): array
    {
        $artists = Artist::where('name', 'like', "%{$query}%")
            ->orWhere('bio', 'like', "%{$query}%")
            ->limit($limit)
            ->get();

        return [
            'total' => $artists->count(),
            'results' => $artists->map(function ($artist) {
                return [
                    'id' => $artist->id,
                    'score' => 1.0,
                    'highlight' => [],
                    'source' => $artist->toArray(),
                ];
            })->toArray(),
        ];
    }
}
