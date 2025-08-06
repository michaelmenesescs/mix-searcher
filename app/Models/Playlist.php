<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Playlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'user_id',
        'is_public',
        'cover_image_url',
        'external_id',
        'external_link',
        'platform',
        'total_duration',
        'track_count',
        'genre',
        'tags',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'total_duration' => 'integer',
        'track_count' => 'integer',
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns this playlist
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all tracks in this playlist
     */
    public function tracks(): HasMany
    {
        return $this->hasMany(PlaylistTrack::class)->orderBy('position');
    }

    /**
     * Get all songs in this playlist through tracks
     */
    public function songs()
    {
        return $this->belongsToMany(Song::class, 'playlist_tracks')
                    ->withPivot('position')
                    ->orderBy('playlist_tracks.position');
    }

    /**
     * Get formatted total duration
     */
    public function getFormattedTotalDurationAttribute(): string
    {
        $totalSeconds = $this->total_duration;
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        
        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $totalSeconds % 60);
        }
        
        return sprintf('%d:%02d', $minutes, $totalSeconds % 60);
    }

    /**
     * Get the average duration of tracks in this playlist
     */
    public function getAverageDurationAttribute(): float
    {
        $trackCount = $this->track_count;
        return $trackCount > 0 ? $this->total_duration / $trackCount : 0;
    }

    /**
     * Get formatted average duration
     */
    public function getFormattedAverageDurationAttribute(): string
    {
        $avgSeconds = round($this->average_duration);
        $minutes = floor($avgSeconds / 60);
        $seconds = $avgSeconds % 60;
        
        return sprintf('%d:%02d', $minutes, $seconds);
    }

    /**
     * Scope to search playlists by name
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
    }

    /**
     * Scope to filter by platform
     */
    public function scopeByPlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Scope to filter by genre
     */
    public function scopeByGenre($query, $genre)
    {
        return $query->where('genre', $genre);
    }

    /**
     * Scope to get public playlists
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope to get playlists with most tracks
     */
    public function scopeMostTracks($query, $limit = 10)
    {
        return $query->orderBy('track_count', 'desc')
                    ->limit($limit);
    }

    /**
     * Scope to get recent playlists
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
} 