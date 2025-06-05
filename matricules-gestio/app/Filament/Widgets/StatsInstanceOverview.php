<?php

namespace App\Filament\Widgets;

use App\Models\Instance;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsInstanceOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $notificadesCount = Instance::where('is_notificat', true)->count();
        $noNotificadesCount = Instance::where('is_notificat', false)->count();
        $totalCount = Instance::count();

        $notificadesDesc = $notificadesCount == 0 ? 'No hi ha instàncies notificades' : 'Total d\'instàncies notificades';

        $noNotificadesIcon = $noNotificadesCount == 0 ? 'heroicon-m-hand-thumb-up' : 'heroicon-m-exclamation-triangle';
        $noNotificadesDesc = $noNotificadesCount == 0 ? 'No hi ha instàncies pendents de notificació ' : 'Hi ha instàncies pendents de notificació ';
        $noNotificadesColor = $noNotificadesCount == 0
            ? 'success'
            : 'danger';

        return [
            Stat::make('Instàncies notificades', $notificadesCount)
                ->description($notificadesDesc)
                ->color('success'),

            Stat::make('Instàncies no notificades', $noNotificadesCount)
                ->description($noNotificadesDesc)
                ->descriptionIcon($noNotificadesIcon)
                ->color($noNotificadesColor),

            Stat::make('Total Instàncies', $totalCount)
                //->description('Total d\'instàncies'),
        ];
    }
}
