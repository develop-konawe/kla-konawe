<?php

namespace App\Filament\Resources\Statistics\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class StatisticsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Indikator KLA')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(45),
                TextColumn::make('category_id')
                    ->label('Klaster')
                    ->badge()
                    ->searchable(),
                TextColumn::make('year')
                    ->label('Tahun')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('value')
                    ->label('Nilai Capaian')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($record) => "{$record->value} " . ($record->unit ?? '')),
                TextColumn::make('source')
                    ->label('Sumber / OPD')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('region_id')
                    ->label('Wilayah')
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
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('view_public')
                    ->label('Lihat di Web')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('info')
                    ->tooltip('Buka bagian Capaian Indikator KLA di website publik')
                    ->url(fn () => url('/#indikator-kla'))
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
