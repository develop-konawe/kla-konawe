<?php

namespace Tests\Unit;

use App\Services\BeritaService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BeritaServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.berita.base_url', 'https://berita.konawekab.go.id/api/v1');
        Config::set('services.berita.api_key', '418595a9a453178dbdc3ea13af01789324f967e9cd60069e624de8b92003a613');
        Config::set('services.berita.timeout', 5);
    }

    public function test_it_loads_configuration_correctly(): void
    {
        $service = new BeritaService();

        $this->assertEquals('https://berita.konawekab.go.id/api/v1', $service->getBaseUrl());
        $this->assertEquals('418595a9a453178dbdc3ea13af01789324f967e9cd60069e624de8b92003a613', $service->getApiKey());
    }

    public function test_it_fetches_and_formats_berita_successfully(): void
    {
        Http::fake([
            'https://berita.konawekab.go.id/api/v1/berita*' => Http::response([
                'success' => true,
                'meta' => [
                    'total' => 2,
                    'per_page' => 2,
                    'current_page' => 1,
                    'last_page' => 1,
                ],
                'data' => [
                    [
                        'id' => 101,
                        'title' => 'Judul Berita Tes',
                        'slug' => 'judul-berita-tes',
                        'summary' => '<p>Ringkasan artikel tes.</p>',
                        'cover_image' => 'https://berita.konawekab.go.id/uploads/covers/test.jpg',
                        'category' => [
                            'name' => 'Pemerintahan',
                            'slug' => 'pemerintahan',
                        ],
                        'opd' => [
                            'name' => 'Diskominfo',
                            'slug' => 'diskominfo',
                        ],
                        'author' => 'Admin',
                        'views' => 45,
                        'is_pinned' => false,
                        'published_at' => '2026-10-12 08:00:00',
                        'url' => 'https://berita.konawekab.go.id/post/judul-berita-tes',
                    ],
                ],
            ], 200),
        ]);

        $service = new BeritaService();
        $result = $service->getBerita(['page' => 1, 'per_page' => 2]);

        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['data']);

        $item = $result['data'][0];
        $this->assertEquals(101, $item['id']);
        $this->assertEquals('Judul Berita Tes', $item['title']);
        $this->assertEquals('judul-berita-tes', $item['slug']);
        $this->assertEquals('Ringkasan artikel tes.', $item['excerpt']);
        $this->assertEquals('https://berita.konawekab.go.id/uploads/covers/test.jpg', $item['featured_image']);
        $this->assertEquals('https://berita.konawekab.go.id/post/judul-berita-tes', $item['url']);
        $this->assertEquals('published', $item['status']);
    }

    public function test_it_handles_api_failure_gracefully(): void
    {
        Http::fake([
            'https://berita.konawekab.go.id/api/v1/berita*' => Http::response(null, 500),
        ]);

        $service = new BeritaService();
        $result = $service->getBerita(['page' => 1, 'per_page' => 5]);

        $this->assertFalse($result['success']);
        $this->assertEmpty($result['data']);
        $this->assertEquals(0, $result['meta']['total']);
    }

    public function test_it_fetches_agenda_successfully(): void
    {
        Http::fake([
            'https://berita.konawekab.go.id/api/v1/agenda*' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'id' => 1,
                        'title' => 'Rapat Koordinasi',
                        'description' => 'Deskripsi rapat',
                    ],
                ],
            ], 200),
        ]);

        $service = new BeritaService();
        $events = $service->getAgenda(['per_page' => 1]);

        $this->assertCount(1, $events);
        $this->assertEquals('Rapat Koordinasi', $events[0]['title']);
    }

    public function test_it_returns_paginator_for_valid_data(): void
    {
        Http::fake([
            'https://berita.konawekab.go.id/api/v1/berita*' => Http::response([
                'success' => true,
                'meta' => [
                    'total' => 1,
                    'per_page' => 9,
                    'current_page' => 1,
                    'last_page' => 1,
                ],
                'data' => [
                    [
                        'id' => 1,
                        'title' => 'Test',
                        'slug' => 'test',
                    ],
                ],
            ], 200),
        ]);

        $service = new BeritaService();
        $paginator = $service->getPaginatedBerita(1, 9);

        $this->assertNotNull($paginator);
        $this->assertEquals(1, $paginator->total());
        $this->assertEquals(1, $paginator->currentPage());
    }

    public function test_it_finds_berita_by_slug(): void
    {
        Http::fake([
            'https://berita.konawekab.go.id/api/v1/berita*' => Http::response([
                'success' => true,
                'meta' => ['total' => 1, 'per_page' => 15, 'current_page' => 1, 'last_page' => 1],
                'data' => [
                    [
                        'id' => 55,
                        'title' => 'Konawe Bangkit',
                        'slug' => 'konawe-bangkit',
                        'url' => 'https://berita.konawekab.go.id/post/konawe-bangkit',
                    ],
                ],
            ], 200),
        ]);

        $service = new BeritaService();
        $item = $service->findBeritaBySlug('konawe-bangkit');

        $this->assertNotNull($item);
        $this->assertEquals('konawe-bangkit', $item['slug']);
        $this->assertEquals('https://berita.konawekab.go.id/post/konawe-bangkit', $item['url']);
    }
}
