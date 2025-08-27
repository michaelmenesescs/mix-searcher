<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        // Get recent tracks for the dashboard
        $tracks = App\Models\Track::orderBy('added_date', 'desc')->take(12)->get();
        
        return Inertia::render('dashboard', [
            'tracks' => $tracks
        ]);
    })->name('dashboard');
    
    // Albums routes
    Route::get('albums', [App\Http\Controllers\AlbumController::class, 'index'])->name('albums.index');



    
    
    // Search routes
    Route::prefix('search')->group(function () {
        Route::get('/', [App\Http\Controllers\SearchController::class, 'index'])->name('search.index');
        Route::post('/', [App\Http\Controllers\SearchController::class, 'search'])->name('search.all');
        Route::post('/songs', [App\Http\Controllers\SearchController::class, 'searchSongs'])->name('search.songs');
        Route::post('/albums', [App\Http\Controllers\SearchController::class, 'searchAlbums'])->name('search.albums');
        Route::post('/artists', [App\Http\Controllers\SearchController::class, 'searchArtists'])->name('search.artists');
        Route::post('/autocomplete', [App\Http\Controllers\SearchController::class, 'autocomplete'])->name('search.autocomplete');
        Route::get('/stats', [App\Http\Controllers\SearchController::class, 'stats'])->name('search.stats');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
