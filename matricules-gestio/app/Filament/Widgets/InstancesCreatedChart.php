<?php

namespace App\Filament\Widgets;

use App\Models\Instance;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class InstancesCreatedChart extends ChartWidget
{
    protected static ?int $sort = 2;

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

        return 'Instàncies creades ' . $label;
    }

    protected function getData(): array
    {
        if (env('FILTER_WIDGET') === 'setmana') {
            // Agrupar per setmana (últimes 8 setmanes)
            $weeks = collect(range(0, 7))->map(function ($weekOffset) {
                return now()->copy()->subWeeks($weekOffset)->startOfWeek()->format('W/Y');
            })->reverse();

            $instanceCounts = Instance::selectRaw('YEARWEEK(created_at, 1) as week, COUNT(*) as count')
                ->where('created_at', '>=', now()->subWeeks(8))
                ->where('RESNUME', '!=', 'PADRO')
                ->groupBy('week')
                ->orderBy('week')
                ->pluck('count', 'week');

            $data = $weeks->mapWithKeys(function ($weekKey) use ($instanceCounts) {
                $parts = explode('/', $weekKey);
                $formatted = $parts[1] . str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                return [$weekKey => $instanceCounts[$formatted] ?? 0];
            });

        } elseif (env('FILTER_WIDGET') === 'dies') {
            // Agrupar per dies (últims 15 dies)
            $days = collect(range(0, 14))->map(function ($dayOffset) {
                return now()->copy()->subDays($dayOffset)->format('Y-m-d');
            })->reverse();

            $instanceCounts = Instance::selectRaw('DATE(created_at) as day, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(15))
                ->where('RESNUME', '!=', 'PADRO')
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('count', 'day');

            $data = $days->mapWithKeys(function ($day) use ($instanceCounts) {
                return [$day => $instanceCounts[$day] ?? 0];
            });
        } 
        
        else {
            // Agrupar per mes (últim any)
            $months = collect(range(1, 12))->map(function ($month) {
                return Carbon::create()->month($month)->format('F');
            });

            $instanceCounts = Instance::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', now()->year)
                ->where('RESNUME', '!=', 'PADRO')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month');

            $data = $months->mapWithKeys(function ($monthName, $index) use ($instanceCounts) {
                $monthNumber = $index + 1;
                return [$monthName => $instanceCounts[$monthNumber] ?? 0];
            });
        }

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
        return 'bar';
    }
}
