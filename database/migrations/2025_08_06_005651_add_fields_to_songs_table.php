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
        Schema::table('songs', function (Blueprint $table) {
            $table->string('external_id')->nullable()->after('genre');
            $table->string('external_link')->nullable()->after('external_id');
            $table->string('isrc')->nullable()->after('external_link');
            $table->integer('track_number')->nullable()->after('isrc');
            $table->integer('disc_number')->nullable()->after('track_number');
            $table->text('lyrics')->nullable()->after('disc_number');
            $table->string('preview_url')->nullable()->after('lyrics');
            
            $table->index('external_id');
            $table->index('isrc');
            $table->index(['album_id', 'track_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropIndex(['external_id', 'isrc', 'album_id', 'track_number']);
            $table->dropColumn(['external_id', 'external_link', 'isrc', 'track_number', 'disc_number', 'lyrics', 'preview_url']);
        });
    }
};
