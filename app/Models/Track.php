<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform',
        'type',
        'external_id',
        'title',
        'artist',
        'artist_external_id',
        'artist_link',
        'album',
        'album_external_id',
        'album_link',
        'isrc',
        'duration',
        'track_link',
        'preview_url',
        'picture',
        'added_date',
        'added_by',
        'position',
        'share_urls',
        'metadata',
    ];

    protected $casts = [
        'duration' => 'integer',
        'added_date' => 'integer',
        'share_urls' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the formatted duration in MM:SS format
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration) {
            return null;
        }
        
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;
        
        return sprintf('%d:%02d', $minutes, $seconds);
    }

    /**
     * Get the added date as a Carbon instance
     */
    public function getAddedDateAttribute($value)
    {
        return $value ? \Carbon\Carbon::createFromTimestamp($value) : null;
    }

    /**
     * Scope to filter by platform
     */
    public function scopeByPlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Scope to filter by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get recent tracks
     */
    public function scopeRecent($query, $days = 30)
    {
        $timestamp = now()->subDays($days)->timestamp;
        return $query->where('added_date', '>=', $timestamp);
    }
} 