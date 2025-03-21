<?php

namespace App\Filament\Widgets;

use App\Models\AttestationTravail;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AttestationStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $total = AttestationTravail::count();
        $enCours = AttestationTravail::where('status', 'en cours')->count();
        $signe = AttestationTravail::where('status', 'signé')->count();
        $rejete = AttestationTravail::where('status', 'rejeté')->count();

        return [
            Stat::make('Total Attestations de Travail', $total)
                ->description('Nombre total d\'attestations de travail')
                ->icon('heroicon-o-document-text')
                ->color('primary'),
            Stat::make('En Cours', $enCours)
                ->description('Attestations de travail en cours de traitement')
                ->icon('heroicon-o-clock')
                ->color('warning'),
            Stat::make('Signées', $signe)
                ->description('Attestations de travail signées')
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Rejetées', $rejete)
                ->description('Attestations de travail rejetées')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}
