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
        $this->timeout = (int) config('services.orpeg.timeout', 15);
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
            ->acceptJson()
            ->withBasicAuth($this->username, $this->password);
    }

    /**
     * Mengambil seluruh data Unit Organisasi / OPD dari API Orpeg.
     * Endpoint: GET /units
     *
     * @return array{success: bool, data: array, message: string}
     */
    public function getUnits(): array
    {
        try {
            $response = $this->client()->get("{$this->baseUrl}/units");

            if (! $response->successful()) {
                Log::warning('Orpeg API units returned unsuccessful status', [
                    'status' => $response->status(),
                    'body' => Str::limit($response->body(), 300),
                ]);

                return [
                    'success' => false,
                    'data' => [],
                    'message' => 'Gagal mengambil data dari API Orpeg (Status: ' . $response->status() . ')',
                ];
            }

            $json = $response->json();
            $units = $json['data'] ?? [];

            return [
                'success' => true,
                'data' => is_array($units) ? $units : [],
                'message' => $json['message'] ?? 'Data unit organisasi berhasil diambil.',
            ];
        } catch (Throwable $e) {
            Log::error('Orpeg API units request failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'data' => [],
                'message' => 'Koneksi ke API Orpeg gagal: ' . $e->getMessage(),
            ];
        }
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
     * @return array{success: bool, total: int, created: int, updated: int, message: string}
     */
    public function syncUnitsToDatabase(): array
    {
        $result = $this->getUnits();

        if (! $result['success'] || empty($result['data'])) {
            return [
                'success' => false,
                'total' => 0,
                'created' => 0,
                'updated' => 0,
                'message' => $result['message'],
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

        return [
            'success' => true,
            'total' => count($units),
            'created' => $createdCount,
            'updated' => $updatedCount,
            'message' => "Berhasil menyinkronkan " . count($units) . " unit organisasi dari API Orpeg ($createdCount baru, $updatedCount diperbarui).",
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
}

