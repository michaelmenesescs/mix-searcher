<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Song;
use App\Models\Artist;
use App\Models\Album;

class SongSeeder extends Seeder
{
    public function run(): void
    {
        $endtroducing = Album::where('title', 'Endtroducing.....')->first();
        $mezzanine = Album::where('title', 'Mezzanine')->first();

        if ($endtroducing) {
            Song::create([
                'album_id' => $endtroducing->id,
                'artist_id' => $endtroducing->artist_id,
                'title' => 'Building Steam With A Grain Of Salt',
                'duration' => 399, // 6:39
                'genre' => 'Instrumental Hip Hop'
            ]);
            Song::create([
                'album_id' => $endtroducing->id,
                'artist_id' => $endtroducing->artist_id,
                'title' => 'Midnight In A Perfect World',
                'duration' => 301, // 5:01
                'genre' => 'Trip Hop'
            ]);
        }

        if ($mezzanine) {
            Song::create([
                'album_id' => $mezzanine->id,
                'artist_id' => $mezzanine->artist_id,
                'title' => 'Angel',
                'duration' => 379, // 6:19
                'genre' => 'Trip Hop'
            ]);
            Song::create([
                'album_id' => $mezzanine->id,
                'artist_id' => $mezzanine->artist_id,
                'title' => 'Teardrop',
                'duration' => 330, // 5:30
                'genre' => 'Trip Hop'
            ]);
        }
    }
}