<?php

namespace App\Filament\Exports;

use App\Models\Dwelling;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class DwellingExporter extends Exporter
{
    protected static ?string $model = Dwelling::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('DOMCOD')->label('DOMCOD'),
            ExportColumn::make('PAISCOD')->label('PAISCOD'),
            ExportColumn::make('PROVCOD')->label('PROVCOD'),
            ExportColumn::make('MUNICOD')->label('MUNICOD'),
            ExportColumn::make('CARCOD')->label('CARCOD'),
            ExportColumn::make('PSEUDOCOD')->label('PSEUDOCOD'),
            ExportColumn::make('GISCOD')->label('GISCOD'),
            ExportColumn::make('DOMNUM')->label('DOMNUM'),
            ExportColumn::make('DOMBIS')->label('DOMBIS'),
            ExportColumn::make('DOMNUM2')->label('DOMNUM2'),
            ExportColumn::make('DOMBIS2')->label('DOMBIS2'),
            ExportColumn::make('DOMESC')->label('DOMESC'),
            ExportColumn::make('DOMPIS')->label('DOMPIS'),
            ExportColumn::make('DOMPTA')->label('DOMPTA'),
            ExportColumn::make('DOMBLOC')->label('DOMBLOC'),
            ExportColumn::make('DOMPTAL')->label('DOMPTAL'),
            ExportColumn::make('DOMKM')->label('DOMKM'),
            ExportColumn::make('DOMHM')->label('DOMHM'),
            ExportColumn::make('DOMTLOC')->label('DOMTLOC'),
            ExportColumn::make('APCORREUS')->label('APCORREUS'),
            ExportColumn::make('DOMTIP')->label('DOMTIP'),
            ExportColumn::make('DOMOBS')->label('DOMOBS'),
            ExportColumn::make('VALDATA')->label('VALDATA'),
            ExportColumn::make('BAIXASW')->label('BAIXASW'),
            ExportColumn::make('STDAPLADD')->label('STDAPLADD'),
            ExportColumn::make('STDAPLMOD')->label('STDAPLMOD'),
            ExportColumn::make('STDUGR')->label('STDUGR'),
            ExportColumn::make('STDUMOD')->label('STDUMOD'),
            ExportColumn::make('STDDGR')->label('STDDGR'),
            ExportColumn::make('STDDMOD')->label('STDDMOD'),
            ExportColumn::make('STDHGR')->label('STDHGR'),
            ExportColumn::make('STDHMOD')->label('STDHMOD'),
            ExportColumn::make('DOMCP')->label('DOMCP'),
            ExportColumn::make('X')->label('X'),
            ExportColumn::make('Y')->label('Y'),
            ExportColumn::make('POBLDESC')->label('POBLDESC'),
            ExportColumn::make('GUID')->label('GUID'),
            ExportColumn::make('SWREVISAT')->label('SWREVISAT'),
            ExportColumn::make('REFCADASTRAL')->label('REFCADASTRAL'),
            ExportColumn::make('SWPARE')->label('SWPARE'),
            ExportColumn::make('CIV')->label('CIV'),
        ];
    }
    public static function getCsvDelimiter(): string
    {
        return ';';
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your dwelling export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
