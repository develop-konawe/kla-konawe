<?php

namespace App\Filament\Resources\Services\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Foto')
                    ->circular(),
                TextColumn::make('name')
                    ->label('Nama Layanan Ramah Anak')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('organization.name')
                    ->label('OPD Penyelenggara')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Moda / Jenis')
                    ->badge()
                    ->searchable(),
                TextColumn::make('contact_info')
                    ->label('Kontak / WA')
                    ->searchable(),
                TextColumn::make('working_hours')
                    ->label('Jam Layanan')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->searchable(),
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
                    ->tooltip('Buka halaman layanan ini di website publik')
                    ->url(fn ($record) => url("/layanan/{$record->slug}"))
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
