<?php

namespace App\Filament\Resources\Complaints\Schemas;

use App\Enums\ComplaintStatus;
use App\Enums\ComplaintUrgency;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ComplaintForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Section::make('Informasi Tiket')
                    ->schema([
                        TextInput::make('ticket_number')
                            ->disabled()
                            ->dehydrated(false)
                            ->visibleOn(['edit', 'view']),
                        Select::make('status')
                            ->options(ComplaintStatus::class)
                            ->default('new')
                            ->required(),
                        Select::make('urgency')
                            ->options(ComplaintUrgency::class)
                            ->default('low')
                            ->required(),
                        Select::make('assigned_to')
                            ->relationship('assignee', 'name'),
                    ])->columns(2),
                \Filament\Forms\Components\Section::make('Data Pelapor')
                    ->schema([
                        TextInput::make('reporter_name'),
                        TextInput::make('reporter_contact'),
                        Toggle::make('is_anonymous')
                            ->required(),
                        TextInput::make('victim_relation'),
                        DateTimePicker::make('consent_at'),
                    ])->columns(2),
                \Filament\Forms\Components\Section::make('Detail Pengaduan')
                    ->schema([
                        TextInput::make('category_id'),
                        TextInput::make('general_location'),
                        Textarea::make('chronology')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
