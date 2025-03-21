<?php

namespace App\Filament\Widgets;

use App\Models\Corps;
use Filament\Widgets\ChartWidget;

class CorpsDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Distribution des Fonctionnaires par Corps';

    protected static ?int $sort = 2;

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
                    'columns' => 3,
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
        $corpsData = Corps::withCount('fonctionnaires')
            ->orderBy('fonctionnaires_count', 'desc')
            ->limit(10)
            ->get();

        $total = $corpsData->sum('fonctionnaires_count');
        $percentageData = $corpsData->map(function($corps) use ($total) {
            $count = $corps->fonctionnaires_count;
            $percentage = round(($count / $total) * 100, 1);
            return [
                'count' => $count,
                'percentage' => $percentage
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Corps (%)',
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
            'labels' => $corpsData->map(function($corps) use ($percentageData) {
                $data = $percentageData->firstWhere('count', $corps->fonctionnaires_count);
                return $corps->libelle . ' (' . $corps->fonctionnaires_count . ' - ' . $data['percentage'] . '%)';
            })->toArray(),
        ];
    }
}
