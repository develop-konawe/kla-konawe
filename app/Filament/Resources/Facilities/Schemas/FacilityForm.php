<?php

namespace App\Filament\Resources\Facilities\Schemas;

use App\Enums\ContentStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class FacilityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('organization_id')
                    ->label('OPD Pembina / Pengelola')
                    ->relationship('organization', 'name')
                    ->searchable()
                    ->preload()
                    ->helperText('Dinas/Badan yang membina atau bertanggung jawab atas fasilitas ini.'),

                TextInput::make('name')
                    ->label('Nama Fasilitas Ramah Anak')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state)))
                    ->placeholder('Contoh: Puskesmas Ramah Anak Unaaha / RPTRA Konawe Ceria')
                    ->helperText('Nama fasilitas yang tampil di peta & direktori /fasilitas.'),

                TextInput::make('slug')
                    ->label('Slug URL')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Akses web: /fasilitas/{slug}'),

                Select::make('category')
                    ->label('Kategori Fasilitas')
                    ->options([
                        'Puskesmas / Faskes Ramah Anak' => '🏥 Puskesmas / Faskes Ramah Anak',
                        'Sekolah Ramah Anak'             => '🏫 Sekolah / Madrasah Ramah Anak',
                        'Taman Bermain / RPTRA'          => '🎪 Taman Bermain / Ruang Bermain Ramah Anak (RBRA)',
                        'Perpustakaan & Pojok Baca'      => '📚 Perpustakaan & Pojok Baca Ramah Anak',
                        'Pusat Kreativitas Anak'         => '🎨 Pusat Kreativitas & Kesenian Anak (PKA)',
                        'Tempat Ibadah Ramah Anak'       => '🕌 Tempat Ibadah Ramah Anak',
                        'Fasilitas Publik Terbuka'       => '🌳 Fasilitas Publik Terbuka / Ramah Anak Lainnya',
                    ])
                    ->searchable()
                    ->helperText('Digunakan untuk filter kategori oleh pengunjung di halaman fasilitas.'),

                Toggle::make('is_child_friendly')
                    ->label('Terverifikasi Standar Ramah Anak')
                    ->default(true)
                    ->helperText('Jika aktif, akan menampilkan lencana hijau "Ramah Anak" dan status terverifikasi di website.'),

                TextInput::make('coordinates')
                    ->label('Titik Koordinat GPS (Latitude, Longitude)')
                    ->placeholder('Contoh: -3.8561, 122.0521')
                    ->helperText('Digunakan oleh pengunjung untuk membuka rute petunjuk arah di Google Maps / OpenStreetMap.'),

                Textarea::make('address')
                    ->label('Alamat Lengkap Lokasi')
                    ->columnSpanFull()
                    ->placeholder('Contoh: Jl. Inolobunggadue No. 12, Kel. Tuoy, Kec. Unaaha, Kab. Konawe')
                    ->helperText('Alamat detail agar pengunjung dan orang tua mudah menemukan lokasi.'),

                Select::make('status')
                    ->label('Status Publikasi')
                    ->options(ContentStatus::class)
                    ->default('published')
                    ->required()
                    ->helperText('Pilih "Published" agar fasilitas muncul di halaman publik /fasilitas.'),

                FileUpload::make('image')
                    ->label('Foto Fasilitas')
                    ->image()
                    ->directory('facilities')
                    ->columnSpanFull()
                    ->helperText('Foto dokumentasi fasilitas ramah anak (format JPG/PNG/WEBP).'),

                RichEditor::make('description')
                    ->label('Deskripsi Fasilitas & Sarana Prasarana Ramah Anak')
                    ->columnSpanFull()
                    ->helperText('Jelaskan fasilitas khusus anak yang tersedia (contoh: ruang laktasi, area bermain aman, toilet terpisah anak, bebas asap rokok).'),

                DateTimePicker::make('verified_at')
                    ->label('Waktu Verifikasi Standar KLA')
                    ->helperText('Tanggal saat fasilitas ini dinilai/diverifikasi ramah anak.'),

                Select::make('verified_by')
                    ->label('Petugas / Tim Verifikator')
                    ->relationship('verifier', 'name')
                    ->searchable()
                    ->preload()
                    ->helperText('Petugas Gugus Tugas KLA yang memverifikasi standar kelayakan.'),
            ]);
    }
}
