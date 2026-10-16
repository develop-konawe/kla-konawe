<?php

namespace App\Filament\Resources\Organizations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class OrganizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama OPD / Lembaga Gugus Tugas')
                    ->required()
                    ->placeholder('Contoh: Dinas Pemberdayaan Perempuan dan Perlindungan Anak')
                    ->helperText('Nama resmi instansi pemerintah atau mitra penyelenggara program KLA.'),

                TextInput::make('code')
                    ->label('Kode / Singkatan')
                    ->placeholder('Contoh: DP3A')
                    ->helperText('Singkatan nama OPD untuk kemudahan identifikasi.'),

                Select::make('type')
                    ->label('Jenis Lembaga')
                    ->options([
                        'dinas'      => 'Dinas Daerah Pemkab Konawe',
                        'badan'      => 'Badan Daerah Pemkab Konawe',
                        'kecamatan'  => 'Pemerintah Kecamatan',
                        'vertikal'   => 'Instansi Vertikal (Kemenag, Pengadilan, Kepolisian)',
                        'mitra'      => 'Organisasi / Lembaga Masyarakat Mitra KLA',
                    ])
                    ->default('dinas')
                    ->searchable()
                    ->helperText('Klasifikasi unit dalam susunan Gugus Tugas KLA Konawe.'),

                Toggle::make('is_active')
                    ->label('Anggota Aktif Gugus Tugas KLA')
                    ->default(true)
                    ->helperText('Hanya instansi aktif yang dapat dipilih sebagai penanggung jawab layanan & fasilitas.'),

                TextInput::make('phone')
                    ->label('Telepon Kantor / Call Center')
                    ->tel()
                    ->placeholder('Contoh: (0408) 2421xxx')
                    ->helperText('Nomor kontak resmi instansi.'),

                TextInput::make('email')
                    ->label('Email Resmi')
                    ->email()
                    ->placeholder('Contoh: kontak@dp3a.konawekab.go.id'),

                Textarea::make('address')
                    ->label('Alamat Kantor Instansi')
                    ->columnSpanFull()
                    ->placeholder('Contoh: Kompleks Perkantoran Pemkab Konawe, Unaaha')
                    ->helperText('Alamat kantor fisik instansi.'),
            ]);
    }
}
