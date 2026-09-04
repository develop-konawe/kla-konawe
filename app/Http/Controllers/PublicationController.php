<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use Illuminate\Http\Request;

class PublicationController extends Controller
{
    public function index()
    {
        // Only show public classification
        $publications = Publication::where('status', 'published')
                                   ->where('classification', 'public')
                                   ->orderBy('published_at', 'desc')
                                   ->paginate(12);

        return view('publications.index', compact('publications'));
    }
}
