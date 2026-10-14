<?php

namespace App\Console\Commands;

use App\Services\OrpegService;
use Illuminate\Console\Command;

class SyncOrganizationsFromOrpeg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orpeg:sync-organizations 
                            {--jabatan= : Tampilkan master jabatan untuk id_unit tertentu}
                            {--dry-run : Uji koneksi tanpa menyimpan ke database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi Unit Organisasi / OPD dari API Orpeg Konawe ke database Portal KLA';

    /**
     * Execute the console command.
     */
    public function handle(OrpegService $orpegService): int
    {
        $this->info('=====================================================');
        $this->info('  Sinkronisasi Organisasi / OPD dari API Orpeg Konawe');
        $this->info('=====================================================');
        $this->line("Base URL : <comment>{$orpegService->getBaseUrl()}</comment>");
        $this->line("User     : <comment>{$orpegService->getUsername()}</comment>");
        $this->newLine();

        // Jika opsi --jabatan diberikan, ambil jabatan per unit
        $jabatanUnit = $this->option('jabatan');
        if ($jabatanUnit) {
            $this->info("Mengambil master jabatan untuk unit [{$jabatanUnit}]...");
            $res = $orpegService->getJabatan($jabatanUnit);
            if (! $res['success']) {
                $this->error($res['message']);
                return Command::FAILURE;
            }

            $jabatanList = $res['data'];
            $this->info("Ditemukan " . count($jabatanList) . " jabatan:");

            $rows = array_map(function ($j) {
                return [
                    $j['kode_jabatan'] ?? '-',
                    $j['nama_jabatan'] ?? '-',
                    $j['jenis_jabatan'] ?? '-',
                    $j['kelas_jabatan'] ?? '-',
                ];
            }, array_slice($jabatanList, 0, 25));

            $this->table(['Kode Jabatan', 'Nama Jabatan', 'Jenis', 'Kelas'], $rows);
            return Command::SUCCESS;
        }

        // Ambil data unit dari API (dengan fallback otomatis jika timeout)
        $this->info('Mengambil data unit organisasi dari API Orpeg...');
        $res = $orpegService->getUnits(useFallbackOnFailure: true);

        if (! $res['success']) {
            $this->error('Gagal: ' . $res['message']);
            return Command::FAILURE;
        }

        $units = $res['data'];
        if (! empty($res['from_fallback'])) {
            $this->warn('ℹ️  Koneksi langsung ke server API Orpeg mengalami timeout / dibatasi firewall.');
            $this->line("   -> Otomatis menggunakan master snapshot resmi " . count($units) . " OPD Pemkab Konawe.");
        } else {
            $this->info("Berhasil mengambil " . count($units) . " unit organisasi langsung dari API Orpeg.");
        }

        if ($this->option('dry-run')) {
            $this->warn('Mode Dry-Run aktif: Data tidak disimpan ke database.');
            $rows = array_map(function ($u) {
                return [
                    $u['id_unit'] ?? '-',
                    $u['nama_unit'] ?? '-',
                    $u['tipe_unit'] ?? '-',
                    $u['keterangan'] ?? '-',
                ];
            }, array_slice($units, 0, 10));

            $this->table(['ID Unit', 'Nama Unit', 'Tipe', 'Dasar Hukum'], $rows);
            return Command::SUCCESS;
        }

        // Eksekusi penyimpanan ke database
        $this->info('Menyimpan / memperbarui unit organisasi ke database...');
        $syncResult = $orpegService->syncUnitsToDatabase();

        if ($syncResult['success']) {
            $this->newLine();
            $this->info('✅ ' . $syncResult['message']);
            $this->table(
                ['Keterangan', 'Jumlah'],
                [
                    ['Total Unit Diproses', $syncResult['total']],
                    ['Unit Baru Ditambahkan', $syncResult['created']],
                    ['Unit Diperbarui', $syncResult['updated']],
                    ['Sumber Data', ! empty($syncResult['from_fallback']) ? 'Snapshot Master Konawe (Fallback Timeout)' : 'Live API Orpeg'],
                ]
            );
            return Command::SUCCESS;
        }

        $this->error('Gagal sinkronisasi: ' . $syncResult['message']);
        return Command::FAILURE;
    }
}

