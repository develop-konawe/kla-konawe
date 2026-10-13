<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FacilityController extends Controller
{
    public function index(Request $request)
    {
        $query = Facility::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $facilities = $query->orderBy('name', 'asc')->paginate(12)->withQueryString();

        return Inertia::render('Facilities/Index', [
            'facilities' => $facilities,
            'search'     => $request->search ?? '',
        ]);
    }

    public function show($slug)
    {
        $facility = Facility::where('slug', $slug)->firstOrFail();

        return Inertia::render('Facilities/Show', [
            'facility' => $facility,
        ]);
    }
}
