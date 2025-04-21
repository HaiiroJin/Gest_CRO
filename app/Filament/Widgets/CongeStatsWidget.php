<?php

namespace App\Filament\Widgets;

use App\Models\Conge;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CongeStatsWidget extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super_admin');
    }

    protected function getStats(): array
    {
        $total = Conge::count();
        $enCours = Conge::where('status', 'en cours')->count();
        $signe = Conge::where('status', 'signée')->count();
        $rejete = Conge::where('status', 'rejetée')->count();

        return [
            Stat::make('Total Congés', $total)
                ->description('Nombre total de congés')
                ->icon('heroicon-o-calendar')
                ->color('primary'),
            Stat::make('En Cours', $enCours)
                ->description('Congés en cours de traitement')
                ->icon('heroicon-o-clock')
                ->color('warning'),
            Stat::make('Signés', $signe)
                ->description('Congés signés')
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Rejetés', $rejete)
                ->description('Congés rejetés')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}
