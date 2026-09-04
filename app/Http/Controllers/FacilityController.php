<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function index(Request $request)
    {
        $query = Facility::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $facilities = $query->orderBy('name', 'asc')->paginate(12);

        return view('facilities.index', compact('facilities'));
    }

    public function show($slug)
    {
        $facility = Facility::where('slug', $slug)->firstOrFail();

        return view('facilities.show', compact('facility'));
    }
}
