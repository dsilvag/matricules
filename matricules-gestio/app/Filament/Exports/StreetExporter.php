<?php

namespace App\Filament\Exports;

use App\Models\Street;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class StreetExporter extends Exporter
{
    protected static ?string $model = Street::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('PAISCOD')->label('PAISCOD'),
            ExportColumn::make('PROVCOD')->label('PROVCOD'),
            ExportColumn::make('MUNICOD')->label('MUNICOD'),
            ExportColumn::make('CARCOD')->label('CARCOD'),
            ExportColumn::make('CARSIG')->label('CARSIG'),
            ExportColumn::make('CARPAR')->label('CARPAR'),
            ExportColumn::make('CARDESC')->label('CARDESC'),
            ExportColumn::make('CARDESC2')->label('CARDESC2'),
            ExportColumn::make('STDUGR')->label('STDUGR'),
            ExportColumn::make('STDUMOD')->label('STDUMOD'),
            ExportColumn::make('STDDGR')->label('STDDGR'),
            ExportColumn::make('STDDMOD')->label('STDDMOD'),
            ExportColumn::make('STDHGR')->label('STDHGR'),
            ExportColumn::make('STDHMOD')->label('STDHMOD'),
            ExportColumn::make('VALDATA')->label('VALDATA'),
            ExportColumn::make('BAIXASW')->label('BAIXASW'),
            ExportColumn::make('INICIFI')->label('INICIFI'),
            ExportColumn::make('OBSERVACIONS')->label('OBSERVACIONS'),
            ExportColumn::make('ORGCOD')->label('ORGCOD'),
            ExportColumn::make('ORGDATA')->label('ORGDATA'),
            ExportColumn::make('ORGOBS')->label('ORGOBS'),
            ExportColumn::make('PLACA')->label('PLACA'),
            ExportColumn::make('GENERIC')->label('GENERIC'),
            ExportColumn::make('ESPECIFIC')->label('ESPECIFIC'),
            ExportColumn::make('TEMATICA')->label('TEMATICA'),
            ExportColumn::make('SEXE')->label('SEXE'),
            ExportColumn::make('LOCAL')->label('LOCAL'),
        ];
    }
    public static function getCsvDelimiter(): string
    {
        return ';';
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your street export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
