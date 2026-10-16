<?php

namespace App\Filament\Resources\Publications\Schemas;

use App\Enums\ContentStatus;
use App\Enums\PublicationClassification;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PublicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label('Kategori Dokumen')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->helperText('Contoh: Regulasi KLA, Rencana Aksi Daerah (RAD), Laporan Tahunan, Panduan.'),

                TextInput::make('title')
                    ->label('Judul Publikasi / Regulasi')
                    ->required()
                    ->placeholder('Contoh: Peraturan Daerah No. 3 Tahun 2023 tentang Penyelenggaraan KLA')
                    ->helperText('Nama resmi peraturan atau dokumen yang tampil di /publikasi.'),

                TextInput::make('year')
                    ->label('Tahun Dokumen')
                    ->numeric()
                    ->default(date('Y'))
                    ->placeholder('Contoh: 2024')
                    ->helperText('Tahun penetapan atau rilis dokumen.'),

                Select::make('classification')
                    ->label('Klasifikasi Keterbukaan')
                    ->options(PublicationClassification::class)
                    ->default('public')
                    ->required()
                    ->helperText('Pilih "Publik" agar tombol unduh dapat diakses seluruh masyarakat.'),

                Select::make('status')
                    ->label('Status Publikasi')
                    ->options(ContentStatus::class)
                    ->default('published')
                    ->required()
                    ->helperText('Pilih "Published" agar dokumen tampil di halaman publik /publikasi.'),

                DateTimePicker::make('published_at')
                    ->label('Waktu Publikasi')
                    ->default(now())
                    ->helperText('Tanggal dokumen mulai ditayangkan di portal.'),

                FileUpload::make('file_path')
                    ->label('Berkas Dokumen (PDF)')
                    ->directory('publications')
                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                    ->maxSize(20480)
                    ->columnSpanFull()
                    ->helperText('Unggah berkas PDF resmi (maksimal 20 MB). Tautan unduh otomatis aktif di website.'),

                Textarea::make('description')
                    ->label('Ringkasan / Abstrak Dokumen')
                    ->rows(3)
                    ->columnSpanFull()
                    ->helperText('Penjelasan singkat latar belakang dan substansi isi dokumen.'),
            ]);
    }
}
