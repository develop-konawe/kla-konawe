<?php

namespace App\Filament\Resources\Aspirations\Schemas;

use App\Enums\AspirationStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Schema;

class AspirationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                RichEditor::make('content')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('author_name'),
                TextInput::make('author_age')
                    ->numeric(),
                TextInput::make('author_region'),
                Select::make('status')
                    ->options(AspirationStatus::class)
                    ->default('draft')
                    ->required(),
                TextInput::make('category'),
                Select::make('reviewed_by')
                    ->relationship('reviewer', 'name'),
                DateTimePicker::make('reviewed_at'),
            ]);
    }
}
