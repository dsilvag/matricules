<?php

namespace App\Filament\Widgets;
use App\Models\Vehicle;
use Filament\Widgets\ChartWidget;

class VehiclesActiusChart extends ChartWidget
{
    protected static ?int $sort = 6;

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
                'backgroundColor' => [
                    'rgba(93, 173, 226, 0.4)',
                    'rgba(244, 208, 63, 0.4)'  
                ],
                'borderColor' => [
                    'rgba(93, 173, 226, 1)',  
                    'rgba(244, 208, 63, 1)'  
                ],
                'borderWidth' => 2,
            ],
        ],
            'labels' => ['Padro', 'No Padro'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
