<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Enums\ContentStatus;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('organization_id')
                    ->label('OPD / Instansi Penyelenggara')
                    ->relationship('organization', 'name')
                    ->searchable()
                    ->preload()
                    ->helperText('Dinas/Badan Gugus Tugas KLA yang bertanggung jawab atas layanan ini.'),

                TextInput::make('name')
                    ->label('Nama Layanan Ramah Anak')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state)))
                    ->placeholder('Contoh: Layanan Konseling & Bantuan Hukum UPTD PPA')
                    ->helperText('Nama layanan yang akan tampil di direktori publik /layanan.'),

                TextInput::make('slug')
                    ->label('Slug URL')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Akses web: /layanan/{slug}'),

                Select::make('type')
                    ->label('Jenis / Moda Layanan')
                    ->options([
                        'terpadu'    => '⭐ Layanan Terpadu & Terintegrasi',
                        'online'     => '🌐 Layanan Online / Daring',
                        'offline'    => '🏢 Layanan Tatap Muka Langsung',
                        'kesehatan'  => '🏥 Layanan Kesehatan Anak',
                        'pendidikan' => '🎒 Layanan Pendidikan & Pengasuhan',
                        'hukum'      => '⚖️ Bantuan Hukum & Perlindungan Khusus',
                    ])
                    ->searchable()
                    ->helperText('Menentukan lencana/badge tipe layanan pada kartu di website.'),

                TextInput::make('contact_info')
                    ->label('Kontak / WhatsApp Resmi')
                    ->placeholder('Contoh: 0812-3456-7890 / ppa@konawekab.go.id')
                    ->helperText('Nomor WA/telepon akan otomatis dikonversi menjadi tombol kontak langsung.'),

                TextInput::make('working_hours')
                    ->label('Jam Layanan')
                    ->placeholder('Contoh: Senin - Jumat, 08.00 - 15.00 WITA')
                    ->helperText('Jadwal operasional layanan yang dapat dikunjungi oleh masyarakat.'),

                Select::make('status')
                    ->label('Status Publikasi')
                    ->options(ContentStatus::class)
                    ->default('published')
                    ->required()
                    ->helperText('Pilih "Published" agar langsung tayang dan dapat diakses publik.'),

                FileUpload::make('image')
                    ->label('Foto / Banner Layanan')
                    ->image()
                    ->directory('services')
                    ->columnSpanFull()
                    ->helperText('Foto dokumentasi layanan ramah anak (format JPG/PNG/WEBP).'),

                RichEditor::make('description')
                    ->label('Deskripsi Lengkap, Syarat & Alur Layanan')
                    ->columnSpanFull()
                    ->helperText('Jelaskan secara rinci sasaran anak, persyaratan berkas, dan langkah-langkah mendapatkan layanan.'),
            ]);
    }
}
