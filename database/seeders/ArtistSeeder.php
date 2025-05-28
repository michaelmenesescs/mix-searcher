<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Artist;

class ArtistSeeder extends Seeder
{
    public function run(): void
    {
        Artist::create(['name' => 'DJ Shadow']);
        Artist::create(['name' => 'Massive Attack']);
        Artist::create(['name' => 'Aphex Twin']);
    }
}