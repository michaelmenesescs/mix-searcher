<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', // Renamed from 'name' for consistency with songs
        'artist_id',
        'release_year', // Optional: good to have
        'cover_image_url', // Optional
    ];

    protected $casts = [
        'release_year' => 'integer',
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    public function songs()
    {
        return $this->hasMany(Song::class);
    }
}