<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::where('status', 'published');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        $services = $query->orderBy('name', 'asc')->paginate(12)->withQueryString();

        return Inertia::render('Services/Index', [
            'services' => $services,
            'search'   => $request->search ?? '',
        ]);
    }

    public function show($slug)
    {
        $service = Service::where('slug', $slug)
                          ->where('status', 'published')
                          ->firstOrFail();

        return Inertia::render('Services/Show', [
            'service' => $service,
        ]);
    }
}
