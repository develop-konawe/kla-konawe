<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\BeritaService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index(Request $request, BeritaService $beritaService)
    {
        $page = (int) $request->input('page', 1);
        $perPage = 9;
        $search = $request->input('search');

        // Fetch from Berita API as LengthAwarePaginator
        $posts = $beritaService->getPaginatedBerita($page, $perPage, $search);

        // Fallback to local posts if API is unavailable
        if (! $posts) {
            $query = Post::where('status', 'published');
            if ($search) {
                $query->where('title', 'like', "%{$search}%");
            }
            $posts = $query->orderBy('published_at', 'desc')->paginate($perPage);
        }

        return Inertia::render('Posts/Index', [
            'posts'   => $posts,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function show($slug, BeritaService $beritaService)
    {
        // 1. Check local database
        $post = Post::with(['category', 'author.organization'])
                    ->where('slug', $slug)
                    ->where('status', 'published')
                    ->first();

        if ($post) {
            $relatedPosts = Post::with(['category'])
                ->where('id', '!=', $post->id)
                ->where('status', 'published')
                ->when($post->category_id, fn ($q) => $q->where('category_id', $post->category_id))
                ->orderBy('published_at', 'desc')
                ->take(3)
                ->get();

            if ($relatedPosts->count() < 3) {
                $excludeIds = $relatedPosts->pluck('id')->push($post->id);
                $morePosts = Post::with(['category'])
                    ->where('status', 'published')
                    ->whereNotIn('id', $excludeIds)
                    ->orderBy('published_at', 'desc')
                    ->take(3 - $relatedPosts->count())
                    ->get();
                $relatedPosts = $relatedPosts->concat($morePosts);
            }

            return Inertia::render('Posts/Show', [
                'post'         => $post,
                'relatedPosts' => $relatedPosts,
            ]);
        }

        // 2. Check Berita API
        $apiItem = $beritaService->findBeritaBySlug($slug);

        if ($apiItem) {
            if (! empty($apiItem['url'])) {
                return redirect()->away($apiItem['url']);
            }

            $relatedPosts = Post::with(['category'])
                ->where('status', 'published')
                ->orderBy('published_at', 'desc')
                ->take(3)
                ->get();

            return Inertia::render('Posts/Show', [
                'post'         => $apiItem,
                'relatedPosts' => $relatedPosts,
            ]);
        }

        abort(404);
    }
}
