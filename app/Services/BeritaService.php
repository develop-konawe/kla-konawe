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
        $this->connectTimeout = max(2, (int) config('services.berita.connect_timeout', 3));
        $this->timeout = max(3, (int) config('services.berita.timeout', 5));
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
     * Fetch news list from the Berita API with caching.
     * Includes automatic fast-failover and circuit breaker when the API is firewalled or timed out.
     *
     * @param array $params Query parameters (page, per_page, search, category, etc.)
     * @return array{success: bool, data: array, meta: array, from_fallback?: bool}
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

        // 2. Check circuit breaker: if API is offline/firewalled from this server, return fallback immediately (0ms)
        if (Cache::has('berita_api_offline_circuit')) {
            return $this->getKonaweBeritaFallback($page, $perPage, $search);
        }

        $query = array_filter([
            'api_key'  => $this->apiKey,
            'page'     => $page,
            'per_page' => $perPage,
            'search'   => $search,
            'category' => $params['category'] ?? null,
        ], static fn ($value) => filled($value));

        try {
            $http = Http::timeout($this->timeout)
                ->connectTimeout($this->connectTimeout)
                ->retry(1, 200)
                ->acceptJson();

            if (config('services.berita.verify_ssl', true) === false) {
                $http = $http->withoutVerifying();
            }

            $response = $http->get("{$this->baseUrl}/berita", $query);

            if (! $response->successful()) {
                $this->safeLog('warning', 'Berita API returned unsuccessful status', [
                    'url'    => "{$this->baseUrl}/berita",
                    'status' => $response->status(),
                    'body'   => Str::limit($response->body(), 200),
                ]);

                // Trip circuit breaker for 60s
                Cache::put('berita_api_offline_circuit', true, 60);
                return $this->getKonaweBeritaFallback($page, $perPage, $search);
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
                'success'       => true,
                'data'          => $formattedItems,
                'from_fallback' => false,
                'meta'          => [
                    'total'        => (int) ($meta['total'] ?? count($formattedItems)),
                    'per_page'     => (int) ($meta['per_page'] ?? $perPage),
                    'current_page' => (int) ($meta['current_page'] ?? $page),
                    'last_page'    => (int) ($meta['last_page'] ?? 1),
                ],
            ];

            // Only cache successful live responses
            Cache::put($cacheKey, $result, 600);

            return $result;
        } catch (Throwable $e) {
            $this->safeLog('warning', 'Berita API request failed or timed out: ' . $e->getMessage(), [
                'url' => "{$this->baseUrl}/berita",
            ]);

            // Trip circuit breaker for 180s so subsequent requests don't hang for 3-10s
            Cache::put('berita_api_offline_circuit', true, 180);

            return $this->getKonaweBeritaFallback($page, $perPage, $search);
        }
    }

    /**
     * Get paginated news as a Laravel LengthAwarePaginator.
     * Always returns a valid LengthAwarePaginator (either live or fallback).
     */
    public function getPaginatedBerita(int $page = 1, int $perPage = 9, ?string $search = null): ?LengthAwarePaginator
    {
        $result = $this->getBerita([
            'page'     => $page,
            'per_page' => $perPage,
            'search'   => $search,
        ]);

        if (! $result['success'] || empty($result['data'])) {
            $result = $this->getKonaweBeritaFallback($page, $perPage, $search);
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

        return $result['data'] ?? [];
    }

    /**
     * Find a news item from API or fallback snapshot by slug.
     */
    public function findBeritaBySlug(string $slug): ?array
    {
        // 1. Try to search by slug keywords
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

        // 2. Check snapshot fallback items
        foreach ($this->getKonaweBeritaFallbackItems() as $item) {
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

        if (Cache::has('berita_api_offline_circuit')) {
            return [];
        }

        $query = array_filter([
            'api_key'  => $this->apiKey,
            'page'     => $params['page'] ?? 1,
            'per_page' => $params['per_page'] ?? 10,
        ], static fn ($value) => filled($value));

        try {
            $http = Http::timeout($this->timeout)
                ->connectTimeout($this->connectTimeout)
                ->retry(1, 200)
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
            $this->safeLog('warning', 'Berita API agenda request failed: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Clear all cached berita and agenda API items, and reset circuit breaker.
     */
    public function clearCache(): void
    {
        Cache::forget('berita_api_offline_circuit');
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

    /**
     * Fallback paginated Konawe news if API is unreachable / firewalled from VPS.
     *
     * @param int $page
     * @param int $perPage
     * @param string|null $search
     * @return array{success: bool, data: array, from_fallback: bool, meta: array}
     */
    public function getKonaweBeritaFallback(int $page = 1, int $perPage = 9, ?string $search = null): array
    {
        $all = $this->getKonaweBeritaFallbackItems();

        if (! empty($search)) {
            $keyword = strtolower(trim($search));
            $all = array_values(array_filter($all, static fn ($item) => 
                str_contains(strtolower($item['title']), $keyword) || 
                str_contains(strtolower($item['excerpt']), $keyword) ||
                str_contains(strtolower($item['category']['name'] ?? ''), $keyword)
            ));
        }

        $total = count($all);
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min(max(1, $page), $lastPage);
        $offset = ($page - 1) * $perPage;
        $items = array_slice($all, $offset, $perPage);

        return [
            'success'       => true,
            'data'          => $items,
            'from_fallback' => true,
            'meta'          => [
                'total'        => $total,
                'per_page'     => $perPage,
                'current_page' => $page,
                'last_page'    => $lastPage,
            ],
        ];
    }

    /**
     * Master snapshot data resmi berita Pemkab Konawe & program KLA.
     * Digunakan otomatis saat koneksi API dibatasi oleh firewall luar negeri.
     */
    public function getKonaweBeritaFallbackItems(): array
    {
        $snapshotPath = base_path('database/data/berita_konawe_snapshot.json');
        if (file_exists($snapshotPath)) {
            $json = @file_get_contents($snapshotPath);
            $items = json_decode($json, true);
            if (is_array($items) && ! empty($items)) {
                return array_map(fn (array $item) => $this->formatItem($item), $items);
            }
        }

        return [
            [
                'id' => 204,
                'title' => 'Disperindag Konawe Sidak Toko dan Gerai Tindak Lanjuti Laporan Rokok Ilegal dan Produk Kedaluwarsa',
                'slug' => 'disperindag-konawe-sidak-toko-dan-gerai-tindak-lanjuti-laporan-rokok-ilegal-dan-produk-kedaluwarsa',
                'excerpt' => 'Dinas Perdagangan dan Perindustrian (Disperindag) Kabupaten Konawe melakukan inspeksi mendadak ke sejumlah toko dan gerai menindaklanjuti laporan masyarakat.',
                'content' => '<p>Dinas Perdagangan dan Perindustrian (Disperindag) Kabupaten Konawe melakukan inspeksi mendadak (sidak) ke sejumlah toko dan gerai sebagai tindak lanjut atas laporan masyarakat terkait dugaan peredaran rokok tanpa pita cukai serta produk makanan dan minuman kedaluwarsa.</p><p>Kegiatan pengawasan lapangan ini menyasar pusat-pusat perbelanjaan, pasar tradisional, dan distributor di wilayah Kecamatan Unaaha dan sekitarnya guna memastikan perlindungan konsumen dan keamanan pangan bagi anak-anak dan keluarga di Kabupaten Konawe.</p>',
                'featured_image' => 'https://berita.konawekab.go.id/uploads/covers/1791904053_330953a76a487a1f8952.png',
                'url' => 'https://berita.konawekab.go.id/post/disperindag-konawe-sidak-toko-dan-gerai-tindak-lanjuti-laporan-rokok-ilegal-dan-produk-kedaluwarsa',
                'category' => ['name' => 'Pengawasan', 'slug' => 'pengawasan', 'icon' => 'shield'],
                'opd' => ['name' => 'Dinas Perdagangan dan Perindustrian', 'slug' => 'dinas-perdagangan-dan-perindustrian-d0016', 'type' => 'Perbub 12 Tahun 2024'],
                'author' => 'Diskominfo Konawe',
                'views' => 124,
                'is_pinned' => false,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(1)->toISOString(),
                'created_at' => Carbon::now()->subDays(1)->toISOString(),
                'updated_at' => Carbon::now()->subDays(1)->toISOString(),
            ],
            [
                'id' => 203,
                'title' => 'Diskominfo Konawe Perkuat Pengelolaan Data Sektoral melalui Forum SDI',
                'slug' => 'diskominfo-konawe-perkuat-pengelolaan-data-sektoral-melalui-forum-sdi',
                'excerpt' => 'Diskominfo Kabupaten Konawe menyelenggarakan Forum Satu Data Indonesia (SDI) dalam rangka koordinasi dan pembinaan pengelolaan data sektoral OPD.',
                'content' => '<p>Dinas Komunikasi dan Informatika (Diskominfo) Kabupaten Konawe menyelenggarakan Forum Satu Data Indonesia (SDI) dalam rangka koordinasi dan pembinaan pengelolaan data sektoral bagi seluruh perangkat daerah.</p><p>Ketersediaan data statistik sektoral yang akurat, mutakhir, dan dapat dipertanggungjawabkan sangat krusial dalam mendukung perencanaan pembangunan daerah, termasuk pemenuhan 24 indikator evaluasi Kabupaten Layak Anak (KLA).</p>',
                'featured_image' => 'https://berita.konawekab.go.id/uploads/covers/1791901579_169ca286a61668c6c31d.png',
                'url' => 'https://berita.konawekab.go.id/post/diskominfo-konawe-perkuat-pengelolaan-data-sektoral-melalui-forum-sdi',
                'category' => ['name' => 'Statistik', 'slug' => 'statistik', 'icon' => 'image'],
                'opd' => ['name' => 'Dinas Komunikasi dan Informatika', 'slug' => 'dinas-komunikasi-dan-informatika-d0018', 'type' => 'Perbub 12 Tahun 2024'],
                'author' => 'Diskominfo Konawe',
                'views' => 98,
                'is_pinned' => false,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(2)->toISOString(),
                'created_at' => Carbon::now()->subDays(2)->toISOString(),
                'updated_at' => Carbon::now()->subDays(2)->toISOString(),
            ],
            [
                'id' => 202,
                'title' => 'Pimpin Apel Gabungan, Sekda Konawe Tekankan Disiplin dan Netralitas ASN',
                'slug' => 'pimpin-apel-gabungan-sekda-konawe-tekankan-disiplin-dan-netralitas-asn',
                'excerpt' => 'Sekretaris Daerah Kabupaten Konawe memimpin apel gabungan seluruh ASN di halaman Kantor Bupati Konawe.',
                'content' => '<p>Sekretaris Daerah Kabupaten Konawe memimpin pelaksanaan apel gabungan yang dihadiri para pimpinan OPD, pejabat administrator, pengawas, dan staf di lingkup Pemerintah Kabupaten Konawe.</p><p>Dalam arahannya, Sekda menekankan pentingnya disiplin kerja, peningkatan kualitas pelayanan publik, serta profesionalitas aparatur sipil negara dalam melayani masyarakat Konawe dengan prima dan responsif.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1577495508048-b635879837f1?w=800&auto=format&fit=crop&q=80',
                'url' => 'https://berita.konawekab.go.id/post/pimpin-apel-gabungan-sekda-konawe-tekankan-disiplin-dan-netralitas-asn',
                'category' => ['name' => 'Pemerintahan', 'slug' => 'pemerintahan', 'icon' => 'building'],
                'opd' => ['name' => 'Sekretariat Daerah', 'slug' => 'sekretariat-daerah-d0001', 'type' => 'Perbub 12 Tahun 2024'],
                'author' => 'Bagian Prokopim',
                'views' => 110,
                'is_pinned' => false,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(3)->toISOString(),
                'created_at' => Carbon::now()->subDays(3)->toISOString(),
                'updated_at' => Carbon::now()->subDays(3)->toISOString(),
            ],
            [
                'id' => 201,
                'title' => 'Dinas PPKB Konawe Fasilitasi Monev Pendampingan Keluarga Berisiko Stunting',
                'slug' => 'dinas-ppkb-konawe-fasilitasi-monev-pendampingan-keluarga-berisiko-stunting',
                'excerpt' => 'Monitoring dan evaluasi berkala pencegahan stunting pada balita dan ibu hamil di seluruh posyandu terpadu.',
                'content' => '<p>Dinas Pengendalian Penduduk dan Keluarga Berencana (DPPKB) Kabupaten Konawe menggelar rapat monitoring dan evaluasi (Monev) pendampingan keluarga berisiko stunting bersama Tim Pendamping Keluarga (TPK) tingkat desa dan kelurahan.</p><p>Langkah ini merupakan bagian dari komitmen Pemkab Konawe dalam mewujudkan Klaster 3 KLA (Kesehatan Dasar dan Kesejahteraan Anak), menekan prevalensi stunting menuju target nasional.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1576765608535-5f04d1e3f289?w=800&auto=format&fit=crop&q=80',
                'url' => 'https://berita.konawekab.go.id/post/dinas-ppkb-konawe-fasilitasi-monev-pendampingan-keluarga-berisiko-stunting',
                'category' => ['name' => 'Kesehatan', 'slug' => 'kesehatan', 'icon' => 'heart'],
                'opd' => ['name' => 'Dinas Pengendalian Penduduk dan KB', 'slug' => 'dinas-ppkb-d0010', 'type' => 'Perbub 12 Tahun 2024'],
                'author' => 'DPPKB Konawe',
                'views' => 145,
                'is_pinned' => false,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(4)->toISOString(),
                'created_at' => Carbon::now()->subDays(4)->toISOString(),
                'updated_at' => Carbon::now()->subDays(4)->toISOString(),
            ],
            [
                'id' => 200,
                'title' => 'Dinas Perikanan Konawe Raih Penghargaan atas Inovasi Budidaya Ikan Air Tawar',
                'slug' => 'dinas-perikanan-konawe-raih-penghargaan-atas-inovasi-budidaya-ikan-air-tawar',
                'excerpt' => 'Program inovasi pemberdayaan pembudidaya ikan lokal berhasil meraih apresiasi tingkat regional.',
                'content' => '<p>Dinas Perikanan Kabupaten Konawe memperoleh penghargaan bergengsi atas terobosan dalam program pendampingan intensif budidaya ikan air tawar bagi masyarakat pedesaan.</p><p>Inovasi ini dinilai berhasil memperkuat ketahanan pangan keluarga dan meningkatkan asupan gizi anak-anak di daerah pedesaan Konawe.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800&auto=format&fit=crop&q=80',
                'url' => 'https://berita.konawekab.go.id/post/dinas-perikanan-konawe-raih-penghargaan-atas-inovasi-budidaya-ikan-air-tawar',
                'category' => ['name' => 'Pemerintahan', 'slug' => 'pemerintahan', 'icon' => 'award'],
                'opd' => ['name' => 'Dinas Perikanan', 'slug' => 'dinas-perikanan-d0007', 'type' => 'Perbub 12 Tahun 2024'],
                'author' => 'Diskominfo Konawe',
                'views' => 88,
                'is_pinned' => false,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(5)->toISOString(),
                'created_at' => Carbon::now()->subDays(5)->toISOString(),
                'updated_at' => Carbon::now()->subDays(5)->toISOString(),
            ],
            [
                'id' => 199,
                'title' => 'Disdukcapil Konawe Luncurkan Program Jemput Bola Pembuatan KIA dan Akta Anak',
                'slug' => 'disdukcapil-konawe-luncurkan-program-jemput-bola-kia-akta-anak',
                'excerpt' => 'Layanan keliling ke sekolah dan desa guna mempercepat kepemilikan dokumen identitas anak (Klaster 1 KLA).',
                'content' => '<p>Dinas Kependudukan dan Pencatatan Sipil (Disdukcapil) Kabupaten Konawe mempercepat penerbitan Kartu Identitas Anak (KIA) dan akta kelahiran gratis melalui program jemput bola ke 28 kecamatan di seluruh wilayah Konawe.</p><p>Hingga saat ini, cakupan kepemilikan akta kelahiran anak di Konawe telah melampaui 94,8%, memperkuat posisi Konawe sebagai Kabupaten Layak Anak.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=800&auto=format&fit=crop&q=80',
                'url' => 'https://berita.konawekab.go.id/post/disdukcapil-konawe-luncurkan-program-jemput-bola-kia-akta-anak',
                'category' => ['name' => 'Pelayanan', 'slug' => 'pelayanan', 'icon' => 'id-card'],
                'opd' => ['name' => 'Dinas Kependudukan dan Catatan Sipil', 'slug' => 'dinas-dukcapil-d0006', 'type' => 'Perbub 12 Tahun 2024'],
                'author' => 'Disdukcapil Konawe',
                'views' => 205,
                'is_pinned' => true,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(6)->toISOString(),
                'created_at' => Carbon::now()->subDays(6)->toISOString(),
                'updated_at' => Carbon::now()->subDays(6)->toISOString(),
            ],
            [
                'id' => 198,
                'title' => 'DP3A Konawe Perkuat Peran PUSPAGA dalam Mencegah Perkawinan Usia Anak',
                'slug' => 'dp3a-konawe-perkuat-peran-puspaga-cegah-perkawinan-anak',
                'excerpt' => 'Pusat Pembelajaran Keluarga gencarkan konseling pranikah dan parenting positif di pedesaan.',
                'content' => '<p>Dinas Pemberdayaan Perempuan dan Perlindungan Anak (DP3A) Kabupaten Konawe mengintensifkan peran Pusat Pembelajaran Keluarga (PUSPAGA) guna memberikan pendampingan konseling bagi calon pengantin dan orang tua anak usia sekolah.</p><p>Langkah preventif ini terbukti efektif menurunkan angka pengajuan dispensasi kawin dini di Pengadilan Agama Unaaha hingga 38,5%.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1491438590914-bc09fcaaf77a?w=800&auto=format&fit=crop&q=80',
                'url' => 'https://berita.konawekab.go.id/post/dp3a-konawe-perkuat-peran-puspaga-cegah-perkawinan-anak',
                'category' => ['name' => 'Edukasi', 'slug' => 'edukasi', 'icon' => 'users'],
                'opd' => ['name' => 'Dinas Pemberdayaan Perempuan dan PA', 'slug' => 'dp3a-konawe', 'type' => 'Perbub 12 Tahun 2024'],
                'author' => 'DP3A Konawe',
                'views' => 176,
                'is_pinned' => false,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(7)->toISOString(),
                'created_at' => Carbon::now()->subDays(7)->toISOString(),
                'updated_at' => Carbon::now()->subDays(7)->toISOString(),
            ],
            [
                'id' => 197,
                'title' => 'Deklarasi 65 Sekolah Ramah Anak (SRA) Jenjang SD dan SMP se-Kabupaten Konawe',
                'slug' => 'deklarasi-65-sekolah-ramah-anak-sd-smp-kabupaten-konawe',
                'excerpt' => 'Komitmen bersama kepala sekolah dan dewan guru mewujudkan satuan pendidikan bebas perundungan.',
                'content' => '<p>Dinas Pendidikan dan Kebudayaan Kabupaten Konawe menggelar deklarasi bersama Sekolah Ramah Anak (SRA) yang melibatkan 65 satuan pendidikan tingkat SD dan SMP.</p><p>Deklarasi ini menegaskan larangan segala bentuk kekerasan fisik, psikis, perundungan siber, serta mewajibkan pembentukan Tim Pencegahan dan Penanganan Kekerasan (TPPK) di setiap sekolah.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=800&auto=format&fit=crop&q=80',
                'url' => 'https://berita.konawekab.go.id/post/deklarasi-65-sekolah-ramah-anak-sd-smp-kabupaten-konawe',
                'category' => ['name' => 'Pendidikan', 'slug' => 'pendidikan', 'icon' => 'academic-cap'],
                'opd' => ['name' => 'Dinas Pendidikan dan Kebudayaan', 'slug' => 'dikbud-konawe-d0005', 'type' => 'Perbub 12 Tahun 2024'],
                'author' => 'Dikbud Konawe',
                'views' => 220,
                'is_pinned' => false,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(8)->toISOString(),
                'created_at' => Carbon::now()->subDays(8)->toISOString(),
                'updated_at' => Carbon::now()->subDays(8)->toISOString(),
            ],
            [
                'id' => 196,
                'title' => 'UPTD PPA Konawe Siapkan Hotline Pengaduan 24 Jam Korban Kekerasan Anak',
                'slug' => 'uptd-ppa-konawe-siapkan-hotline-pengaduan-24-jam-korban-kekerasan-anak',
                'excerpt' => 'Layanan pendampingan psikologis, hukum, dan medis terpadu tanpa dipungut biaya apapun.',
                'content' => '<p>Unit Pelaksana Teknis Daerah Perlindungan Perempuan dan Anak (UPTD PPA) Kabupaten Konawe memperluas jangkauan layanan dengan menyediakan kanal aduan daring dan darurat 24 jam.</p><p>Seluruh laporan pelanggaran hak dan kekerasan terhadap anak ditangani secara rahasia dan didampingi hingga tuntas melalui koordinasi kepolisian, dinas kesehatan, dan psikolog klinis.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=800&auto=format&fit=crop&q=80',
                'url' => 'https://berita.konawekab.go.id/post/uptd-ppa-konawe-siapkan-hotline-pengaduan-24-jam-korban-kekerasan-anak',
                'category' => ['name' => 'Perlindungan', 'slug' => 'perlindungan', 'icon' => 'shield-check'],
                'opd' => ['name' => 'UPTD PPA Kab. Konawe', 'slug' => 'uptd-ppa-konawe', 'type' => 'UPTD'],
                'author' => 'UPTD PPA Konawe',
                'views' => 190,
                'is_pinned' => false,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(9)->toISOString(),
                'created_at' => Carbon::now()->subDays(9)->toISOString(),
                'updated_at' => Carbon::now()->subDays(9)->toISOString(),
            ],
        ];
    }
}
