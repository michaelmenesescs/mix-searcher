<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Song extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'artist_id',
        'album_id',
        'duration',
        'genre',
        'external_id',
        'external_link',
        'isrc',
        'track_number',
        'disc_number',
        'lyrics',
        'preview_url',
    ];

    protected $casts = [
        'duration' => 'integer',
        'track_number' => 'integer',
        'disc_number' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the artist that owns this song
     */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    /**
     * Get the album that contains this song
     */
    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration) {
            return '0:00';
        }
        
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;
        
        return sprintf('%d:%02d', $minutes, $seconds);
    }

    /**
     * Get the track number with disc number if applicable
     */
    public function getFullTrackNumberAttribute(): string
    {
        if ($this->disc_number && $this->disc_number > 1) {
            return sprintf('%d-%02d', $this->disc_number, $this->track_number);
        }
        
        return (string) $this->track_number;
    }

    /**
     * Scope to search songs by title
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('title', 'like', "%{$search}%");
    }

    /**
     * Scope to filter by genre
     */
    public function scopeByGenre($query, $genre)
    {
        return $query->where('genre', $genre);
    }

    /**
     * Scope to filter by artist
     */
    public function scopeByArtist($query, $artistId)
    {
        return $query->where('artist_id', $artistId);
    }

    /**
     * Scope to filter by album
     */
    public function scopeByAlbum($query, $albumId)
    {
        return $query->where('album_id', $albumId);
    }

    /**
     * Scope to get songs longer than specified duration
     */
    public function scopeLongerThan($query, $seconds)
    {
        return $query->where('duration', '>', $seconds);
    }

    /**
     * Scope to get songs shorter than specified duration
     */
    public function scopeShorterThan($query, $seconds)
    {
        return $query->where('duration', '<', $seconds);
    }

    /**
     * Scope to get songs by duration range
     */
    public function scopeByDurationRange($query, $minSeconds, $maxSeconds)
    {
        return $query->whereBetween('duration', [$minSeconds, $maxSeconds]);
    }

    /**
     * Scope to get recent songs
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}