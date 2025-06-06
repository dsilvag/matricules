<?php

namespace App\Filament\Widgets;

use App\Models\Instance;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsInstanceOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $notificadesCount = Instance::where('is_notificat', true)->where('NUMEXP', '!=', 'PADRO')->count();
        $noNotificadesCount = Instance::where('is_notificat', false)->where('NUMEXP', '!=', 'PADRO')->count();
        $totalCount = Instance::where('NUMEXP', '!=', 'PADRO')->count();

        $notificadesDesc = $notificadesCount == 0 ? 'No hi ha instàncies notificades' : 'Total d\'instàncies notificades';

        $noNotificadesIcon = $noNotificadesCount == 0 ? 'heroicon-m-hand-thumb-up' : 'heroicon-m-exclamation-triangle';
        $noNotificadesDesc = $noNotificadesCount == 0 ? 'No hi ha instàncies pendents de notificació ' : 'Hi ha instàncies pendents de notificació ';
        $noNotificadesColor = $noNotificadesCount == 0
            ? 'success'
            : 'danger';

        return [
            Stat::make('Instàncies notificades', $notificadesCount)
                ->description($notificadesDesc)
                ->color('success')
                ->url('/admin/instances?notificades=true&tableFilters[no_padro][isActive]=true'),

            Stat::make('Instàncies no notificades', $noNotificadesCount)
                ->description($noNotificadesDesc)
                ->descriptionIcon($noNotificadesIcon)
                ->color($noNotificadesColor)
                ->url('/admin/instances'),

            Stat::make('Total Instàncies', $totalCount)->url('/admin/instances?tableFilters[no_padro][isActive]=false'),
                //->description('Total d\'instàncies'),
        ];
    }
}
