<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Enums\ContentStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label('Kategori Berita')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->helperText('Klasifikasi klaster hak anak atau jenis kegiatan KLA.'),

                TextInput::make('title')
                    ->label('Judul Berita / Artikel')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state)))
                    ->placeholder('Contoh: DP3A Konawe Gelar Sosialisasi Sekolah Ramah Anak')
                    ->helperText('Judul artikel yang menarik dan informatif bagi pembaca.'),

                TextInput::make('slug')
                    ->label('Slug URL')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Akses web: /berita/{slug}'),

                Select::make('author_id')
                    ->label('Penulis / Kontributor')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload()
                    ->helperText('Akun pengelola yang menyusun artikel ini.'),

                Select::make('status')
                    ->label('Status Publikasi')
                    ->options(ContentStatus::class)
                    ->default('published')
                    ->required()
                    ->helperText('Pilih "Published" agar berita dapat dibaca di halaman /berita.'),

                DateTimePicker::make('published_at')
                    ->label('Tanggal Publikasi')
                    ->default(now())
                    ->helperText('Tanggal rilis artikel yang tampil di website publik.'),

                FileUpload::make('featured_image')
                    ->label('Foto Utama Berita')
                    ->image()
                    ->directory('posts')
                    ->columnSpanFull()
                    ->helperText('Foto sampul artikel (disarankan orientasi lanskap 16:9).'),

                Textarea::make('excerpt')
                    ->label('Ringkasan Singkat (Lead Paragraph)')
                    ->rows(3)
                    ->columnSpanFull()
                    ->helperText('Kutipan 2–3 kalimat yang muncul di kartu pratinjau berita.'),

                RichEditor::make('content')
                    ->label('Isi Berita / Artikel Lengkap')
                    ->columnSpanFull()
                    ->helperText('Tuliskan isi berita selengkapnya dengan format paragraf yang rapi.'),
            ]);
    }
}
