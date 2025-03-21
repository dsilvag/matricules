<?php

namespace App\Filament\Imports;

use App\Models\Teleco;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class TelecoImporter extends Importer
{
    protected static ?string $model = Teleco::class;


    public static function getColumns(): array
    {
        return [
            ImportColumn::make('PERSCOD')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('NUMORDRE')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('TIPCONTACTE')
                ->rules(['max:4', 'nullable']),
            ImportColumn::make('CONTACTE')
                ->rules(['max:255', 'nullable']),
            ImportColumn::make('OBSERVACIONS')
                ->rules(['max:255', 'nullable']),
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
        ];
    }

    public function resolveRecord(): ?Teleco
    {
        //mirem si el perscod existeix
        $personExists = \DB::table('people')->where('PERSCOD', $this->data['PERSCOD'])->exists();
    
        // Si no existeix la persona amb el perscod que diu mostrem un error en la row
        if (!$personExists) {
            throw new RowImportFailedException('La persona amb el codi '. $this->data['PERSCOD'] . ' no existeix en la taula persones.' );
        }
        
        //Mirem si el perscod i el numordre existeixen (per si s'esta duplicant un teleco)
        $telecoExists = Street::where('PERSCOD', $this->data['PERSCOD'])->where('NUMORDRE', $this->data['NUMORDRE'])->exists();

        //si s'esta duplicant mostrem error
        if ($telecoExists) {
            throw new RowImportFailedException('El PERSCOD i el NUMORDRE'. $this->data['PERSCOD'] . ' ' . $this->data['NUMORDRE'] . ' ja existeixen, estan duplicats.');
        }

        return new Teleco();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your teleco import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
