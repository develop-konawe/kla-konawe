<?php

namespace App\Console\Commands;

use App\Services\BeritaService;
use Illuminate\Console\Command;

class FetchBeritaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'berita:fetch {--limit=5 : Number of news items to fetch} {--search= : Search keyword} {--page=1 : Page number}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test and fetch news from Konawe Berita API';

    /**
     * Execute the console command.
     */
    public function handle(BeritaService $service): int
    {
        $limit = (int) $this->option('limit');
        $search = $this->option('search');
        $page = (int) $this->option('page');

        $this->info("Fetching news from API: " . $service->getBaseUrl());
        $this->line("API Key: " . substr((string) $service->getApiKey(), 0, 10) . '...');

        $result = $service->getBerita([
            'page'     => $page,
            'per_page' => $limit,
            'search'   => $search,
        ]);

        if (! $result['success']) {
            $this->error("Failed to fetch news from API. Please verify network access, base URL, and API key.");
            return Command::FAILURE;
        }

        $meta = $result['meta'];
        $items = $result['data'];

        $this->info("Total: {$meta['total']} | Page: {$meta['current_page']}/{$meta['last_page']} | Count: " . count($items));

        $headers = ['ID', 'Title', 'Category', 'Published At', 'URL'];
        $rows = array_map(static fn ($item) => [
            $item['id'],
            \Illuminate\Support\Str::limit($item['title'], 40),
            $item['category']['name'] ?? '-',
            $item['published_at'] ? substr($item['published_at'], 0, 10) : '-',
            \Illuminate\Support\Str::limit($item['url'] ?? '-', 45),
        ], $items);

        $this->table($headers, $rows);

        return Command::SUCCESS;
    }
}

