<?php

namespace App\Providers\Filament;

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Pages;
use Filament\Support\Colors\Color;
use App\Filament\Widgets\AttestationStatsWidget;
use App\Filament\Widgets\CongeStatsWidget;
use App\Filament\Widgets\FonctionnaireStatsWidget;
use App\Filament\Widgets\CorpsDistributionChart;
use App\Filament\Widgets\DirectionDistributionChart;
use App\Filament\Widgets\DivisionDistributionChart;
use App\Filament\Widgets\ServiceDistributionChart;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
        ->sidebarCollapsibleOnDesktop()
            ->default()
            ->id('admin')
            ->path('admin')
            ->authMiddleware([
                Authenticate::class,
            ])
            ->login()
            ->registration(false)
            ->passwordReset(false)
            ->emailVerification(false)
            ->profile()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->navigationGroups([
                'Gestion Fonctionnaires',
                'Gestion des Demandes',
                'Structure Organisationnelle',
                'Paramètrage',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                FonctionnaireStatsWidget::class,
                AttestationStatsWidget::class,
                CongeStatsWidget::class,
                CorpsDistributionChart::class,
                DirectionDistributionChart::class,
                DivisionDistributionChart::class,
                ServiceDistributionChart::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugin(
                FilamentShieldPlugin::make()
            );
    }
}
