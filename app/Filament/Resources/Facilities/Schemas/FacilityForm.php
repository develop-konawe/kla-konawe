<?php

namespace App\Filament\Resources\Facilities\Schemas;

use App\Enums\ContentStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FacilityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('organization_id')
                    ->relationship('organization', 'name'),
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                RichEditor::make('description')
                    ->columnSpanFull(),
                \Filament\Forms\Components\FileUpload::make('image')
                    ->image()
                    ->directory('facilities')
                    ->columnSpanFull(),
                TextInput::make('category'),
                Textarea::make('address')
                    ->columnSpanFull(),
                TextInput::make('coordinates'),
                Select::make('status')
                    ->options(ContentStatus::class)
                    ->default('draft')
                    ->required(),
                Toggle::make('is_child_friendly')
                    ->required(),
                DateTimePicker::make('verified_at'),
                Select::make('verified_by')
                    ->relationship('verifier', 'name'),
            ]);
    }
}
