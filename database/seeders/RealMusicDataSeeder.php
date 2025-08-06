<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Artist;
use App\Models\Album;
use App\Models\Song;
use App\Models\Track;
use App\Models\Playlist;
use App\Models\PlaylistTrack;
use App\Models\User;
use Illuminate\Support\Facades\File;

class RealMusicDataSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        PlaylistTrack::truncate();
        Playlist::truncate();
        Track::truncate();
        Song::truncate();
        Album::truncate();
        Artist::truncate();

        // Import albums data
        $this->importAlbumsData();
        
        // Import playlist tracks data
        $this->importPlaylistData();
        
        // Create sample playlist
        $this->createSamplePlaylist();
    }

    private function importAlbumsData()
    {
        $albumsJson = File::get(base_path('albums1754441640.json'));
        $albumsData = json_decode($albumsJson, true);

        foreach ($albumsData as $albumData) {
            // Create or find artist
            $artist = Artist::firstOrCreate(
                ['name' => $albumData['artist']],
                ['name' => $albumData['artist']]
            );

            // Create album
            $album = Album::create([
                'title' => $albumData['title'],
                'artist_id' => $artist->id,
                'release_year' => $this->extractYearFromTimestamp($albumData['releaseDate']),
                'cover_image_url' => $albumData['picture'],
            ]);

            // Create a track record for this album
            Track::create([
                'platform' => $albumData['platform'],
                'type' => $albumData['type'],
                'external_id' => $albumData['id'],
                'title' => $albumData['title'],
                'artist' => $albumData['artist'],
                'artist_external_id' => $this->extractArtistIdFromLink($albumData['artistLink']),
                'artist_link' => $albumData['artistLink'],
                'album' => $albumData['title'],
                'album_external_id' => $albumData['id'],
                'album_link' => $albumData['albumLink'],
                'duration' => $albumData['duration'],
                'track_link' => $albumData['albumLink'],
                'picture' => $albumData['picture'],
                'position' => 0,
                'share_urls' => $albumData['shareUrls'] ?? [],
                'metadata' => [
                    'upc' => $albumData['upc'],
                    'nbTracks' => $albumData['nbTracks'],
                    'releaseDate' => $albumData['releaseDate'],
                ],
            ]);
        }
    }

    private function importPlaylistData()
    {
        $playlistJson = File::get(base_path('space_travels_playlist.json'));
        $playlistData = json_decode($playlistJson, true);

        foreach ($playlistData as $trackData) {
            // Create or find artist
            $artist = Artist::firstOrCreate(
                ['name' => $trackData['artist']],
                ['name' => $trackData['artist']]
            );

            // Create or find album
            $album = Album::firstOrCreate(
                [
                    'title' => $trackData['album'],
                    'artist_id' => $artist->id,
                ],
                [
                    'title' => $trackData['album'],
                    'artist_id' => $artist->id,
                    'cover_image_url' => $trackData['picture'],
                ]
            );

            // Create song
            $song = Song::create([
                'title' => $trackData['title'],
                'artist_id' => $artist->id,
                'album_id' => $album->id,
                'duration' => $trackData['duration'],
                'genre' => 'Electronic', // Default genre for this playlist
                'external_id' => $trackData['id'],
            ]);

            // Create track record
            Track::create([
                'platform' => $trackData['platform'],
                'type' => $trackData['type'],
                'external_id' => $trackData['id'],
                'title' => $trackData['title'],
                'artist' => $trackData['artist'],
                'artist_external_id' => $this->extractArtistIdFromLink($trackData['artistLink']),
                'artist_link' => $trackData['artistLink'],
                'album' => $trackData['album'],
                'album_external_id' => $this->extractAlbumIdFromLink($trackData['albumLink']),
                'album_link' => $trackData['albumLink'],
                'isrc' => $trackData['isrc'],
                'duration' => $trackData['duration'],
                'track_link' => $trackData['trackLink'],
                'picture' => $trackData['picture'],
                'position' => $trackData['position'],
                'share_urls' => $trackData['shareUrls'] ?? [],
                'metadata' => [
                    'isrc' => $trackData['isrc'],
                ],
            ]);
        }
    }

    private function createSamplePlaylist()
    {
        $user = User::where('email', 'test@example.com')->first();
        
        if (!$user) {
            return;
        }

        // Create a sample playlist
        $playlist = Playlist::create([
            'name' => 'Space Travels',
            'description' => 'A curated collection of electronic and techno tracks for space exploration',
            'user_id' => $user->id,
            'is_public' => true,
            'genre' => 'Electronic',
            'tags' => ['electronic', 'techno', 'space', 'ambient'],
        ]);

        // Get tracks from the playlist data and add them to the playlist
        $tracks = Track::where('type', 'track')
                      ->orderBy('position')
                      ->limit(20) // Limit to first 20 tracks
                      ->get();

        $totalDuration = 0;
        $trackCount = 0;

        foreach ($tracks as $index => $track) {
            PlaylistTrack::create([
                'playlist_id' => $playlist->id,
                'track_id' => $track->id,
                'position' => $index + 1,
                'added_at' => now(),
                'added_by' => $user->id,
            ]);

            $totalDuration += $track->duration;
            $trackCount++;
        }

        // Update playlist with totals
        $playlist->update([
            'total_duration' => $totalDuration,
            'track_count' => $trackCount,
        ]);
    }

    private function extractYearFromTimestamp($timestamp)
    {
        return date('Y', $timestamp);
    }

    private function extractArtistIdFromLink($link)
    {
        if (preg_match('/artist\/(\d+)/', $link, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function extractAlbumIdFromLink($link)
    {
        if (preg_match('/album\/(\d+)/', $link, $matches)) {
            return $matches[1];
        }
        return null;
    }
} 