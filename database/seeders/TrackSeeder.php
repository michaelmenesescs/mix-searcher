<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Track;

class TrackSeeder extends Seeder
{
    public function run(): void
    {
        // Read the JSON file
        $jsonPath = storage_path('app/spotify_tracks.json');
        
        if (!file_exists($jsonPath)) {
            $this->command->warn('Spotify tracks JSON file not found. Creating sample data instead.');
            $this->createSampleTracks();
            return;
        }

        $tracksData = json_decode(file_get_contents($jsonPath), true);

        foreach ($tracksData as $trackData) {
            Track::create([
                'platform' => $trackData['platform'] ?? 'spotify',
                'type' => $trackData['type'] ?? 'track',
                'external_id' => $trackData['id'],
                'title' => $trackData['title'],
                'artist' => $trackData['artist'],
                'artist_external_id' => $this->extractArtistId($trackData['artistLink'] ?? ''),
                'artist_link' => $trackData['artistLink'] ?? null,
                'album' => $trackData['album'],
                'album_external_id' => $this->extractAlbumId($trackData['albumLink'] ?? ''),
                'album_link' => $trackData['albumLink'] ?? null,
                'isrc' => $trackData['isrc'] ?? null,
                'duration' => $trackData['duration'] ?? null,
                'track_link' => $trackData['trackLink'] ?? null,
                'preview_url' => $trackData['preview'] ?? null,
                'picture' => $trackData['picture'] ?? null,
                'added_date' => $trackData['addedDate'] ?? null,
                'added_by' => $trackData['addedBy'] ?? null,
                'position' => $trackData['position'] ?? null,
                'share_urls' => $trackData['shareUrls'] ?? [],
                'metadata' => [
                    'platform_specific' => $trackData
                ],
            ]);
        }
    }

    private function extractArtistId($artistLink)
    {
        if (empty($artistLink)) return null;
        
        // Extract ID from Spotify artist URL
        if (preg_match('/\/artist\/([a-zA-Z0-9]+)/', $artistLink, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    private function extractAlbumId($albumLink)
    {
        if (empty($albumLink)) return null;
        
        // Extract ID from Spotify album URL
        if (preg_match('/\/album\/([a-zA-Z0-9]+)/', $albumLink, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    private function createSampleTracks()
    {
        $sampleTracks = [
            [
                'platform' => 'spotify',
                'type' => 'track',
                'external_id' => '52yAJNEvLjD5wRMnSDa3na',
                'title' => 'I Can\'t Lose (feat. Keyone Starr) - Pomo Remix',
                'artist' => 'Mark Ronson, Keyone Starr, Pomo',
                'album' => 'I Can\'t Lose (Remixes) - EP (feat. Keyone Starr)',
                'duration' => 217,
                'picture' => 'https://i.scdn.co/image/ab67616d00001e02b6c4d6f4e8e4c4d1dfcf4c4e',
                'added_date' => 1596419814,
            ],
            [
                'platform' => 'spotify',
                'type' => 'track',
                'external_id' => '7aQeWViSfRWSEwtJD86Eq0',
                'title' => 'Late Night Feelings (feat. Lykke Li)',
                'artist' => 'Mark Ronson, Lykke Li',
                'album' => 'Late Night Feelings (feat. Lykke Li)',
                'duration' => 251,
                'picture' => 'https://i.scdn.co/image/ab67616d00001e02d10949cdaf3a0b91818e0d11',
                'added_date' => 1596419872,
            ],
            [
                'platform' => 'spotify',
                'type' => 'track',
                'external_id' => '2ljvO8ZpKFMT3HXwCjW4Yw',
                'title' => 'anthems',
                'artist' => 'Charli xcx',
                'album' => 'how i\'m feeling now',
                'duration' => 172,
                'picture' => 'https://i.scdn.co/image/ab67616d00001e0249bdbd5880802dcbe4e0b2dd',
                'added_date' => 1596419891,
            ],
        ];

        foreach ($sampleTracks as $trackData) {
            Track::create($trackData);
        }
    }
} 