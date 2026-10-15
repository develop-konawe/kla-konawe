<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FacilityController extends Controller
{
    public function index(Request $request)
    {
        $query = Facility::with('organization');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
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
        $facility = Facility::with('organization')->where('slug', $slug)->firstOrFail();

        $relatedFacilities = Facility::where('id', '!=', $facility->id)
            ->when($facility->category, function ($query, $cat) {
                $query->where('category', $cat);
            })
            ->orderBy('name', 'asc')
            ->take(3)
            ->get();

        if ($relatedFacilities->count() < 3) {
            $excludeIds = $relatedFacilities->pluck('id')->push($facility->id);
            $moreFacilities = Facility::whereNotIn('id', $excludeIds)
                ->take(3 - $relatedFacilities->count())
                ->get();
            $relatedFacilities = $relatedFacilities->concat($moreFacilities);
        }

        return Inertia::render('Facilities/Show', [
            'facility' => $facility,
            'relatedFacilities' => $relatedFacilities,
        ]);
    }
}
