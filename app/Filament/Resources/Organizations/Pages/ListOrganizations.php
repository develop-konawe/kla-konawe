<?php

namespace App\Filament\Resources\Organizations\Pages;

use App\Filament\Resources\Organizations\OrganizationResource;
use App\Services\OrpegService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListOrganizations extends ListRecords
{
    protected static string $resource = OrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('syncOrpeg')
                ->label('Sinkronkan dari API Orpeg')
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Sinkronisasi Unit Organisasi / OPD')
                ->modalDescription('Aplikasi akan mengambil daftar Unit Organisasi terbaru dari API Orpeg Konawe dan menyimpannya ke database.')
                ->action(function (OrpegService $service) {
                    $result = $service->syncUnitsToDatabase();

                    if ($result['success']) {
                        Notification::make()
                            ->title('Sinkronisasi Berhasil')
                            ->body($result['message'])
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Gagal Sinkronisasi')
                            ->body($result['message'])
                            ->danger()
                            ->send();
                    }
                }),
            CreateAction::make(),
        ];
    }
}
