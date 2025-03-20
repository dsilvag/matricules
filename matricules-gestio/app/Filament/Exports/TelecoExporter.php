<?php

namespace App\Filament\Exports;

use App\Models\Teleco;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TelecoExporter extends Exporter
{
    protected static ?string $model = Teleco::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('PERSCOD')->label('PERSCOD'),
            ExportColumn::make('NUMORDRE')->label('NUMORDRE'),
            ExportColumn::make('TIPCONTACTE')->label('TIPCONTACTE'),
            ExportColumn::make('CONTACTE')->label('CONTACTE'),
            ExportColumn::make('OBSERVACIONS')->label('OBSERVACIONS'),
            ExportColumn::make('STDUGR')->label('STDUGR'),
            ExportColumn::make('STDUMOD')->label('STDUMOD'),
            ExportColumn::make('STDDGR')->label('STDDGR'),
            ExportColumn::make('STDDMOD')->label('STDDMOD'),
            ExportColumn::make('STDHGR')->label('STDHGR'),
            ExportColumn::make('STDHMOD')->label('STDHMOD'),
            ExportColumn::make('VALDATA')->label('VALDATA'),
            ExportColumn::make('BAIXASW')->label('BAIXASW'),
        ];
    }
    public static function getCsvDelimiter(): string
    {
        return ';';
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your teleco export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
