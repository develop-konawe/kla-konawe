<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Enums\ContentStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul Halaman')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state)))
                    ->placeholder('Contoh: Tentang Kabupaten Layak Anak (KLA)')
                    ->helperText('Judul halaman yang akan tampil sebagai heading utama di website.'),

                TextInput::make('slug')
                    ->label('Slug URL')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('PENTING: Gunakan slug "tentang-kla" untuk mengelola halaman Tentang KLA. Halaman lainnya dapat diakses di /halaman/{slug}.'),

                Select::make('status')
                    ->label('Status Publikasi')
                    ->options(ContentStatus::class)
                    ->default('published')
                    ->required()
                    ->helperText('Pilih "Published" agar halaman aktif dan dapat diakses pengunjung.'),

                DateTimePicker::make('published_at')
                    ->label('Waktu Publikasi')
                    ->default(now()),

                Select::make('author_id')
                    ->label('Penyusun')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload(),

                Select::make('reviewer_id')
                    ->label('Peninjau')
                    ->relationship('reviewer', 'name')
                    ->searchable()
                    ->preload(),

                Textarea::make('excerpt')
                    ->label('Ringkasan / Pengantar Singkat')
                    ->rows(2)
                    ->columnSpanFull()
                    ->helperText('Kutipan deskripsi singkat untuk meta description dan pengantar halaman.'),

                RichEditor::make('content')
                    ->label('Isi Konten Halaman')
                    ->columnSpanFull()
                    ->helperText('Tuliskan struktur visi misi, landasan hukum, atau materi edukasi yang ingin disampaikan.'),
            ]);
    }
}
