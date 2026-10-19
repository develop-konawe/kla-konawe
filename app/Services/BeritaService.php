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
    protected int $connectTimeout;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.berita.base_url', 'https://berita.konawekab.go.id/api/v1'), '/');
        
        $apiKey = config('services.berita.api_key');
        if (empty($apiKey)) {
            $apiKey = '418595a9a453178dbdc3ea13af01789324f967e9cd60069e624de8b92003a613';
        }
        $this->apiKey = $apiKey;

        // Tolerant network timeouts for cloud server to regional government server
        $this->connectTimeout = max(5, (int) config('services.berita.connect_timeout', 10));
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
     * Safe logging that will never crash the application if log files have permission errors.
     */
    protected function safeLog(string $level, string $message, array $context = []): void
    {
        try {
            Log::log($level, $message, $context);
        } catch (Throwable) {
            // Silently ignore if filesystem logging is restricted or has permission errors
        }
    }

    /**
     * Fetch news list directly from the live Berita API with caching.
     *
     * @param array $params Query parameters (page, per_page, search, category, etc.)
     * @return array{success: bool, data: array, meta: array}
     */
    public function getBerita(array $params = []): array
    {
        $page = (int) ($params['page'] ?? 1);
        $perPage = (int) ($params['per_page'] ?? 10);
        $search = $params['search'] ?? null;

        $cacheKey = 'berita_api_' . md5(serialize($params));

        // 1. Return valid cached live response if available
        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            if (is_array($cached) && ! empty($cached['success'])) {
                return $cached;
            }
            Cache::forget($cacheKey);
        }

        $query = array_filter([
            'api_key'  => $this->apiKey,
            'page'     => $page,
            'per_page' => $perPage,
            'search'   => $search,
            'category' => $params['category'] ?? null,
        ], static fn ($value) => filled($value));

        try {
            $response = $this->executeHttpRequest("{$this->baseUrl}/berita", $query);

            if (! $response->successful()) {
                $this->safeLog('warning', 'Berita API returned unsuccessful status', [
                    'url'    => "{$this->baseUrl}/berita",
                    'status' => $response->status(),
                    'body'   => Str::limit($response->body(), 200),
                ]);

                return [
                    'success' => false,
                    'data'    => [],
                    'meta'    => [
                        'total'        => 0,
                        'per_page'     => $perPage,
                        'current_page' => $page,
                        'last_page'    => 1,
                    ],
                ];
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

            // Only cache successful live responses for 10 minutes
            Cache::put($cacheKey, $result, 600);

            return $result;
        } catch (Throwable $e) {
            $this->safeLog('warning', 'Berita API request failed or timed out: ' . $e->getMessage(), [
                'url' => "{$this->baseUrl}/berita",
            ]);

            return [
                'success' => false,
                'data'    => [],
                'meta'    => [
                    'total'        => 0,
                    'per_page'     => $perPage,
                    'current_page' => $page,
                    'last_page'    => 1,
                ],
            ];
        }
    }

    /**
     * Execute an HTTP GET request with retries and SSL fallback.
     */
    protected function executeHttpRequest(string $url, array $query = [])
    {
        $verifySsl = config('services.berita.verify_ssl', true);

        try {
            $http = Http::timeout($this->timeout)
                ->connectTimeout($this->connectTimeout)
                ->retry(2, 300)
                ->acceptJson();

            if (! $verifySsl) {
                $http = $http->withoutVerifying();
            }

            return $http->get($url, $query);
        } catch (Throwable $e) {
            // If failed due to SSL handshake/certificate in Docker container, retry once withoutVerifying
            if ($verifySsl) {
                return Http::timeout($this->timeout)
                    ->connectTimeout($this->connectTimeout)
                    ->withoutVerifying()
                    ->acceptJson()
                    ->get($url, $query);
            }

            throw $e;
        }
    }

    /**
     * Get paginated news as a Laravel LengthAwarePaginator directly from live API.
     */
    public function getPaginatedBerita(int $page = 1, int $perPage = 9, ?string $search = null): ?LengthAwarePaginator
    {
        $result = $this->getBerita([
            'page'     => $page,
            'per_page' => $perPage,
            'search'   => $search,
        ]);

        if (! $result['success'] || empty($result['data'])) {
            return null;
        }

        return new LengthAwarePaginator(
            $result['data'],
            $result['meta']['total'],
            $result['meta']['per_page'],
            $result['meta']['current_page'],
            [
                'path'  => request()->url(),
                'query' => request()->query(),
            ]
        );
    }

    /**
     * Fetch latest news items directly from live API (for homepage or widgets).
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

        return $result['data'] ?? [];
    }

    /**
     * Find a news item from API by slug.
     */
    public function findBeritaBySlug(string $slug): ?array
    {
        $searchQuery = str_replace('-', ' ', $slug);
        $result = $this->getBerita([
            'search'   => $searchQuery,
            'per_page' => 15,
        ]);

        if (! empty($result['data'])) {
            foreach ($result['data'] as $item) {
                if (($item['slug'] ?? '') === $slug) {
                    return $item;
                }
            }
        }

        return null;
    }

    /**
     * Fetch agenda events directly from the API.
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
            'per_page' => $params['per_page'] ?? 5,
        ], static fn ($value) => filled($value));

        try {
            $response = $this->executeHttpRequest("{$this->baseUrl}/agenda", $query);

            if ($response->successful()) {
                $items = $response->json()['data'] ?? [];
                Cache::put($cacheKey, $items, 600);
                return $items;
            }

            return [];
        } catch (Throwable $e) {
            $this->safeLog('warning', 'Agenda API request failed or timed out: ' . $e->getMessage(), [
                'url' => "{$this->baseUrl}/agenda",
            ]);

            return [];
        }
    }

    /**
     * Clear all cached berita API responses.
     */
    public function clearCache(): void
    {
        Cache::forget('berita_api_' . md5(serialize([])));
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
