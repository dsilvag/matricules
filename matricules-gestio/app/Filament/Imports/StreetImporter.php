<?php

namespace App\Filament\Imports;

use App\Models\Street;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class StreetImporter extends Importer
{
    protected static ?string $model = Street::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('PAISCOD')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('PROVCOD')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('MUNICOD')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('CARSIG')
                ->rules(['max:5']),
            ImportColumn::make('CARPAR')
                ->rules(['max:6']),
            ImportColumn::make('CARDESC')
                ->requiredMapping()
                ->rules(['required', 'max:50']),
            ImportColumn::make('CARDESC2')
                ->rules(['max:25']),
            ImportColumn::make('STDUGR')
                ->rules(['max:20']),
            ImportColumn::make('STDUMOD')
                ->rules(['max:20']),
            ImportColumn::make('STDDGR')
                ->rules(['max:8']),
            ImportColumn::make('STDDMOD')
                ->rules(['max:8']),
            ImportColumn::make('STDHGR')
                ->rules(['max:6']),
            ImportColumn::make('STDHMOD')
                ->rules(['max:6']),
            ImportColumn::make('VALDATA')
                ->rules(['max:8']),
            ImportColumn::make('BAIXASW')
                ->rules(['max:1']),
            ImportColumn::make('INICIFI')
                ->rules(['max:4000']),
            ImportColumn::make('OBSERVACIONS')
                ->rules(['max:4000']),
            ImportColumn::make('ORGCOD')
                ->rules(['max:4']),
            ImportColumn::make('ORGDATA')
                ->rules(['max:8']),
            ImportColumn::make('ORGOBS')
                ->rules(['max:4000']),
            ImportColumn::make('PLACA')
                ->rules(['max:255']),
            ImportColumn::make('GENERIC')
                ->rules(['max:50']),
            ImportColumn::make('ESPECIFIC')
                ->rules(['max:50']),
            ImportColumn::make('TEMATICA')
                ->rules(['max:50']),
            ImportColumn::make('SEXE')
                ->rules(['max:1']),
            ImportColumn::make('LOCAL')
                ->rules(['max:1']),
        ];
    }

    public function resolveRecord(): ?Street
    {
        // return Street::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Street();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your street import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
