<?php

namespace App\Filament\Imports;

use App\Models\Street;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;

class StreetImporter extends Importer
{
    protected static ?string $model = Street::class;

    //Comptadors estats dels camps
    protected static $modified = 0;
    protected static $created = 0;
    protected static $invalid = 0;

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
            ImportColumn::make('CARCOD')
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
        $paisCod = $this->data['PAISCOD'] ?? null;
        $provCod = $this->data['PROVCOD'] ?? null;
        $muniCod = $this->data['MUNICOD'] ?? null;

        // Comprovar país, província i municipi
        if ($paisCod != 108 || $provCod != 17 || $muniCod != 15) {
            self::$invalid++;
            return null;
        }

        $streetExists =  Street::where('CARCOD', $this->data['CARCOD'])->exists();
        if ($streetExists) {
            self::$modified++; // es modificarà un registre existent
        } else {
            self::$created++; // es crearà un nou registre
        }

        return Street::firstOrNew([
            'CARCOD' => $this->data['CARCOD'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your street import has completed. ' . number_format(self::$created) . ' ' . str('street')->plural(self::$created) . ' created, ' . number_format(self::$modified) . ' ' . str('street')->plural(self::$modified) . ' modified.';

        if (self::$invalid > 0) {
            $body .= ' ' . number_format(self::$invalid) . ' ' . str('street')->plural(self::$invalid) . ' did not meet the requirements.';
        }

        return $body;
    }
}
