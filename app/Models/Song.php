<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Recommended to add for seeding/testing
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory; // Add this trait

    protected $fillable = [
        'title',
        'artist_id', // Foreign key for Artist
        'album_id',  // Foreign key for Album
        'duration',  // Assuming this is in seconds (integer)
        'genre',
        'external_id',
        'external_'
    ];

    // Hidden attributes are fine if you don't want to expose timestamps often
    protected $hidden = [
        // 'created_at', // You might want these for display sometimes
        // 'updated_at',
    ];

    protected $casts = [
        'duration' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Eloquent infers 'songs' if your model is Song. Explicitly setting is fine too.
    // protected $table = 'songs'; 

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    public function album()
    {
        return $this->belongsTo(Album::class);
    }
}