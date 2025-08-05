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
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
