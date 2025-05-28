<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Album;
use App\Models\Artist;

class AlbumSeeder extends Seeder
{
    public function run(): void
    {
        $djShadow = Artist::where('name', 'DJ Shadow')->first();
        $massiveAttack = Artist::where('name', 'Massive Attack')->first();

        if ($djShadow) {
            Album::create(['artist_id' => $djShadow->id, 'title' => 'Endtroducing.....', 'release_year' => 1996]);
            Album::create(['artist_id' => $djShadow->id, 'title' => 'The Private Press', 'release_year' => 2002]);
        }

        if ($massiveAttack) {
            Album::create(['artist_id' => $massiveAttack->id, 'title' => 'Mezzanine', 'release_year' => 1998]);
        }
    }
}