<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Artist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'external_id',
        'external_link',
        'bio',
        'image_url',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all songs by this artist
     */
    public function songs(): HasMany
    {
        return $this->hasMany(Song::class);
    }

    /**
     * Get all albums by this artist
     */
    public function albums(): HasMany
    {
        return $this->hasMany(Album::class);
    }

    /**
     * Get all tracks by this artist
     */
    public function tracks(): HasMany
    {
        return $this->hasMany(Track::class, 'artist', 'name');
    }

    /**
     * Get the total number of songs by this artist
     */
    public function getSongCountAttribute(): int
    {
        return $this->songs()->count();
    }

    /**
     * Get the total number of albums by this artist
     */
    public function getAlbumCountAttribute(): int
    {
        return $this->albums()->count();
    }

    /**
     * Get the total duration of all songs by this artist
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
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        
        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $totalSeconds % 60);
        }
        
        return sprintf('%d:%02d', $minutes, $totalSeconds % 60);
    }

    /**
     * Scope to search artists by name
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    /**
     * Scope to get artists with most songs
     */
    public function scopeMostPopular($query, $limit = 10)
    {
        return $query->withCount('songs')
                    ->orderBy('songs_count', 'desc')
                    ->limit($limit);
    }
}