<?php

namespace Tests\Unit;

use App\Models\Organization;
use App\Services\OrpegService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OrpegServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.orpeg.base_url', 'https://orpeg.konawekab.go.id/api/v1');
        Config::set('services.orpeg.username', 'kominfo');
        Config::set('services.orpeg.password', 'bersahaja2030');
        Config::set('services.orpeg.timeout', 5);
    }

    public function test_it_loads_configuration_correctly(): void
    {
        $service = new OrpegService();

        $this->assertEquals('https://orpeg.konawekab.go.id/api/v1', $service->getBaseUrl());
        $this->assertEquals('kominfo', $service->getUsername());
    }

    public function test_it_fetches_units_successfully(): void
    {
        Http::fake([
            'https://orpeg.konawekab.go.id/api/v1/units' => Http::response([
                'status' => true,
                'message' => 'Data unit organisasi berhasil diambil',
                'data' => [
                    [
                        'id_unit' => 'D0024',
                        'nama_unit' => 'Dinas Pemberdayaan Perempuan dan Perlindungan Anak',
                        'tipe_unit' => 'A',
                        'keterangan' => 'Perbub 12 Tahun 2024',
                    ],
                    [
                        'id_unit' => 'D0004',
                        'nama_unit' => 'Dinas Kesehatan',
                        'tipe_unit' => 'A',
                        'keterangan' => 'Perbub 12 Tahun 2024',
                    ],
                ],
            ], 200),
        ]);

        $service = new OrpegService();
        $result = $service->getUnits();

        $this->assertTrue($result['success']);
        $this->assertCount(2, $result['data']);
        $this->assertEquals('D0024', $result['data'][0]['id_unit']);
        $this->assertEquals('Dinas Pemberdayaan Perempuan dan Perlindungan Anak', $result['data'][0]['nama_unit']);
    }

    public function test_it_handles_units_api_failure(): void
    {
        Http::fake([
            'https://orpeg.konawekab.go.id/api/v1/units' => Http::response([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401),
        ]);

        $service = new OrpegService();
        $result = $service->getUnits(useFallbackOnFailure: false);

        $this->assertFalse($result['success']);
        $this->assertEmpty($result['data']);
    }

    public function test_it_falls_back_to_konawe_master_snapshot_on_api_timeout(): void
    {
        Http::fake([
            'https://orpeg.konawekab.go.id/api/v1/units' => function () {
                throw new \Illuminate\Http\Client\ConnectionException('Connection timed out');
            },
        ]);

        $service = new OrpegService();
        $result = $service->getUnits(useFallbackOnFailure: true);

        $this->assertTrue($result['success']);
        $this->assertTrue($result['from_fallback']);
        $this->assertCount(63, $result['data']);
        $this->assertEquals('D0001', $result['data'][0]['id_unit']);
    }

    public function test_it_fetches_jabatan_successfully(): void
    {
        Http::fake([
            'https://orpeg.konawekab.go.id/api/v1/jabatan*' => Http::response([
                'status' => true,
                'message' => 'Data jabatan berhasil diambil',
                'data' => [
                    [
                        'kode_jabatan' => 'D0024-000-0001',
                        'nama_jabatan' => 'Kepala Dinas',
                        'id_unit' => 'D0024',
                        'jenis_jabatan' => 'Jabatan Pimpinan Tinggi Pratama',
                        'kelas_jabatan' => '14',
                    ],
                ],
            ], 200),
        ]);

        $service = new OrpegService();
        $result = $service->getJabatan('D0024');

        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['data']);
        $this->assertEquals('Kepala Dinas', $result['data'][0]['nama_jabatan']);
    }

    public function test_it_determines_organization_type_correctly(): void
    {
        $service = new OrpegService();

        $this->assertEquals('dinas', $service->determineOrganizationType('Dinas Pemberdayaan Perempuan dan Perlindungan Anak'));
        $this->assertEquals('badan', $service->determineOrganizationType('Badan Perencanaan Pembangunan Daerah'));
        $this->assertEquals('kecamatan', $service->determineOrganizationType('Kecamatan Unaaha'));
        $this->assertEquals('sekretariat', $service->determineOrganizationType('Sekretariat Daerah'));
        $this->assertEquals('satpol_pp', $service->determineOrganizationType('Satuan Polisi Pamong Praja'));
        $this->assertEquals('opd', $service->determineOrganizationType('Pusat Layanan Terpadu'));
    }
}

