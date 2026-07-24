<?php

namespace App\Filament\Resources\ForumTopics\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ForumTopicForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                RichEditor::make('content')
                    ->required()
                    ->columnSpanFull(),
                Select::make('author_id')
                    ->relationship('author', 'name')
                    ->required(),
                TextInput::make('category'),
                Toggle::make('is_locked')
                    ->required(),
                Toggle::make('is_pinned')
                    ->required(),
            ]);
    }
}
