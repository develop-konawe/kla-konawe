<?php

namespace App\Filament\Resources\Statistics\Schemas;

use App\Enums\ContentStatus;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StatisticForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Indikator & Klaster KLA')
                    ->schema([
                        TextInput::make('title')
                            ->label('Nama Indikator KLA / Statistik Anak')
                            ->required()
                            ->columnSpanFull()
                            ->placeholder('Contoh: Persentase Anak Memiliki Kartu Identitas Anak (KIA)')
                            ->helperText('Nama indikator capaian evaluasi Kabupaten Layak Anak.'),

                        Select::make('category_id')
                            ->label('Klaster Hak Anak')
                            ->options([
                                'Klaster 1' => 'Klaster 1: Hak Sipil dan Kebebasan',
                                'Klaster 2' => 'Klaster 2: Lingkungan Keluarga dan Pengasuhan Alternatif',
                                'Klaster 3' => 'Klaster 3: Kesehatan Dasar dan Kesejahteraan',
                                'Klaster 4' => 'Klaster 4: Pendidikan, Pemanfaatan Waktu Luang, dan Seni Budaya',
                                'Klaster 5' => 'Klaster 5: Perlindungan Khusus',
                                'Kelembagaan' => 'Penguatan Kelembagaan KLA',
                            ])
                            ->searchable()
                            ->helperText('Kelompok indikator sesuai 5 Klaster KemenPPPA.'),

                        TextInput::make('region_id')
                            ->label('Cakupan Wilayah')
                            ->default('Kabupaten Konawe')
                            ->placeholder('Contoh: Kabupaten Konawe / Kecamatan Unaaha'),
                    ])->columns(2),

                Section::make('Nilai Capaian Data')
                    ->schema([
                        TextInput::make('year')
                            ->label('Tahun Data')
                            ->required()
                            ->numeric()
                            ->default(date('Y'))
                            ->placeholder('Contoh: 2024'),

                        TextInput::make('value')
                            ->label('Nilai / Angka Capaian')
                            ->required()
                            ->numeric()
                            ->placeholder('Contoh: 87.5'),

                        TextInput::make('unit')
                            ->label('Satuan')
                            ->placeholder('Contoh: %, Orang, Unit, Sekolah')
                            ->helperText('Satuan ukuran data.'),

                        TextInput::make('source')
                            ->label('Sumber Data / OPD Pengampu')
                            ->placeholder('Contoh: Dinas Dukcapil / Dinas Kesehatan Konawe')
                            ->helperText('Instansi penanggung jawab penyedia data.'),
                    ])->columns(2),

                Section::make('Publikasi')
                    ->schema([
                        Select::make('status')
                            ->label('Status Publikasi')
                            ->options(ContentStatus::class)
                            ->default('published')
                            ->required()
                            ->helperText('Pilih "Published" agar statistik tampil di agregat publik.'),

                        KeyValue::make('metadata_json')
                            ->label('Keterangan Tambahan / Metadata')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
