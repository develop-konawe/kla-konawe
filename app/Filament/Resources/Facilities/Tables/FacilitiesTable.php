<?php

namespace App\Filament\Resources\Facilities\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FacilitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Foto')
                    ->circular(),
                TextColumn::make('name')
                    ->label('Nama Fasilitas')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->searchable(),
                TextColumn::make('organization.name')
                    ->label('OPD Pembina')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_child_friendly')
                    ->label('Ramah Anak')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->trueColor('success')
                    ->falseIcon('heroicon-o-x-circle')
                    ->falseColor('gray'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('coordinates')
                    ->label('Koordinat')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('verified_at')
                    ->label('Diverifikasi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Action::make('view_public')
                    ->label('Lihat di Web')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('info')
                    ->tooltip('Buka halaman detail fasilitas ini di website publik')
                    ->url(fn ($record) => url("/fasilitas/{$record->slug}"))
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
