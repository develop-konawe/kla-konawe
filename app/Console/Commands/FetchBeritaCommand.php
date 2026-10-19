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
                            {--clear-cache : Clear cached berita API items before fetching}
                            {--sync : Fetch all news items from live API and save to database/data/berita_konawe_snapshot.json}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test, diagnose, or sync connection to Konawe Berita API';

    /**
     * Execute the console command.
     */
    public function handle(BeritaService $service): int
    {
        $limit = (int) $this->option('limit');
        $search = $this->option('search');
        $page = (int) $this->option('page');
        $clearCache = (bool) $this->option('clear-cache');
        $sync = (bool) $this->option('sync');

        if ($clearCache) {
            $service->clearCache();
            $this->warn("Berita API cache has been cleared.");
        }

        $baseUrl = $service->getBaseUrl();
        $apiKey = $service->getApiKey();
        $host = parse_url($baseUrl, PHP_URL_HOST);

        if ($sync) {
            $this->info("==========================================");
            $this->info(" Sinkronisasi Snapshot Berita API Konawe ");
            $this->info("==========================================");
            $all = [];
            $currentPage = 1;
            $lastPage = 1;
            do {
                $url = "{$baseUrl}/berita";
                try {
                    $response = \Illuminate\Support\Facades\Http::timeout(15)
                        ->acceptJson()
                        ->get($url, [
                            'api_key'  => $apiKey,
                            'page'     => $currentPage,
                            'per_page' => 50,
                        ]);

                    if (! $response->successful()) {
                        $this->error("Gagal mengambil halaman {$currentPage}: HTTP " . $response->status());
                        break;
                    }

                    $json = $response->json();
                    $items = $json['data'] ?? [];
                    if (empty($items)) {
                        break;
                    }

                    $all = array_merge($all, $items);
                    $lastPage = $json['meta']['last_page'] ?? 1;
                    $this->line(" -> Halaman {$currentPage}/{$lastPage} terunduh (" . count($items) . " item)");
                    $currentPage++;
                } catch (\Throwable $e) {
                    $this->error("Koneksi gagal atau timed out: " . $e->getMessage());
                    break;
                }
            } while ($currentPage <= $lastPage);

            if (! empty($all)) {
                $dir = base_path('database/data');
                if (! is_dir($dir)) {
                    @mkdir($dir, 0755, true);
                }
                file_put_contents(
                    $dir . '/berita_konawe_snapshot.json',
                    json_encode($all, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                );
                $this->info("\n[SUKSES] Berhasil menyimpan " . count($all) . " berita ke database/data/berita_konawe_snapshot.json!");
                $service->clearCache();
                return Command::SUCCESS;
            }

            $this->warn("\nTidak ada berita yang dapat disinkronkan.");
            return Command::FAILURE;
        }

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
            $this->warn("Saran    : Tambahkan 'extra_hosts' di docker-compose.yml: '{$host}:103.170.89.162'");
        }

        // 2. Fetch Data
        $this->line("\nMengambil data berita...");
        $startTime = microtime(true);

        $result = $service->getBerita([
            'page'     => $page,
            'per_page' => $limit,
            'search'   => $search,
        ]);

        $elapsed = round((microtime(true) - $startTime) * 1000, 2);

        if (! $result['success']) {
            $this->error("\n[GAGAL] Tidak dapat mengambil berita dari API ({$elapsed}ms).");
            $this->line("Kemungkinan penyebab:");
            $this->line(" 1. API Key tidak valid atau kosong di staging .env (Gunakan: 418595a9a453178dbdc3ea13af01789324f967e9cd60069e624de8b92003a613)");
            $this->line(" 2. Server staging tidak dapat mengakses internet / port 443 ke 103.170.89.162 (Firewall/GeoIP)");
            $this->line(" 3. Docker container gagal resolve domain (Tambahkan extra_hosts & dns di docker-compose.yml)");
            $this->line(" 4. Cek log laravel dengan: tail -n 50 storage/logs/laravel.log");
            return Command::FAILURE;
        }

        $meta = $result['meta'];
        $items = $result['data'];

        $this->info("[SUKSES] Berhasil terhubung ke Berita API ({$elapsed}ms)!");
        $this->info("Total: {$meta['total']} berita | Halaman: {$meta['current_page']}/{$meta['last_page']} | Menampilkan: " . count($items) . " item\n");

        if (empty($items)) {
            $this->warn("Tidak ada item berita pada halaman ini.");
            return Command::SUCCESS;
        }

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

