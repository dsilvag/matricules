<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use App\Models\Instance;

class NotifiedInstancesChart extends ChartWidget
{
    protected static ?string $heading = null;
    protected static ?int $sort = 3;

    public function getHeading(): string
    {
        $label = env('FILTER_WIDGET') === 'setmana' ? 'per setmana' : 'per mes';
        return 'Instàncies notificades ' . $label;
    }

    protected function getData(): array
    {
        if (env('FILTER_WIDGET') === 'setmana') {
            // Agrupar per setmana (últimes 8 setmanes)
            $weeks = collect(range(0, 7))->map(function ($weekOffset) {
                return now()->copy()->subWeeks($weekOffset)->startOfWeek()->format('W/Y');
            })->reverse();

            $instanceCounts = Instance::selectRaw('YEARWEEK(updated_at, 1) as week, COUNT(*) as count')
                ->where('updated_at', '>=', now()->subWeeks(8))
                ->where('RESNUME', '!=', 'PADRO')
                ->where('is_notificat', '=', true)
                ->groupBy('week')
                ->orderBy('week')
                ->pluck('count', 'week');

            $data = $weeks->mapWithKeys(function ($weekKey) use ($instanceCounts) {
                $parts = explode('/', $weekKey);
                $formatted = $parts[1] . str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                return [$weekKey => $instanceCounts[$formatted] ?? 0];
            });

        } else {
            // Agrupar per mes (últim any)
            $months = collect(range(1, 12))->map(function ($month) {
                return Carbon::create()->month($month)->format('F');
            });

            $instanceCounts = Instance::selectRaw('MONTH(updated_at) as month, COUNT(*) as count')
                ->whereYear('updated_at', now()->year)
                ->where('RESNUME', '!=', 'PADRO')
                ->where('is_notificat', '=', true)
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
                    'label' => 'Instàncies notificades',
                    'data' => $data->values(),
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
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
