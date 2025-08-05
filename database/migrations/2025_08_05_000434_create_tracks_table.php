<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->string('platform'); // spotify, apple_music, tidal, youtube_music, etc.
            $table->string('type')->default('track'); // track, album, artist, playlist
            $table->string('external_id'); // Platform-specific ID (spotify_id, apple_id, etc.)
            $table->string('title');
            $table->string('artist');
            $table->string('artist_external_id')->nullable(); // Platform artist ID
            $table->string('artist_link')->nullable();
            $table->string('album');
            $table->string('album_external_id')->nullable(); // Platform album ID
            $table->string('album_link')->nullable();
            $table->string('isrc')->nullable(); // International Standard Recording Code
            $table->integer('duration')->nullable(); // Duration in seconds
            $table->string('track_link')->nullable();
            $table->string('preview_url')->nullable();
            $table->string('picture')->nullable(); // Album/track artwork
            $table->bigInteger('added_date')->nullable(); // Unix timestamp
            $table->string('added_by')->nullable();
            $table->string('position')->nullable(); // Position in playlist
            $table->json('share_urls')->nullable();
            $table->json('metadata')->nullable(); // Platform-specific metadata
            $table->timestamps();
            
            // Composite unique index for platform + external_id
            $table->unique(['platform', 'external_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracks');
    }
};
