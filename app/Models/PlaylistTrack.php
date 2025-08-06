<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlaylistTrack extends Model
{
    use HasFactory;

    protected $table = 'playlist_tracks';

    protected $fillable = [
        'playlist_id',
        'track_id',
        'position',
        'added_at',
        'added_by',
    ];

    protected $casts = [
        'position' => 'integer',
        'added_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the playlist that contains this track
     */
    public function playlist(): BelongsTo
    {
        return $this->belongsTo(Playlist::class);
    }

    /**
     * Get the track in this playlist
     */
    public function track(): BelongsTo
    {
        return $this->belongsTo(Track::class);
    }

    /**
     * Get the user who added this track
     */
    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * Scope to order by position
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }

    /**
     * Scope to get tracks added by a specific user
     */
    public function scopeAddedBy($query, $userId)
    {
        return $query->where('added_by', $userId);
    }

    /**
     * Scope to get tracks added within a date range
     */
    public function scopeAddedBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('added_at', [$startDate, $endDate]);
    }
} 