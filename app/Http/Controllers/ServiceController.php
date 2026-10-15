<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::with('organization')->where('status', 'published');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('contact_info', 'like', "%{$search}%");
            });
        }

        $services = $query->orderBy('name', 'asc')->paginate(12)->withQueryString();

        return Inertia::render('Services/Index', [
            'services' => $services,
            'search'   => $request->search ?? '',
        ]);
    }

    public function show($slug)
    {
        $service = Service::with('organization')
                          ->where('slug', $slug)
                          ->where('status', 'published')
                          ->firstOrFail();

        $relatedServices = Service::where('id', '!=', $service->id)
            ->where('status', 'published')
            ->when($service->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->take(3)
            ->get();

        if ($relatedServices->count() < 3) {
            $excludeIds = $relatedServices->pluck('id')->push($service->id);
            $moreServices = Service::where('status', 'published')
                ->whereNotIn('id', $excludeIds)
                ->take(3 - $relatedServices->count())
                ->get();
            $relatedServices = $relatedServices->concat($moreServices);
        }

        return Inertia::render('Services/Show', [
            'service' => $service,
            'relatedServices' => $relatedServices,
        ]);
    }
}
