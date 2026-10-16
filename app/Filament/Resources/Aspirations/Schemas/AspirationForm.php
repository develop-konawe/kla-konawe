<?php

namespace App\Filament\Resources\Aspirations\Schemas;

use App\Enums\AspirationStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AspirationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul Aspirasi / Harapan Anak')
                    ->required()
                    ->placeholder('Contoh: Usulan pojok baca dan fasilitas bermain di ruang publik')
                    ->helperText('Pokok aspirasi atau ide yang disampaikan oleh anak/warga.'),

                Select::make('category')
                    ->label('Klaster / Kategori Aspirasi')
                    ->options([
                        'Pendidikan & Kebudayaan'    => '🎒 Pendidikan & Kebudayaan',
                        'Kesehatan & Gizi Anak'       => '🏥 Kesehatan & Gizi Anak',
                        'Fasilitas & Ruang Bermain'   => '🎪 Fasilitas & Ruang Publik Ramah Anak',
                        'Perlindungan Khusus Anak'    => '🛡️ Perlindungan Khusus & Anti-Kekerasan',
                        'Partisipasi & Hak Sipil'     => '🗣️ Partisipasi & Hak Sipil Anak',
                        'Lainnya'                     => '💬 Aspirasi Lainnya',
                    ])
                    ->searchable()
                    ->helperText('Klasifikasi klaster hak anak yang menjadi fokus aspirasi.'),

                TextInput::make('author_name')
                    ->label('Nama Pengirim (Anak / Warga)')
                    ->placeholder('Contoh: Salsabila (atau Inisial S)')
                    ->helperText('Nama pengirim atau inisial untuk melindungi privasi anak.'),

                TextInput::make('author_age')
                    ->label('Usia Anak (Tahun)')
                    ->numeric()
                    ->placeholder('Contoh: 15')
                    ->helperText('Usia anak saat menyampaikan aspirasi.'),

                TextInput::make('author_region')
                    ->label('Asal Kecamatan / Wilayah di Konawe')
                    ->placeholder('Contoh: Kecamatan Unaaha')
                    ->helperText('Wilayah domisili pengirim di Kabupaten Konawe.'),

                Select::make('status')
                    ->label('Status Moderasi & Tindak Lanjut')
                    ->options(AspirationStatus::class)
                    ->default('draft')
                    ->required()
                    ->helperText('Status verifikasi sebelum diteruskan ke OPD Gugus Tugas KLA terkait.'),

                RichEditor::make('content')
                    ->label('Isi Lengkap Aspirasi & Suara Anak')
                    ->required()
                    ->columnSpanFull()
                    ->helperText('Uraian lengkap saran, harapan, atau aspirasi yang disampaikan.'),

                Select::make('reviewed_by')
                    ->label('Petugas Peninjau')
                    ->relationship('reviewer', 'name')
                    ->searchable()
                    ->preload(),

                DateTimePicker::make('reviewed_at')
                    ->label('Waktu Ditinjau')
                    ->default(now()),
            ]);
    }
}
