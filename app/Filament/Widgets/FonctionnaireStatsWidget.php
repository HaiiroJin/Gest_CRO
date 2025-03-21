<?php

namespace App\Filament\Widgets;

use App\Models\Fonctionnaire;
use App\Models\Corps;
use App\Models\Direction;
use App\Models\Division;
use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FonctionnaireStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $total = Fonctionnaire::count();
        $totalCorps = Corps::count();
        $totalDirections = Direction::count();
        $totalDivisions = Division::count();
        $totalServices = Service::count();

        return [
            Stat::make('Total Fonctionnaires', $total)
                ->description('Nombre total de fonctionnaires')
                ->icon('heroicon-o-user-group')
                ->color('primary'),
            Stat::make('Corps', $totalCorps)
                ->description('Nombre total de corps')
                ->icon('heroicon-o-briefcase')
                ->color('success'),
            Stat::make('Directions', $totalDirections)
                ->description('Nombre total de directions')
                ->icon('heroicon-o-building-office-2')
                ->color('info'),
            Stat::make('Divisions', $totalDivisions)
                ->description('Nombre total de divisions')
                ->icon('heroicon-o-squares-2x2')
                ->color('warning'),
            Stat::make('Services', $totalServices)
                ->description('Nombre total de services')
                ->icon('heroicon-o-server')
                ->color('danger'),
        ];
    }
}
