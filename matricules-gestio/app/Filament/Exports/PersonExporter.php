<?php

namespace App\Filament\Exports;

use App\Models\Person;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PersonExporter extends Exporter
{
    protected static ?string $model = Person::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('PERSCOD')->label('PERSCOD'),
            ExportColumn::make('PAISCOD')->label('PAISCOD'),
            ExportColumn::make('PROVCOD')->label('PROVCOD'),
            ExportColumn::make('MUNICOD')->label('MUNICOD'),
            ExportColumn::make('PERSNOM')->label('PERSNOM'),
            ExportColumn::make('PERSCOG1')->label('PERSCOG1'),
            ExportColumn::make('PERSCOG2')->label('PERSCOG2'),
            ExportColumn::make('PERSPAR1')->label('PERSPAR1'),
            ExportColumn::make('PERSPAR2')->label('PERSPAR2'),
            ExportColumn::make('NIFNUMP')->label('NIFNUMP'),
            ExportColumn::make('NIFNUM')->label('NIFNUM'),
            ExportColumn::make('NIFDC')->label('NIFDC'),
            ExportColumn::make('NIFSW')->label('NIFSW'),
            ExportColumn::make('PERSDCONNIF')->label('PERSDCONNIF'),
            ExportColumn::make('PERSDCANNIF')->label('PERSDCANNIF'),
            ExportColumn::make('PERSNACIONA')->label('PERSNACIONA'),
            ExportColumn::make('PERSPASSPORT')->label('PERSPASSPORT'),
            ExportColumn::make('PERSNDATA')->label('PERSNDATA'),
            ExportColumn::make('PERSPARE')->label('PERSPARE'),
            ExportColumn::make('PERSMARE')->label('PERSMARE'),
            ExportColumn::make('PERSSEXE')->label('PERSSEXE'),
            ExportColumn::make('PERSSW')->label('PERSSW'),
            ExportColumn::make('IDIOCOD')->label('IDIOCOD'),
            ExportColumn::make('PERSVNUM')->label('PERSVNUM'),
            ExportColumn::make('STDAPLADD')->label('STDAPLADD'),
            ExportColumn::make('STDAPLMOD')->label('STDAPLMOD'),
            ExportColumn::make('STDUGR')->label('STDUGR'),
            ExportColumn::make('STDUMOD')->label('STDUMOD'),
            ExportColumn::make('STDDGR')->label('STDDGR'),
            ExportColumn::make('STDDMOD')->label('STDDMOD'),
            ExportColumn::make('STDHGR')->label('STDHGR'),
            ExportColumn::make('STDHMOD')->label('STDHMOD'),
            ExportColumn::make('CONTVNUM')->label('CONTVNUM'),
            ExportColumn::make('NIFORIG')->label('NIFORIG'),
            ExportColumn::make('PERSCODOLD')->label('PERSCODOLD'),
            ExportColumn::make('VALDATA')->label('VALDATA'),
            ExportColumn::make('BAIXASW')->label('BAIXASW'),
            ExportColumn::make('GUID')->label('GUID'),

        ];
    }
    public static function getCsvDelimiter(): string
    {
        return ';';
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your person export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
