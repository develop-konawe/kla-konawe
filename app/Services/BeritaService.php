<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class BeritaService
{
    protected string $baseUrl;
    protected ?string $apiKey;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.berita.base_url', 'https://berita.konawekab.go.id/api/v1'), '/');
        
        $apiKey = config('services.berita.api_key');
        if (empty($apiKey)) {
            $apiKey = '418595a9a453178dbdc3ea13af01789324f967e9cd60069e624de8b92003a613';
        }
        $this->apiKey = $apiKey;
        $this->timeout = max(10, (int) config('services.berita.timeout', 15));
    }

    /**
     * Get the configured base URL.
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get the configured API Key.
     */
    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    /**
     * Fetch news list from the Berita API with 10-minute caching.
     * Failed responses are NOT cached to allow immediate recovery.
     *
     * @param array $params Query parameters (page, per_page, search, category, etc.)
     * @return array{success: bool, data: array, meta: array}
     */
    public function getBerita(array $params = []): array
    {
        $cacheKey = 'berita_api_' . md5(serialize($params));

        // Return valid cached response if available
        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            if (is_array($cached) && ! empty($cached['success'])) {
                return $cached;
            }
            Cache::forget($cacheKey);
        }

        $page = (int) ($params['page'] ?? 1);
        $perPage = (int) ($params['per_page'] ?? 10);

        $query = array_filter([
            'api_key'  => $this->apiKey,
            'page'     => $page,
            'per_page' => $perPage,
            'search'   => $params['search'] ?? null,
            'category' => $params['category'] ?? null,
        ], static fn ($value) => filled($value));

        $defaultResult = [
            'success' => false,
            'data'    => [],
            'meta'    => [
                'total'        => 0,
                'per_page'     => $perPage,
                'current_page' => $page,
                'last_page'    => 1,
            ],
        ];

        try {
            $http = Http::timeout($this->timeout)
                ->connectTimeout(5)
                ->retry(2, 300)
                ->acceptJson();

            if (config('services.berita.verify_ssl', true) === false) {
                $http = $http->withoutVerifying();
            }

            $response = $http->get("{$this->baseUrl}/berita", $query);

            if (! $response->successful()) {
                Log::warning('Berita API call returned unsuccessful status', [
                    'url'    => "{$this->baseUrl}/berita",
                    'status' => $response->status(),
                    'body'   => Str::limit($response->body(), 200),
                ]);
                return $defaultResult;
            }

            $json = $response->json();
            $rawItems = $json['data'] ?? [];

            $formattedItems = collect($rawItems)
                ->filter(static fn ($item) => is_array($item))
                ->map(fn (array $item) => $this->formatItem($item))
                ->values()
                ->toArray();

            $meta = $json['meta'] ?? [];

            $result = [
                'success' => true,
                'data'    => $formattedItems,
                'meta'    => [
                    'total'        => (int) ($meta['total'] ?? count($formattedItems)),
                    'per_page'     => (int) ($meta['per_page'] ?? $perPage),
                    'current_page' => (int) ($meta['current_page'] ?? $page),
                    'last_page'    => (int) ($meta['last_page'] ?? 1),
                ],
            ];

            // Only cache successful responses
            Cache::put($cacheKey, $result, 600);

            return $result;
        } catch (Throwable $e) {
            Log::warning('Berita API request failed or timed out: ' . $e->getMessage(), [
                'url' => "{$this->baseUrl}/berita",
            ]);
            return $defaultResult;
        }
    }

    /**
     * Get paginated news as a Laravel LengthAwarePaginator.
     * Returns null if API call was unsuccessful.
     */
    public function getPaginatedBerita(int $page = 1, int $perPage = 9, ?string $search = null): ?LengthAwarePaginator
    {
        $result = $this->getBerita([
            'page'     => $page,
            'per_page' => $perPage,
            'search'   => $search,
        ]);

        if (! $result['success']) {
            return null;
        }

        $paginator = new LengthAwarePaginator(
            $result['data'],
            $result['meta']['total'],
            $result['meta']['per_page'],
            $result['meta']['current_page'],
            [
                'path'  => request()->url(),
                'query' => request()->query(),
            ]
        );

        return $paginator;
    }

    /**
     * Fetch latest news items (for homepage or widgets).
     *
     * @param int $limit
     * @return array
     */
    public function getLatest(int $limit = 3): array
    {
        $result = $this->getBerita([
            'page'     => 1,
            'per_page' => $limit,
        ]);

        return $result['data'];
    }

    /**
     * Find a news item from API by slug (e.g. for fallback details).
     */
    public function findBeritaBySlug(string $slug): ?array
    {
        // Try to search by slug keywords
        $searchQuery = str_replace('-', ' ', $slug);
        $result = $this->getBerita([
            'search'   => $searchQuery,
            'per_page' => 15,
        ]);

        foreach ($result['data'] as $item) {
            if (($item['slug'] ?? '') === $slug) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Fetch agenda events from the API.
     *
     * @param array $params
     * @return array
     */
    public function getAgenda(array $params = []): array
    {
        $cacheKey = 'agenda_api_' . md5(serialize($params));

        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            if (is_array($cached) && ! empty($cached)) {
                return $cached;
            }
            Cache::forget($cacheKey);
        }

        $query = array_filter([
            'api_key'  => $this->apiKey,
            'page'     => $params['page'] ?? 1,
            'per_page' => $params['per_page'] ?? 10,
        ], static fn ($value) => filled($value));

        try {
            $http = Http::timeout($this->timeout)
                ->connectTimeout(5)
                ->retry(2, 300)
                ->acceptJson();

            if (config('services.berita.verify_ssl', true) === false) {
                $http = $http->withoutVerifying();
            }

            $response = $http->get("{$this->baseUrl}/agenda", $query);

            if ($response->successful()) {
                $data = $response->json('data') ?? [];
                if (! empty($data)) {
                    Cache::put($cacheKey, $data, 600);
                }
                return $data;
            }
        } catch (Throwable $e) {
            Log::warning('Berita API agenda request failed: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Clear all cached berita and agenda API items.
     */
    public function clearCache(): void
    {
        for ($p = 1; $p <= 10; $p++) {
            Cache::forget('berita_api_' . md5(serialize(['page' => $p, 'per_page' => 9, 'search' => null])));
            Cache::forget('berita_api_' . md5(serialize(['page' => $p, 'per_page' => 10, 'search' => null])));
            Cache::forget('berita_api_' . md5(serialize(['page' => $p, 'per_page' => 3, 'search' => null])));
            Cache::forget('berita_api_' . md5(serialize(['page' => $p, 'per_page' => 5, 'search' => null])));
        }
        Cache::forget('agenda_api_' . md5(serialize(['per_page' => 3])));
        Cache::forget('agenda_api_' . md5(serialize([])));
    }

    /**
     * Normalize and format a single raw news item from the API
     * so it seamlessly aligns with the application's Post schema.
     */
    public function formatItem(array $item): array
    {
        $publishedAt = null;
        if (! empty($item['published_at'])) {
            try {
                $publishedAt = Carbon::parse($item['published_at'])->toISOString();
            } catch (Throwable) {
                $publishedAt = null;
            }
        }

        $summary = (string) ($item['summary'] ?? '');
        $cleanSummary = trim(strip_tags($summary));

        return [
            'id'             => $item['id'] ?? null,
            'title'          => (string) ($item['title'] ?? 'Tanpa Judul'),
            'slug'           => (string) ($item['slug'] ?? ''),
            'excerpt'        => Str::limit($cleanSummary, 160),
            'content'        => $summary,
            'featured_image' => $item['cover_image'] ?? $item['image'] ?? null,
            'url'            => $item['url'] ?? null,
            'category'       => $item['category'] ?? null,
            'opd'            => $item['opd'] ?? null,
            'author'         => $item['author'] ?? null,
            'views'          => (int) ($item['views'] ?? 0),
            'is_pinned'      => ! empty($item['is_pinned']),
            'status'         => 'published',
            'published_at'   => $publishedAt,
            'created_at'     => $publishedAt ?? Carbon::now()->toISOString(),
            'updated_at'     => Carbon::now()->toISOString(),
        ];
    }
}

