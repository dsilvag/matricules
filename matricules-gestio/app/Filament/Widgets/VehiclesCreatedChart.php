<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use App\Models\Vehicle;

class VehiclesCreatedChart extends ChartWidget
{
    protected static ?int $sort = 5;
    protected static ?string $heading = null;

    public function getHeading(): string
    {
        $filter = env('FILTER_WIDGET');

        if ($filter === 'setmana') {
            $label = 'per setmana';
        } elseif ($filter === 'dies') {
            $label = 'últims 15 dies';
        } else {
            $label = 'per mes';
        }

        return 'Matrícules creades ' . $label;
    }

    protected function getData(): array
    {
        if (env('FILTER_WIDGET') === 'setmana') {
            // Agrupar per setmana (últimes 8 setmanes)
            $weeks = collect(range(0, 7))->map(function ($weekOffset) {
                return now()->copy()->subWeeks($weekOffset)->startOfWeek()->format('W/Y');
            })->reverse();

            $vehicleCounts = Vehicle::selectRaw('YEARWEEK(vehicles.created_at, 1) as week, COUNT(*) as count')
                ->join('instances', 'vehicles.instance_id', '=', 'instances.id')
                ->where('vehicles.created_at', '>=', now()->subWeeks(8))
                ->where('instances.RESNUME', '!=', 'PADRO')
                ->groupBy('week')
                ->orderBy('week')
                ->pluck('count', 'week');

            $data = $weeks->mapWithKeys(function ($weekKey) use ($vehicleCounts) {
                $parts = explode('/', $weekKey);
                $formatted = $parts[1] . str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                return [$weekKey => $vehicleCounts[$formatted] ?? 0];
            });

        } elseif (env('FILTER_WIDGET') === 'dies') {
            // Agrupar per dies (últims 15 dies)
            $days = collect(range(0, 14))->map(function ($dayOffset) {
                return now()->copy()->subDays($dayOffset)->format('Y-m-d');
            })->reverse();

            $vehicleCounts = Vehicle::selectRaw('DATE(vehicles.created_at) as day, COUNT(*) as count')
                ->join('instances', 'vehicles.instance_id', '=', 'instances.id')
                ->where('vehicles.created_at', '>=', now()->subDays(15))
                ->where('instances.RESNUME', '!=', 'PADRO')
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('count', 'day');

            $data = $days->mapWithKeys(function ($day) use ($vehicleCounts) {
                return [$day => $vehicleCounts[$day] ?? 0];
            });

        } else {
            // Agrupar per mes (últim any)
            $months = collect(range(1, 12))->map(function ($month) {
                return Carbon::create()->month($month)->format('F');
            });

            $vehicleCounts = Vehicle::selectRaw('MONTH(vehicles.created_at) as month, COUNT(*) as count')
                ->join('instances', 'vehicles.instance_id', '=', 'instances.id')
                ->whereYear('vehicles.created_at', now()->year)
                ->where('instances.RESNUME', '!=', 'PADRO')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month');

            $data = $months->mapWithKeys(function ($monthName, $index) use ($vehicleCounts) {
                $monthNumber = $index + 1;
                return [$monthName => $vehicleCounts[$monthNumber] ?? 0];
            });
        }

        return [
            'datasets' => [
                [
                    'label' => 'Matrícules creades',
                    'data' => $data->values(),
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $data->keys(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
