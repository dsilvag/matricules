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
                ->rules(['max:4']),
            ImportColumn::make('CONTACTE')
                ->rules(['max:255']),
            ImportColumn::make('OBSERVACIONS')
                ->rules(['max:255']),
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
        ];
    }

    public function resolveRecord(): ?Teleco
    {
        // return Teleco::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

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
