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
        Schema::table('artists', function (Blueprint $table) {
            $table->string('external_id')->nullable()->after('name');
            $table->string('external_link')->nullable()->after('external_id');
            $table->text('bio')->nullable()->after('external_link');
            $table->string('image_url')->nullable()->after('bio');
            
            $table->index('external_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artists', function (Blueprint $table) {
            $table->dropIndex(['external_id']);
            $table->dropColumn(['external_id', 'external_link', 'bio', 'image_url']);
        });
    }
};
