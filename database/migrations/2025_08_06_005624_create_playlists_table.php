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
        Schema::create('playlists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_public')->default(true);
            $table->string('cover_image_url')->nullable();
            $table->string('external_id')->nullable();
            $table->string('external_link')->nullable();
            $table->string('platform')->nullable();
            $table->integer('total_duration')->default(0);
            $table->integer('track_count')->default(0);
            $table->string('genre')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'is_public']);
            $table->index(['platform', 'external_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playlists');
    }
};
