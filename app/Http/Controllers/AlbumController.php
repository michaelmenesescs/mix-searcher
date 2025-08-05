<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Album;

class AlbumController extends Controller
{
    /**
     * Display a listing of albums.
     */
    public function index()
    {
        // Get all albums with their associated artist data
        $albums = Album::with('artist')->get();
        
        // Return the data to the frontend
        return inertia('Albums/Index', [
            'albums' => $albums
        ]);
    }
}
