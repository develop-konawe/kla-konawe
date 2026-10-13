<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Enums\ContentStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Schema;

class ServiceForm
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
                    ->directory('services')
                    ->columnSpanFull(),
                TextInput::make('type'),
                TextInput::make('contact_info'),
                TextInput::make('working_hours'),
                Select::make('status')
                    ->options(ContentStatus::class)
                    ->default('draft')
                    ->required(),
            ]);
    }
}
