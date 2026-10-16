<?php

namespace App\Filament\Widgets;

use App\Models\Aspiration;
use App\Models\Facility;
use App\Models\Publication;
use App\Models\Service;
use App\Models\Statistic;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KlaStatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $servicesCount = Service::where('status', 'published')->count();
        $facilitiesCount = Facility::where('is_child_friendly', true)->count();
        $publicationsCount = Publication::where('status', 'published')->count();
        $indicatorsCount = Statistic::where('status', 'published')->count();
        $aspirationsPending = Aspiration::where('status', 'draft')->orWhere('status', 'pending')->count();
        $aspirationsTotal = Aspiration::count();

        return [
            Stat::make('Layanan Ramah Anak', $servicesCount)
                ->description('Aktif di portal publik (/layanan)')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('success'),

            Stat::make('Fasilitas Ramah Anak', $facilitiesCount)
                ->description('Terverifikasi ramah anak (/fasilitas)')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary'),

            Stat::make('Indikator Capaian KLA', $indicatorsCount)
                ->description('Tampil di Beranda & Publikasi')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),

            Stat::make('Publikasi & Dokumen KLA', $publicationsCount)
                ->description('Regulasi & panduan terbuka (/publikasi)')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('info'),

            Stat::make('Aspirasi Suara Anak', $aspirationsTotal)
                ->description($aspirationsPending > 0 ? "{$aspirationsPending} menunggu ditinjau" : 'Semua sudah ditinjau')
                ->descriptionIcon('heroicon-m-chat-bubble-bottom-center-text')
                ->color($aspirationsPending > 0 ? 'danger' : 'gray'),
        ];
    }
}

