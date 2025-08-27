<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    public function __construct(
        private SearchService $searchService
    ) {}

    /**
     * Show the search page
     */
    public function index(): Response
    {
        return Inertia::render('Search/Index', [
            'initialResults' => [
                'songs' => ['total' => 0, 'results' => []],
                'albums' => ['total' => 0, 'results' => []],
                'artists' => ['total' => 0, 'results' => []],
            ],
        ]);
    }

    /**
     * Search across all content types
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:1|max:255',
            'limit' => 'integer|min:1|max:100',
        ]);

        $query = $request->input('query');
        $limit = $request->input('limit', 20);

        $results = $this->searchService->search($query, $limit);

        return response()->json([
            'success' => true,
            'data' => $results,
            'query' => $query,
        ]);
    }

    /**
     * Search songs only
     */
    public function searchSongs(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:1|max:255',
            'limit' => 'integer|min:1|max:100',
        ]);

        $query = $request->input('query');
        $limit = $request->input('limit', 20);

        $results = $this->searchService->searchSongs($query, $limit);

        return response()->json([
            'success' => true,
            'data' => $results,
            'query' => $query,
        ]);
    }

    /**
     * Search albums only
     */
    public function searchAlbums(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:1|max:255',
            'limit' => 'integer|min:1|max:100',
        ]);

        $query = $request->input('query');
        $limit = $request->input('limit', 20);

        $results = $this->searchService->searchAlbums($query, $limit);

        return response()->json([
            'success' => true,
            'data' => $results,
            'query' => $query,
        ]);
    }

    /**
     * Search artists only
     */
    public function searchArtists(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:1|max:255',
            'limit' => 'integer|min:1|max:100',
        ]);

        $query = $request->input('query');
        $limit = $request->input('limit', 20);

        $results = $this->searchService->searchArtists($query, $limit);

        return response()->json([
            'success' => true,
            'data' => $results,
            'query' => $query,
        ]);
    }

    /**
     * Get autocomplete suggestions
     */
    public function autocomplete(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:1|max:255',
            'type' => 'string|in:songs,albums,artists',
            'limit' => 'integer|min:1|max:20',
        ]);

        $query = $request->input('query');
        $type = $request->input('type', 'songs');
        $limit = $request->input('limit', 10);

        $suggestions = match ($type) {
            'songs' => $this->searchService->autocompleteSongs($query, $limit),
            'albums' => $this->searchService->searchAlbums($query, $limit),
            'artists' => $this->searchService->searchArtists($query, $limit),
            default => [],
        };

        return response()->json([
            'success' => true,
            'data' => $suggestions,
            'query' => $query,
            'type' => $type,
        ]);
    }

    /**
     * Get search statistics
     */
    public function stats(): JsonResponse
    {
        try {
            // This would typically come from Elasticsearch stats
            // For now, we'll return basic stats from the database
            $stats = [
                'total_songs' => \App\Models\Song::count(),
                'total_albums' => \App\Models\Album::count(),
                'total_artists' => \App\Models\Artist::count(),
                'total_genres' => \App\Models\Song::distinct('genre')->count(),
                'avg_song_duration' => \App\Models\Song::avg('duration'),
                'recent_additions' => [
                    'songs' => \App\Models\Song::latest()->take(5)->get(),
                    'albums' => \App\Models\Album::latest()->take(5)->get(),
                    'artists' => \App\Models\Artist::latest()->take(5)->get(),
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve search statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
