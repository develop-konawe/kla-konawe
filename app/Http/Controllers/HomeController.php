<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Http;
use App\Models\Service;
use App\Models\Facility;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get latest published news
        $latestNews = Post::where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();
            
        // Get upcoming events from API
        $upcomingEvents = collect();
        try {
            $response = Http::get(env('BERITA_BASE_URL') . '/agenda', [
                'api_key' => env('BERITA_API_KEY')
            ]);
            
            if ($response->successful()) {
                $upcomingEvents = collect($response->json('data'))->take(3);
            }
        } catch (\Exception $e) {
            // Silently fail or log error
        }
            
        // Get quick stats
        $stats = [
            'services' => Service::where('status', 'published')->count(),
            'facilities' => Facility::where('is_child_friendly', true)->count(),
        ];

        return view('home', compact('latestNews', 'upcomingEvents', 'stats'));
    }
}
