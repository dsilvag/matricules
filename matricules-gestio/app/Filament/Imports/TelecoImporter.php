<?php

namespace App\Filament\Imports;

use App\Models\Teleco;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;

class TelecoImporter extends Importer
{
    protected static ?string $model = Teleco::class;

    //Comptadors estats dels camps
    protected static $modified = 0;
    protected static $created = 0;
    protected static $invalid = 0;

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
        set_time_limit(30);
        //mirem si el perscod existeix
        $personExists = \DB::table('people')->where('PERSCOD', $this->data['PERSCOD'])->exists();
    
        // Si no existeix la persona amb el perscod que diu mostrem un error en la row
        if (!$personExists) {
            self::$invalid++;
            throw new RowImportFailedException('La persona amb el codi '. $this->data['PERSCOD'] . ' no existeix en la taula persones.' );
        }

        $telecoExists = Teleco::where('PERSCOD', $this->data['PERSCOD'])->where('NUMORDRE', $this->data['NUMORDRE'])->exists();
        
        if ($telecoExists) {
            self::$modified++; // es modificarà un registre existent
        } else {
            self::$created++; // es crearà un nou registre
        }
        
        return Teleco::firstOrNew([
            'PERSCOD' => $this->data['PERSCOD'],
            'NUMORDRE' => $this->data['NUMORDRE'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your teleco import has completed. ' . number_format(self::$created) . ' ' . str('teleco')->plural(self::$created) . ' created, ' . number_format(self::$modified) . ' ' . str('teleco')->plural(self::$modified) . ' modified.';

        if (self::$invalid > 0) {
            $body .= ' ' . number_format(self::$invalid) . ' ' . str('teleco')->plural(self::$invalid) . ' did not meet the requirements.';
        }

        return $body;
    }
}
