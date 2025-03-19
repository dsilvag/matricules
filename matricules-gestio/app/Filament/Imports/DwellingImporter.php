<?php

namespace App\Filament\Imports;

use App\Models\Dwelling;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class DwellingImporter extends Importer
{
    protected static ?string $model = Dwelling::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('PAISCOD')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('PROVCOD')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('MUNICOD')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('CARCOD')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('PSEUDOCOD')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('GISCOD')
                ->rules(['max:255']),
            ImportColumn::make('DOMNUM')
                ->rules(['max:4']),
            ImportColumn::make('DOMBIS')
                ->rules(['max:1']),
            ImportColumn::make('DOMNUM2')
                ->rules(['max:4']),
            ImportColumn::make('DOMBIS2')
                ->rules(['max:1']),
            ImportColumn::make('DOMESC')
                ->rules(['max:2']),
            ImportColumn::make('DOMPIS')
                ->rules(['max:3']),
            ImportColumn::make('DOMPTA')
                ->rules(['max:4']),
            ImportColumn::make('DOMBLOC')
                ->rules(['max:2']),
            ImportColumn::make('DOMPTAL')
                ->rules(['max:2']),
            ImportColumn::make('DOMKM')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('DOMHM')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('DOMTLOC')
                ->rules(['max:1']),
            ImportColumn::make('APCORREUS')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('DOMTIP')
                ->rules(['max:4']),
            ImportColumn::make('DOMOBS')
                ->rules(['max:256']),
            ImportColumn::make('VALDATA')
                ->rules(['max:8']),
            ImportColumn::make('BAIXASW')
                ->rules(['max:1']),
            ImportColumn::make('STDAPLADD')
                ->rules(['max:5']),
            ImportColumn::make('STDAPLMOD')
                ->rules(['max:5']),
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
            ImportColumn::make('DOMCP')
                ->rules(['max:20']),
            ImportColumn::make('X')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('Y')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('POBLDESC')
                ->rules(['max:50']),
            ImportColumn::make('GID')
                ->rules(['max:32']),
            ImportColumn::make('SWREVISAT')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('REFCADASTRAL')
                ->rules(['max:255']),
            ImportColumn::make('SWPARE')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('CIV')
                ->rules(['max:24']),
        ];
    }

    public function resolveRecord(): ?Dwelling
    {
        // return Dwelling::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Dwelling();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your dwelling import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
