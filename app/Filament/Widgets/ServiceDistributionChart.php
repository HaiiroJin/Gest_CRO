<?php

namespace App\Filament\Widgets;

use App\Models\Service;
use Filament\Widgets\ChartWidget;

class ServiceDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Distribution des Fonctionnaires par Service';
    protected static ?int $sort = 5;

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
                    'columns' => 1,
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
        $serviceData = Service::withCount('fonctionnaires')
            ->orderBy('fonctionnaires_count', 'desc')
            ->limit(10)
            ->get();

        $total = $serviceData->sum('fonctionnaires_count');
        $percentageData = $serviceData->map(function($service) use ($total) {
            $count = $service->fonctionnaires_count;
            $percentage = round(($count / $total) * 100, 1);
            return [
                'count' => $count,
                'percentage' => $percentage
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Services (%)',
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
            'labels' => $serviceData->map(function($service) use ($percentageData) {
                $data = $percentageData->firstWhere('count', $service->fonctionnaires_count);
                return $service->libelle . ' (' . $service->fonctionnaires_count . ' - ' . $data['percentage'] . '%)';
            })->toArray(),
        ];
    }
}
