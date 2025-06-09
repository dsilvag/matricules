<?php

namespace App\Filament\Widgets;

use App\Models\Instance;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class InstancesCreatedChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Instàncies creades per mes';

    protected function getData(): array
    {
        $months = collect(range(1, 12))->map(function ($month) {
            return Carbon::create()->month($month)->format('F'); // Creem una array amb els números dels mesos i obtenim el nom del mes
        });
        //Obtenim les dades del últim any
        $instanceCounts = Instance::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', now()->year)
            ->where('RESNUME', '!=', 'PADRO')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        // Map clau valor
        $data = $months->mapWithKeys(function ($monthName, $index) use ($instanceCounts) {
            $monthNumber = $index + 1;
            return [$monthName => $instanceCounts[$monthNumber] ?? 0];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Instàncies creades',
                    'data' => $data->values(),
                    'borderColor' => '#6366F1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.2)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $data->keys(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
