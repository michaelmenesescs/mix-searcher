<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DJSet extends Model
{
    //
    protected $fillable = [
        'name',
        'description',
        'tracks',
        'image_url',
        'external_id',
        'external_link',
        'created_at',
        'updated_at',
    ];
    
}
