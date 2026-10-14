<?php

namespace App\Filament\Resources\Organizations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class OrganizationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode Unit')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama Organisasi / OPD')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),
                TextColumn::make('type')
                    ->label('Klasifikasi')
                    ->badge()
                    ->colors([
                        'success' => 'dinas',
                        'info' => 'badan',
                        'warning' => 'kecamatan',
                        'primary' => 'sekretariat',
                        'danger' => 'satpol_pp',
                        'gray' => 'opd',
                    ])
                    ->formatStateUsing(fn (?string $state) => strtoupper($state ?? '-'))
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Telepon / Kontak')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->placeholder('-'),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Klasifikasi')
                    ->options([
                        'dinas' => 'Dinas',
                        'badan' => 'Badan',
                        'kecamatan' => 'Kecamatan',
                        'sekretariat' => 'Sekretariat',
                        'satpol_pp' => 'Satpol PP',
                        'lsm' => 'LSM / Non-Pemda',
                        'opd' => 'OPD Lainnya',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
