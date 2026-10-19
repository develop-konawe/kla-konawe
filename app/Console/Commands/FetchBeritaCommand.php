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
    protected $signature = 'berita:fetch 
                            {--limit=5 : Number of news items to fetch} 
                            {--search= : Search keyword} 
                            {--page=1 : Page number}
                            {--clear-cache : Clear cached berita API items before fetching}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test and diagnose connection directly to Konawe Berita API';

    /**
     * Execute the console command.
     */
    public function handle(BeritaService $service): int
    {
        $limit = (int) $this->option('limit');
        $search = $this->option('search');
        $page = (int) $this->option('page');
        $clearCache = (bool) $this->option('clear-cache');

        if ($clearCache) {
            $service->clearCache();
            $this->warn("Berita API cache has been cleared.");
        }

        $baseUrl = $service->getBaseUrl();
        $apiKey = $service->getApiKey();
        $host = parse_url($baseUrl, PHP_URL_HOST);

        $this->info("==========================================");
        $this->info("    Diagnostik Koneksi Berita API KLA     ");
        $this->info("==========================================");
        $this->line("Base URL : {$baseUrl}");
        $this->line("API Key  : " . (empty($apiKey) ? '<error>KOSONG / TIDAK DIATUR</error>' : substr($apiKey, 0, 10) . '...' . substr($apiKey, -6)));

        // 1. DNS Resolution Test
        $this->line("DNS Test : Memeriksa resolusi host '{$host}'...");
        $resolvedIp = @gethostbyname($host);
        if ($resolvedIp && $resolvedIp !== $host) {
            $this->info("DNS Ok   : Host '{$host}' berhasil di-resolve ke IP {$resolvedIp}");
        } else {
            $this->warn("DNS Fail : Host '{$host}' tidak dapat di-resolve oleh DNS container/server.");
            $this->warn("Saran    : Pastikan 'extra_hosts' di docker-compose.yml aktif: '{$host}:103.170.89.162'");
        }

        // 2. Fetch Data directly from live API
        $this->line("\nMengambil data langsung dari Berita API...");
        $startTime = microtime(true);

        $result = $service->getBerita([
            'page'     => $page,
            'per_page' => $limit,
            'search'   => $search,
        ]);

        $elapsed = round((microtime(true) - $startTime) * 1000, 2);

        if (! $result['success'] || empty($result['data'])) {
            $this->error("\n[GAGAL] Tidak dapat mengambil berita langsung dari API ({$elapsed}ms).");
            $this->line("Kemungkinan penyebab:");
            $this->line(" 1. Server staging / IP cloud dibatasi oleh firewall server Pemda (port 443 ke 103.170.89.162)");
            $this->line(" 2. Resolusi DNS gagal (cek extra_hosts di docker-compose.yml)");
            $this->line(" 3. API key kosong atau tidak cocok");
            $this->line(" 4. Cek log laravel dengan: tail -n 50 storage/logs/laravel.log");
            return Command::FAILURE;
        }

        $meta = $result['meta'];
        $items = $result['data'];

        $this->info("[SUKSES] Berhasil terhubung ke Berita API ({$elapsed}ms)!");
        $this->info("Total: {$meta['total']} berita | Halaman: {$meta['current_page']}/{$meta['last_page']} | Menampilkan: " . count($items) . " item\n");

        $headers = ['ID', 'Judul Berita', 'Kategori', 'Tanggal Tayang', 'Penulis / OPD'];
        $rows = array_map(static fn ($item) => [
            $item['id'],
            \Illuminate\Support\Str::limit($item['title'], 40),
            $item['category']['name'] ?? '-',
            $item['published_at'] ? substr($item['published_at'], 0, 10) : '-',
            \Illuminate\Support\Str::limit($item['opd']['name'] ?? ($item['author'] ?? '-'), 30),
        ], $items);

        $this->table($headers, $rows);

        return Command::SUCCESS;
    }
}
