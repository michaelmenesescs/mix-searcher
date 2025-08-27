<?php

namespace App\Providers;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Song;
use App\Observers\AlbumObserver;
use App\Observers\ArtistObserver;
use App\Observers\SongObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Elasticsearch observers
        Song::observe(SongObserver::class);
        Album::observe(AlbumObserver::class);
        Artist::observe(ArtistObserver::class);
    }
}
