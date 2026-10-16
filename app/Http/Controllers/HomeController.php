<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Post;
use App\Models\Service;
use App\Models\Statistic;
use App\Services\BeritaService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(BeritaService $beritaService)
    {
        // Get latest news from Berita API with graceful fallback to local database
        $latestNews = $beritaService->getLatest(3);

        if (empty($latestNews)) {
            $latestNews = Post::where('status', 'published')
                ->orderBy('published_at', 'desc')
                ->take(3)
                ->get()
                ->map(fn ($post) => [
                    'id'             => $post->id,
                    'title'          => $post->title,
                    'slug'           => $post->slug,
                    'excerpt'        => $post->excerpt,
                    'featured_image' => $post->featured_image,
                    'published_at'   => $post->published_at?->toISOString(),
                ])
                ->toArray();
        }

        // Get upcoming events from Berita API
        $upcomingEvents = collect($beritaService->getAgenda(['per_page' => 3]))
            ->take(3)
            ->toArray();

        // Quick stats
        $stats = [
            'services'   => Service::where('status', 'published')->count(),
            'facilities' => Facility::where('is_child_friendly', true)->count(),
        ];

        // Published KLA Indicators / Statistics
        $indicators = Statistic::where('status', 'published')
            ->orderBy('year', 'desc')
            ->orderBy('category_id', 'asc')
            ->take(12)
            ->get();

        return Inertia::render('Home', [
            'latestNews'     => $latestNews,
            'upcomingEvents' => $upcomingEvents,
            'stats'          => $stats,
            'indicators'     => $indicators,
        ]);
    }
}
