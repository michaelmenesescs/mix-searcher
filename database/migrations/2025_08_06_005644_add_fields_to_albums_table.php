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
        Schema::table('albums', function (Blueprint $table) {
            $table->string('external_id')->nullable()->after('cover_image_url');
            $table->string('external_link')->nullable()->after('external_id');
            $table->string('genre')->nullable()->after('external_link');
            $table->integer('total_duration')->default(0)->after('genre');
            $table->integer('track_count')->default(0)->after('total_duration');
            
            $table->index('external_id');
            $table->index('genre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('albums', function (Blueprint $table) {
            $table->dropIndex(['external_id', 'genre']);
            $table->dropColumn(['external_id', 'external_link', 'genre', 'total_duration', 'track_count']);
        });
    }
};
