<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    //
    protected $fillable = [
        'title',
        'artist',
        'album',
        'duration',
        'genre',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'duration' => 'integer',
        'genre' => 'string',
        'artist' => 'string',
        'album' => 'string',
        'title' => 'string',
    ];

    protected $table = 'songs'; 

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    public function _construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->attributes['artist_id'] = $this->artist_id;
        $this->attributes['album_id'] = $this->album_id;
        $this->attributes['duration'] = $this->duration;
        $this->attributes['genre'] = $this->genre;
        $this->attributes['title'] = $this->title;
    }
}

