<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->brandName('Portal KLA Konawe')
            ->brandLogo(asset('images/logo_konawe.png'))
            ->brandLogoHeight('3rem')
            ->navigationGroups([
                \Filament\Navigation\NavigationGroup::make('Layanan & Fasilitas Anak')
                    ->icon('heroicon-o-heart'),
                \Filament\Navigation\NavigationGroup::make('Informasi & Edukasi KLA')
                    ->icon('heroicon-o-newspaper'),
                \Filament\Navigation\NavigationGroup::make('Partisipasi Anak & Warga')
                    ->icon('heroicon-o-chat-bubble-left-right'),
                \Filament\Navigation\NavigationGroup::make('Gugus Tugas & Data KLA')
                    ->icon('heroicon-o-building-office-2'),
                \Filament\Navigation\NavigationGroup::make('Pengaturan Sistem')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                \App\Filament\Widgets\KlaStatsOverviewWidget::class,
                \App\Filament\Widgets\KlaPublicWebsiteGuideWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
