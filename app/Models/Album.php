<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Album extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'artist_id',
        'release_year',
        'cover_image_url',
        'external_id',
        'external_link',
        'genre',
        'total_duration',
        'track_count',
    ];

    protected $casts = [
        'release_year' => 'integer',
        'total_duration' => 'integer',
        'track_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the artist that owns this album
     */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    /**
     * Get all songs on this album
     */
    public function songs(): HasMany
    {
        return $this->hasMany(Song::class);
    }

    /**
     * Get all tracks for this album
     */
    public function tracks(): HasMany
    {
        return $this->hasMany(Track::class, 'album', 'title');
    }

    /**
     * Get the total number of songs on this album
     */
    public function getSongCountAttribute(): int
    {
        return $this->songs()->count();
    }

    /**
     * Get the total duration of all songs on this album
     */
    public function getTotalDurationAttribute(): int
    {
        return $this->songs()->sum('duration');
    }

    /**
     * Get formatted total duration
     */
    public function getFormattedTotalDurationAttribute(): string
    {
        $totalSeconds = $this->total_duration;
        $minutes = floor($totalSeconds / 60);
        $seconds = $totalSeconds % 60;
        
        return sprintf('%d:%02d', $minutes, $seconds);
    }

    /**
     * Get the average duration of songs on this album
     */
    public function getAverageDurationAttribute(): float
    {
        $songCount = $this->song_count;
        return $songCount > 0 ? $this->total_duration / $songCount : 0;
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
     * Scope to search albums by title
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('title', 'like', "%{$search}%");
    }

    /**
     * Scope to filter by release year
     */
    public function scopeByYear($query, $year)
    {
        return $query->where('release_year', $year);
    }

    /**
     * Scope to filter by artist
     */
    public function scopeByArtist($query, $artistId)
    {
        return $query->where('artist_id', $artistId);
    }

    /**
     * Scope to get albums with most songs
     */
    public function scopeMostTracks($query, $limit = 10)
    {
        return $query->withCount('songs')
                    ->orderBy('songs_count', 'desc')
                    ->limit($limit);
    }

    /**
     * Scope to get recent albums
     */
    public function scopeRecent($query, $years = 5)
    {
        $cutoffYear = now()->year - $years;
        return $query->where('release_year', '>=', $cutoffYear);
    }
}