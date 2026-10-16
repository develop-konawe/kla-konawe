<?php

namespace App\Filament\Resources\Aspirations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AspirationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Aspirasi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(45),
                TextColumn::make('category')
                    ->label('Klaster / Kategori')
                    ->badge()
                    ->searchable(),
                TextColumn::make('author_name')
                    ->label('Pengirim')
                    ->searchable(),
                TextColumn::make('author_age')
                    ->label('Usia')
                    ->formatStateUsing(fn ($state) => $state ? "{$state} th" : '-')
                    ->sortable(),
                TextColumn::make('author_region')
                    ->label('Kecamatan')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('reviewer.name')
                    ->label('Ditinjau Oleh')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reviewed_at')
                    ->label('Waktu Review')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Masuk Pada')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Tinjau / Moderasi'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
