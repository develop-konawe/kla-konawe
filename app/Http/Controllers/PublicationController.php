<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicationController extends Controller
{
    public function index()
    {
        $publications = Publication::where('status', 'published')
                                   ->where('classification', 'public')
                                   ->orderBy('published_at', 'desc')
                                   ->paginate(12);

        return Inertia::render('Publications/Index', [
            'publications' => $publications,
        ]);
    }
}
