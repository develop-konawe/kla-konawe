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
        $this->apiKey = config('services.berita.api_key');
        $this->timeout = (int) config('services.berita.timeout', 2);
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
     *
     * @param array $params Query parameters (page, per_page, search, category, etc.)
     * @return array{success: bool, data: array, meta: array}
     */
    public function getBerita(array $params = []): array
    {
        $cacheKey = 'berita_api_' . md5(serialize($params));

        return Cache::remember($cacheKey, 600, function () use ($params) {
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
                $response = Http::timeout($this->timeout)
                    ->acceptJson()
                    ->get("{$this->baseUrl}/berita", $query);

                if (! $response->successful()) {
                    Log::warning('Berita API call returned unsuccessful status', [
                        'status' => $response->status(),
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

                return [
                    'success' => true,
                    'data'    => $formattedItems,
                    'meta'    => [
                        'total'        => (int) ($meta['total'] ?? count($formattedItems)),
                        'per_page'     => (int) ($meta['per_page'] ?? $perPage),
                        'current_page' => (int) ($meta['current_page'] ?? $page),
                        'last_page'    => (int) ($meta['last_page'] ?? 1),
                    ],
                ];
            } catch (Throwable $e) {
                Log::warning('Berita API request failed or timed out: ' . $e->getMessage());
                return $defaultResult;
            }
        });
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

        return Cache::remember($cacheKey, 600, function () use ($params) {
            $query = array_filter([
                'api_key'  => $this->apiKey,
                'page'     => $params['page'] ?? 1,
                'per_page' => $params['per_page'] ?? 10,
            ], static fn ($value) => filled($value));

            try {
                $response = Http::timeout($this->timeout)
                    ->acceptJson()
                    ->get("{$this->baseUrl}/agenda", $query);

                if ($response->successful()) {
                    return $response->json('data') ?? [];
                }
            } catch (Throwable $e) {
                Log::warning('Berita API agenda request failed: ' . $e->getMessage());
            }

            return [];
        });
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

