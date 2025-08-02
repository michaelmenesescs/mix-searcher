<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Album;
use App\Models\Artist;

class AlbumSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create artists
        $artists = [
            'DJ Shadow' => [
                ['title' => 'Endtroducing.....', 'release_year' => 1996, 'cover_image_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9f/DJ_Shadow_-_Endtroducing.....jpg'],
                ['title' => 'The Private Press', 'release_year' => 2002, 'cover_image_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8a/DJ_Shadow_-_The_Private_Press.jpg'],
                ['title' => 'The Outsider', 'release_year' => 2006, 'cover_image_url' => 'https://upload.wikimedia.org/wikipedia/en/4/4d/DJ_Shadow_-_The_Outsider.jpg'],
            ],
            'Massive Attack' => [
                ['title' => 'Blue Lines', 'release_year' => 1991, 'cover_image_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8a/Massive_Attack_-_Blue_Lines.jpg'],
                ['title' => 'Protection', 'release_year' => 1994, 'cover_image_url' => 'https://upload.wikimedia.org/wikipedia/en/4/4a/Massive_Attack_-_Protection.jpg'],
                ['title' => 'Mezzanine', 'release_year' => 1998, 'cover_image_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8a/Massive_Attack_-_Mezzanine.jpg'],
                ['title' => '100th Window', 'release_year' => 2003, 'cover_image_url' => 'https://upload.wikimedia.org/wikipedia/en/4/4a/Massive_Attack_-_100th_Window.jpg'],
            ],
            'Aphex Twin' => [
                ['title' => 'Selected Ambient Works 85-92', 'release_year' => 1992, 'cover_image_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8a/Aphex_Twin_-_Selected_Ambient_Works_85-92.jpg'],
                ['title' => 'Selected Ambient Works Volume II', 'release_year' => 1994, 'cover_image_url' => 'https://upload.wikimedia.org/wikipedia/en/4/4a/Aphex_Twin_-_Selected_Ambient_Works_Volume_II.jpg'],
                ['title' => 'Richard D. James Album', 'release_year' => 1996, 'cover_image_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8a/Aphex_Twin_-_Richard_D._James_Album.jpg'],
                ['title' => 'Drukqs', 'release_year' => 2001, 'cover_image_url' => 'https://upload.wikimedia.org/wikipedia/en/4/4a/Aphex_Twin_-_Drukqs.jpg'],
            ],
            'Portishead' => [
                ['title' => 'Dummy', 'release_year' => 1994, 'cover_image_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8a/Portishead_-_Dummy.jpg'],
                ['title' => 'Portishead', 'release_year' => 1997, 'cover_image_url' => 'https://upload.wikimedia.org/wikipedia/en/4/4a/Portishead_-_Portishead.jpg'],
                ['title' => 'Third', 'release_year' => 2008, 'cover_image_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8a/Portishead_-_Third.jpg'],
            ],
            'The Chemical Brothers' => [
                ['title' => 'Exit Planet Dust', 'release_year' => 1995, 'cover_image_url' => 'https://upload.wikimedia.org/wikipedia/en/4/4a/The_Chemical_Brothers_-_Exit_Planet_Dust.jpg'],
                ['title' => 'Dig Your Own Hole', 'release_year' => 1997, 'cover_image_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8a/The_Chemical_Brothers_-_Dig_Your_Own_Hole.jpg'],
                ['title' => 'Surrender', 'release_year' => 1999, 'cover_image_url' => 'https://upload.wikimedia.org/wikipedia/en/4/4a/The_Chemical_Brothers_-_Surrender.jpg'],
            ],
            'Underworld' => [
                ['title' => 'Dubnobasswithmyheadman', 'release_year' => 1994, 'cover_image_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8a/Underworld_-_Dubnobasswithmyheadman.jpg'],
                ['title' => 'Second Toughest in the Infants', 'release_year' => 1996, 'cover_image_url' => 'https://upload.wikimedia.org/wikipedia/en/4/4a/Underworld_-_Second_Toughest_in_the_Infants.jpg'],
                ['title' => 'Beaucoup Fish', 'release_year' => 1999, 'cover_image_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8a/Underworld_-_Beaucoup_Fish.jpg'],
            ],
            'Orbital' => [
                ['title' => 'Orbital', 'release_year' => 1991, 'cover_image_url' => 'https://upload.wikimedia.org/wikipedia/en/4/4a/Orbital_-_Orbital.jpg'],
                ['title' => 'Orbital 2', 'release_year' => 1993, 'cover_image_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8a/Orbital_-_Orbital_2.jpg'],
                ['title' => 'Snivilisation', 'release_year' => 1994, 'cover_image_url' => 'https://upload.wikimedia.org/wikipedia/en/4/4a/Orbital_-_Snivilisation.jpg'],
            ],
            'The Prodigy' => [
                ['title' => 'Experience', 'release_year' => 1992, 'cover_image_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8a/The_Prodigy_-_Experience.jpg'],
                ['title' => 'Music for the Jilted Generation', 'release_year' => 1994, 'cover_image_url' => 'https://upload.wikimedia.org/wikipedia/en/4/4a/The_Prodigy_-_Music_for_the_Jilted_Generation.jpg'],
                ['title' => 'The Fat of the Land', 'release_year' => 1997, 'cover_image_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8a/The_Prodigy_-_The_Fat_of_the_Land.jpg'],
            ],
        ];

        foreach ($artists as $artistName => $albums) {
            $artist = Artist::where('name', $artistName)->first();
            
            if (!$artist) {
                $artist = Artist::create(['name' => $artistName]);
            }

            foreach ($albums as $albumData) {
                Album::create([
                    'artist_id' => $artist->id,
                    'title' => $albumData['title'],
                    'release_year' => $albumData['release_year'],
                    'cover_image_url' => $albumData['cover_image_url'],
                ]);
            }
        }
    }
}