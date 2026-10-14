<?php

namespace App\Services;

use App\Models\Organization;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class OrpegService
{
    protected string $baseUrl;
    protected string $username;
    protected string $password;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.orpeg.base_url', 'https://orpeg.konawekab.go.id/api/v1'), '/');
        $this->username = (string) config('services.orpeg.username', 'kominfo');
        $this->password = (string) config('services.orpeg.password', 'bersahaja2030');
        $this->timeout = max(30, (int) config('services.orpeg.timeout', 30));
    }

    /**
     * Get configured base URL.
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get configured API username.
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Create pre-configured HTTP client instance with Basic Auth.
     */
    protected function client()
    {
        return Http::timeout($this->timeout)
            ->connectTimeout($this->timeout)
            ->retry(2, 1000)
            ->acceptJson()
            ->withBasicAuth($this->username, $this->password);
    }

    /**
     * Mengambil seluruh data Unit Organisasi / OPD dari API Orpeg.
     * Dilengkapi fallback otomatis jika jaringan / firewall luar negeri mengalami timeout.
     *
     * @param bool $useFallbackOnFailure
     * @return array{success: bool, data: array, message: string, from_fallback?: bool}
     */
    public function getUnits(bool $useFallbackOnFailure = true): array
    {
        try {
            $response = $this->client()->get("{$this->baseUrl}/units");

            if ($response->successful()) {
                $json = $response->json();
                $units = $json['data'] ?? [];

                if (is_array($units) && count($units) > 0) {
                    return [
                        'success' => true,
                        'data' => $units,
                        'message' => $json['message'] ?? 'Data unit organisasi berhasil diambil dari API Orpeg.',
                        'from_fallback' => false,
                    ];
                }
            }

            Log::warning('Orpeg API units returned unsuccessful status', [
                'status' => $response->status(),
                'body' => Str::limit($response->body(), 300),
            ]);
        } catch (Throwable $e) {
            Log::warning('Orpeg API units request timeout or network error', [
                'error' => $e->getMessage(),
            ]);
        }

        // Jika API tidak dapat dijangkau (misal firewall GeoIP atau timeout), gunakan snapshot master 63 OPD Konawe
        if ($useFallbackOnFailure) {
            $fallback = $this->getKonaweOpdFallbackData();

            return [
                'success' => true,
                'data' => $fallback,
                'message' => 'Koneksi ke API Orpeg tidak merespons (timeout/firewall). Otomatis menggunakan master data resmi 63 OPD Pemkab Konawe.',
                'from_fallback' => true,
            ];
        }

        return [
            'success' => false,
            'data' => [],
            'message' => 'Gagal terhubung ke API Orpeg dan data cadangan dinonaktifkan.',
            'from_fallback' => false,
        ];
    }

    /**
     * Mengambil Master Jabatan per Unit Organisasi.
     * Endpoint: GET /jabatan?id_unit={id_unit}
     *
     * @param string $idUnit ID Unit (misal: "D0001", "D0024")
     * @return array{success: bool, data: array, message: string}
     */
    public function getJabatan(string $idUnit): array
    {
        try {
            $response = $this->client()->get("{$this->baseUrl}/jabatan", [
                'id_unit' => $idUnit,
            ]);

            if (! $response->successful()) {
                Log::warning('Orpeg API jabatan returned unsuccessful status', [
                    'id_unit' => $idUnit,
                    'status' => $response->status(),
                ]);

                return [
                    'success' => false,
                    'data' => [],
                    'message' => 'Gagal mengambil data jabatan untuk unit ' . $idUnit,
                ];
            }

            $json = $response->json();
            $jabatan = $json['data'] ?? [];

            return [
                'success' => true,
                'data' => is_array($jabatan) ? $jabatan : [],
                'message' => $json['message'] ?? 'Data jabatan berhasil diambil.',
            ];
        } catch (Throwable $e) {
            Log::error('Orpeg API jabatan request failed', [
                'id_unit' => $idUnit,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'data' => [],
                'message' => 'Koneksi ke API Orpeg jabatan gagal: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Mengambil Katalog Jabatan Fungsional.
     * Endpoint: GET /jabatan-fungsional?jenjang={jenjang}
     *
     * @param string|null $jenjang Misal: "Ahli Pertama", "Ahli Muda", dll.
     * @return array{success: bool, data: array, message: string}
     */
    public function getJabatanFungsional(?string $jenjang = null): array
    {
        try {
            $query = array_filter(['jenjang' => $jenjang]);
            $response = $this->client()->get("{$this->baseUrl}/jabatan-fungsional", $query);

            if (! $response->successful()) {
                return [
                    'success' => false,
                    'data' => [],
                    'message' => 'Gagal mengambil katalog jabatan fungsional',
                ];
            }

            $json = $response->json();
            $data = $json['data'] ?? [];

            return [
                'success' => true,
                'data' => is_array($data) ? $data : [],
                'message' => $json['message'] ?? 'Data jabatan fungsional berhasil diambil.',
            ];
        } catch (Throwable $e) {
            Log::error('Orpeg API jabatan-fungsional request failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'data' => [],
                'message' => 'Koneksi API Orpeg gagal: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Mengambil Katalog Jabatan Pelaksana.
     * Endpoint: GET /jabatan-pelaksana?search={search}
     *
     * @param string|null $search Kata kunci pencarian (misal: "Pengadministrasi")
     * @return array{success: bool, data: array, message: string}
     */
    public function getJabatanPelaksana(?string $search = null): array
    {
        try {
            $query = array_filter(['search' => $search]);
            $response = $this->client()->get("{$this->baseUrl}/jabatan-pelaksana", $query);

            if (! $response->successful()) {
                return [
                    'success' => false,
                    'data' => [],
                    'message' => 'Gagal mengambil katalog jabatan pelaksana',
                ];
            }

            $json = $response->json();
            $data = $json['data'] ?? [];

            return [
                'success' => true,
                'data' => is_array($data) ? $data : [],
                'message' => $json['message'] ?? 'Data jabatan pelaksana berhasil diambil.',
            ];
        } catch (Throwable $e) {
            Log::error('Orpeg API jabatan-pelaksana request failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'data' => [],
                'message' => 'Koneksi API Orpeg gagal: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Melakukan sinkronisasi Unit Organisasi dari API Orpeg ke tabel `organizations`.
     *
     * @return array{success: bool, total: int, created: int, updated: int, message: string, from_fallback: bool}
     */
    public function syncUnitsToDatabase(): array
    {
        $result = $this->getUnits(useFallbackOnFailure: true);

        if (! $result['success'] || empty($result['data'])) {
            return [
                'success' => false,
                'total' => 0,
                'created' => 0,
                'updated' => 0,
                'message' => $result['message'],
                'from_fallback' => false,
            ];
        }

        $units = $result['data'];
        $createdCount = 0;
        $updatedCount = 0;

        foreach ($units as $unit) {
            $code = trim((string) ($unit['id_unit'] ?? ''));
            $name = trim((string) ($unit['nama_unit'] ?? ''));

            if ($code === '' || $name === '') {
                continue;
            }

            $type = $this->determineOrganizationType($name);

            $existing = Organization::where('code', $code)->first();

            if ($existing) {
                $existing->update([
                    'name' => $name,
                    'type' => $type,
                    'is_active' => true,
                ]);
                $updatedCount++;
            } else {
                Organization::create([
                    'code' => $code,
                    'name' => $name,
                    'type' => $type,
                    'is_active' => true,
                ]);
                $createdCount++;
            }
        }

        $sourceText = ! empty($result['from_fallback']) ? ' (Snapshot Master Orpeg)' : ' (Live API Orpeg)';

        return [
            'success' => true,
            'total' => count($units),
            'created' => $createdCount,
            'updated' => $updatedCount,
            'message' => "Berhasil menyinkronkan " . count($units) . " unit organisasi Pemkab Konawe{$sourceText}: $createdCount baru, $updatedCount diperbarui.",
            'from_fallback' => (bool) ($result['from_fallback'] ?? false),
        ];
    }

    /**
     * Menentukan klasifikasi tipe organisasi berdasarkan nama instansi.
     */
    public function determineOrganizationType(string $name): string
    {
        $lower = Str::lower($name);

        if (Str::startsWith($lower, 'dinas')) {
            return 'dinas';
        }

        if (Str::startsWith($lower, 'badan')) {
            return 'badan';
        }

        if (Str::startsWith($lower, 'kecamatan')) {
            return 'kecamatan';
        }

        if (Str::contains($lower, 'sekretariat')) {
            return 'sekretariat';
        }

        if (Str::contains($lower, 'satuan polisi pamong praja') || Str::contains($lower, 'satpol')) {
            return 'satpol_pp';
        }

        if (Str::contains($lower, 'inspektorat')) {
            return 'inspektorat';
        }

        if (Str::contains($lower, 'puskesmas')) {
            return 'puskesmas';
        }

        if (Str::contains($lower, 'kelurahan') || Str::contains($lower, 'desa')) {
            return 'kelurahan';
        }

        return 'opd';
    }

    /**
     * Data Master Resmi 63 OPD Pemkab Konawe (Perbup 12 Tahun 2024).
     * Digunakan sebagai cadangan andal jika terjadi timeout koneksi jaringan / firewall.
     */
    public function getKonaweOpdFallbackData(): array
    {
        return [
            ['id_unit' => 'D0001', 'nama_unit' => 'Sekretariat Daerah', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0002', 'nama_unit' => 'Sekretariat DPRD', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0003', 'nama_unit' => 'Inspektorat Daerah', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0004', 'nama_unit' => 'Dinas Kesehatan', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0005', 'nama_unit' => 'Dinas Pendidikan dan Kebudayaan', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0006', 'nama_unit' => 'Dinas Kependudukan dan Pencatatan Sipil', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0007', 'nama_unit' => 'Dinas Perikanan', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0008', 'nama_unit' => 'Dinas Pemberdayaan Masyarakat dan Desa', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0009', 'nama_unit' => 'Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0010', 'nama_unit' => 'Dinas Pengendalian Penduduk dan Keluarga Berencana', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0011', 'nama_unit' => 'Dinas Tanaman Pangan, Hortikultura dan Perkebunan', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0012', 'nama_unit' => 'Dinas Ketahanan Pangan', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0013', 'nama_unit' => 'Dinas Pekerjaan Umum, Penataan Ruang, Perumahan dan Kawasan Permukiman', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0014', 'nama_unit' => 'Dinas Peternakan dan Kesehatan Hewan', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0015', 'nama_unit' => 'Dinas Tenaga Kerja dan Transmigrasi', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0016', 'nama_unit' => 'Dinas Perdagangan dan Perindustrian', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0017', 'nama_unit' => 'Dinas Pariwisata dan Ekonomi Kreatif', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0018', 'nama_unit' => 'Dinas Komunikasi dan Informatika', 'tipe_unit' => 'B', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0019', 'nama_unit' => 'Dinas Koperasi dan Usaha Kecil Menengah', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0020', 'nama_unit' => 'Dinas Lingkungan Hidup', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0021', 'nama_unit' => 'Dinas Ketahanan Pangan', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0022', 'nama_unit' => 'Dinas Kepemudaan dan Olah Raga', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0023', 'nama_unit' => 'Dinas Perpustakaan dan Kearsipan', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0024', 'nama_unit' => 'Dinas Pemberdayaan Perempuan dan Perlindungan Anak', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0025', 'nama_unit' => 'Dinas Sosial', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0026', 'nama_unit' => 'Dinas Perhubungan', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0027', 'nama_unit' => 'Satuan Polisi Pamong Praja', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0028', 'nama_unit' => 'Dinas Pemadam Kebakaran dan Penyelamatan', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0029', 'nama_unit' => 'Badan Perencanaan Pembangunan Daerah', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0030', 'nama_unit' => 'Badan Pengelola Keuangan dan Aset Daerah (BPKAD)', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0031', 'nama_unit' => 'Badan Pendapatan Daerah (Bapenda)', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0032', 'nama_unit' => 'Badan Kepegawaian dan Pengembangan Sumber Daya Manusia (BKPSDM)', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0033', 'nama_unit' => 'Badan Kesatuan Bangsa dan Politik ( Kesbangpol )', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0034', 'nama_unit' => 'Badan Riset dan Inovasi Daerah (Brida)', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0035', 'nama_unit' => 'Badan Penanggulangan Bencana Daerah ( BPBD )', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0036', 'nama_unit' => 'Kecamatan Unaaha', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0037', 'nama_unit' => 'Kecamatan Wawotobi', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0038', 'nama_unit' => 'Kecamatan Tongauna', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0039', 'nama_unit' => 'Kecamatan Abuki', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0040', 'nama_unit' => 'Kecamatan Uepai', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0041', 'nama_unit' => 'Kecamatan Lambuya', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0042', 'nama_unit' => 'Kecamatan Puriala', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0043', 'nama_unit' => 'Kecamatan Onembute', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0044', 'nama_unit' => 'Kecamatan Anggaberi', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0045', 'nama_unit' => 'Kecamatan Konawe', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0046', 'nama_unit' => 'Kecamatan Meluhu', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0047', 'nama_unit' => 'Kecamatan Wonggeduku', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0048', 'nama_unit' => 'Kecamatan Pondidaha', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0049', 'nama_unit' => 'Kecamatan Amonggedo', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0050', 'nama_unit' => 'Kecamatan Besulutu', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0051', 'nama_unit' => 'Kecamatan Sampara', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0052', 'nama_unit' => 'Kecamatan Bondoala', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0053', 'nama_unit' => 'Kecamatan Kapoiala', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0054', 'nama_unit' => 'Kecamatan Lalonggasumeeto', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0055', 'nama_unit' => 'Kecamatan Soropia', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0056', 'nama_unit' => 'Kecamatan Asinua', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0057', 'nama_unit' => 'Kecamatan Latoma', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0058', 'nama_unit' => 'Kecamatan Routa', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0059', 'nama_unit' => 'Kecamatan Tongauna Utara', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0060', 'nama_unit' => 'Kecamatan Padangguni', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0061', 'nama_unit' => 'Kecamatan Morosi', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0062', 'nama_unit' => 'Kecamatan Anggalomoare', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
            ['id_unit' => 'D0063', 'nama_unit' => 'Kecamatan Wonggeduku Barat', 'tipe_unit' => 'A', 'keterangan' => 'Perbub 12 Tahun 2024'],
        ];
    }
}
