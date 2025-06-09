<?php

namespace App\Filament\Widgets;
use App\Models\Vehicle;
use Filament\Widgets\ChartWidget;

class VehiclesActiusChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected static ?string $heading = 'Vehicles actius padro/no padro';

    protected function getData(): array
    {
        $padro = Vehicle::whereHas('instance', function ($query) {
                $query->where('RESNUME', 'PADRO');
            })
            ->where('DATAEXP', '>', now())
            ->where('DATAINICI', '<=', now())
            ->count();

            $noPadro = Vehicle::whereHas('instance', function ($query) {
                $query->where('RESNUME', '!=', 'PADRO');
            })
            ->where('DATAEXP', '>', now())
            ->where('DATAINICI', '<=', now())
            ->count();
        return [
            'datasets' => [
                [
                    'label' => 'Vehicles actius padro/no padro',
                    'data' => [$padro, $noPadro],
                    'backgroundColor' => ['#5dade2', '#f4d03f'],
                ],
            ],
            'labels' => ['Padro', 'No Padro'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
