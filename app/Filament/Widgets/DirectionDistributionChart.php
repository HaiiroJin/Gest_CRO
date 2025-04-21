<?php

namespace App\Filament\Widgets;

use App\Models\Direction;
use Filament\Widgets\ChartWidget;

class DirectionDistributionChart extends ChartWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super_admin');
    }
    protected static ?string $heading = 'Distribution des Fonctionnaires par Direction';

    protected static ?int $sort = 3;

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'font' => [
                            'size' => 10,
                        ],
                        'boxWidth' => 10,
                        'padding' => 5,
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => false,
                ],
                'y' => [
                    'display' => false,
                ],
            ],
        ];
    }

    protected function getData(): array
    {
        $directionData = Direction::withCount('fonctionnaires')
            ->orderBy('fonctionnaires_count', 'desc')
            ->limit(10)
            ->get();

        $total = $directionData->sum('fonctionnaires_count');
        $percentageData = $directionData->map(function($direction) use ($total) {
            $count = $direction->fonctionnaires_count;
            $percentage = round(($count / $total) * 100, 1);
            return [
                'count' => $count,
                'percentage' => $percentage
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Directions (%)',
                    'data' => $percentageData->pluck('percentage')->toArray(),
                    'backgroundColor' => [
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                        'rgba(255, 159, 64, 0.6)',
                        'rgba(199, 199, 199, 0.6)',
                        'rgba(83, 102, 255, 0.6)',
                        'rgba(40, 159, 64, 0.6)',
                        'rgba(210, 99, 132, 0.6)',
                    ],
                ],
            ],
            'labels' => $directionData->map(function($direction) use ($percentageData) {
                $data = $percentageData->firstWhere('count', $direction->fonctionnaires_count);
                return $direction->libelle . ' (' . $direction->fonctionnaires_count . ' - ' . $data['percentage'] . '%)';
            })->toArray(),
        ];
    }
}
