<?php

namespace App\Filament\Resources\Aspirations\Pages;

use App\Filament\Resources\Aspirations\AspirationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAspirations extends ListRecords
{
    protected static string $resource = AspirationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
