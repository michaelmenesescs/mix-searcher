<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Artist;

class ArtistSeeder extends Seeder
{
    public function run(): void
    {
        $artists = [
            'DJ Shadow',
            'Massive Attack',
            'Aphex Twin',
            'Portishead',
            'The Chemical Brothers',
            'Underworld',
            'Orbital',
            'The Prodigy',
            'Daft Punk',
            'Moby',
            'Fatboy Slim',
            'The Crystal Method',
            'Lamb',
            'Morcheeba',
            'Tricky',
            'Goldie',
            'LTJ Bukem',
            'Roni Size',
            'Squarepusher',
            'Autechre',
        ];

        foreach ($artists as $artistName) {
            Artist::firstOrCreate(['name' => $artistName]);
        }
    }
}