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
            ImportColumn::make('CARCOD')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
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
                ->rules(['max:5', 'nullable']),
            ImportColumn::make('CARPAR')
                ->rules(['max:6', 'nullable']),
            ImportColumn::make('CARDESC')
                ->requiredMapping()
                ->rules(['required', 'max:50']),
            ImportColumn::make('CARDESC2')
                ->rules(['max:25', 'nullable']),
            ImportColumn::make('STDUGR')
                ->rules(['max:20', 'nullable']),
            ImportColumn::make('STDUMOD')
                ->rules(['max:20', 'nullable']),
            ImportColumn::make('STDDGR')
                ->rules(['max:8', 'nullable']),
            ImportColumn::make('STDDMOD')
                ->rules(['max:8', 'nullable']),
            ImportColumn::make('STDHGR')
                ->rules(['max:6', 'nullable']),
            ImportColumn::make('STDHMOD')
                ->rules(['max:6', 'nullable']),
            ImportColumn::make('VALDATA')
                ->rules(['max:8', 'nullable']),
            ImportColumn::make('BAIXASW')
                ->rules(['max:1', 'nullable']),
            ImportColumn::make('INICIFI')
                ->rules(['max:4000', 'nullable']),
            ImportColumn::make('OBSERVACIONS')
                ->rules(['max:4000', 'nullable']),
            ImportColumn::make('ORGCOD')
                ->rules(['max:4', 'nullable']),
            ImportColumn::make('ORGDATA')
                ->rules(['max:8', 'nullable']),
            ImportColumn::make('ORGOBS')
                ->rules(['max:4000', 'nullable']),
            ImportColumn::make('PLACA')
                ->rules(['max:255', 'nullable']),
            ImportColumn::make('GENERIC')
                ->rules(['max:50', 'nullable']),
            ImportColumn::make('ESPECIFIC')
                ->rules(['max:50', 'nullable']),
            ImportColumn::make('TEMATICA')
                ->rules(['max:50', 'nullable']),
            ImportColumn::make('SEXE')
                ->rules(['max:1', 'nullable']),
            ImportColumn::make('LOCAL')
                ->rules(['max:1', 'nullable']),
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
