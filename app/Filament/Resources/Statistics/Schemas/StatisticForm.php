<?php

namespace App\Filament\Resources\Statistics\Schemas;

use App\Enums\ContentStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StatisticForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('category_id')
                            ->label('Kategori (Opsional)'),
                        TextInput::make('region_id')
                            ->label('Wilayah (Opsional)'),
                    ])->columns(2),
                \Filament\Forms\Components\Section::make('Data')
                    ->schema([
                        TextInput::make('year')
                            ->required()
                            ->numeric(),
                        TextInput::make('value')
                            ->required()
                            ->numeric(),
                        TextInput::make('unit')
                            ->label('Satuan'),
                        TextInput::make('source')
                            ->label('Sumber Data'),
                    ])->columns(2),
                \Filament\Forms\Components\Section::make('Status & Metadata')
                    ->schema([
                        Select::make('status')
                            ->options(ContentStatus::class)
                            ->default('draft')
                            ->required(),
                        \Filament\Forms\Components\KeyValue::make('metadata_json')
                            ->label('Metadata Kustom')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
