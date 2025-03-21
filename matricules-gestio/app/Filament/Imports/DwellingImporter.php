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
            ImportColumn::make('DOMCOD')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('PAISCOD')
                ->numeric()
                ->rules(['integer', 'nullable']),
            ImportColumn::make('PROVCOD')
                ->numeric()
                ->rules(['integer', 'nullable']),
            ImportColumn::make('MUNICOD')
                ->numeric()
                ->rules(['integer', 'nullable']),
            ImportColumn::make('CARCOD')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('PSEUDOCOD')
                ->numeric()
                ->rules(['integer', 'nullable']),
            ImportColumn::make('GISCOD')
                ->rules(['max:255', 'nullable']),
            ImportColumn::make('DOMNUM')
                ->rules(['max:4', 'nullable']),
            ImportColumn::make('DOMBIS')
                ->rules(['max:1', 'nullable']),
            ImportColumn::make('DOMNUM2')
                ->rules(['max:4', 'nullable']),
            ImportColumn::make('DOMBIS2')
                ->rules(['max:1', 'nullable']),
            ImportColumn::make('DOMESC')
                ->rules(['max:2', 'nullable']),
            ImportColumn::make('DOMPIS')
                ->rules(['max:3', 'nullable']),
            ImportColumn::make('DOMPTA')
                ->rules(['max:4', 'nullable']),
            ImportColumn::make('DOMBLOC')
                ->rules(['max:2', 'nullable']),
            ImportColumn::make('DOMPTAL')
                ->rules(['max:2', 'nullable']),
            ImportColumn::make('DOMKM')
                ->numeric()
                ->rules(['integer', 'nullable']),
            ImportColumn::make('DOMHM')
                ->numeric()
                ->rules(['integer', 'nullable']),
            ImportColumn::make('DOMTLOC')
                ->rules(['max:1', 'nullable']),
            ImportColumn::make('APCORREUS')
                ->numeric()
                ->rules(['integer', 'nullable']),
            ImportColumn::make('DOMTIP')
                ->rules(['max:4', 'nullable']),
            ImportColumn::make('DOMOBS')
                ->rules(['max:256', 'nullable']),
            ImportColumn::make('VALDATA')
                ->rules(['max:8', 'nullable']),
            ImportColumn::make('BAIXASW')
                ->rules(['max:1', 'nullable']),
            ImportColumn::make('STDAPLADD')
                ->rules(['max:5', 'nullable']),
            ImportColumn::make('STDAPLMOD')
                ->rules(['max:5', 'nullable']),
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
            ImportColumn::make('DOMCP')
                ->rules(['max:20', 'nullable']),
            ImportColumn::make('X')
                ->numeric()
                ->rules(['integer', 'nullable']),
            ImportColumn::make('Y')
                ->numeric()
                ->rules(['integer', 'nullable']),
            ImportColumn::make('POBLDESC')
                ->rules(['max:50', 'nullable']),
            ImportColumn::make('GUID')
                ->rules(['max:32', 'nullable']),
            ImportColumn::make('SWREVISAT')
                ->numeric()
                ->rules(['integer', 'nullable']),
            ImportColumn::make('REFCADASTRAL')
                ->rules(['max:255', 'nullable']),
            ImportColumn::make('SWPARE')
                ->numeric()
                ->rules(['integer', 'nullable']),
            ImportColumn::make('CIV')
                ->rules(['max:24', 'nullable']),
        ];
    }

    public function resolveRecord(): ?Dwelling
    {
        $paisCod = $this->data['PAISCOD'] ?? null;
        $provCod = $this->data['PROVCOD'] ?? null;
        $muniCod = $this->data['MUNICOD'] ?? null;

        // Comprovar país, província i municipi
        if ($paisCod != 108 || $provCod != 17 || $muniCod != 15) {
            throw new RowImportFailedException('Els valors de PAISCOD, PROVCOD y MUNICOD no són vàlids. '.$paisCod . ' ' . $provCod . ' ' . $muniCod);
        }

        //mirem si el carcod existeix
        $streetExists = \DB::table('streets')->where('CARCOD', $this->data['CARCOD'])->exists();
    
        // Si no existeix mostrem un error en la row
        if (!$streetExists) {
            throw new RowImportFailedException('El carrer amb el codi '.$this->data['CARCOD'] . ' no existeix en la taula carrers.' );
        }

        //mirem si el domicili ja existeix
        $domCodExists = Dwelling::where('DOMCOD', $this->data['DOMCOD'])->exists();

        if ($domCodExists) {
            throw new RowImportFailedException('El DOMCOD '. $this->data['DOMCOD'] . ' ja existeix, està duplicat.' );
        }

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
