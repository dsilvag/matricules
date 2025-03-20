<?php

namespace App\Filament\Imports;

use App\Models\Person;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class PersonImporter extends Importer
{
    protected static ?string $model = Person::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('PAISCOD')
                ->numeric()
                ->rules(['integer', 'nullable']),
            ImportColumn::make('PROVCOD')
                ->numeric()
                ->rules(['integer', 'nullable']),
            ImportColumn::make('MUNICOD')
                ->numeric()
                ->rules(['integer', 'nullable']),
            ImportColumn::make('PERSNOM')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('PERSCOG1')
                ->rules(['max:25', 'nullable']),
            ImportColumn::make('PERSCOG2')
                ->rules(['max:25', 'nullable']),
            ImportColumn::make('PERSPAR1')
                ->rules(['max:6', 'nullable']),
            ImportColumn::make('PERSPAR2')
                ->rules(['max:6', 'nullable']),
            ImportColumn::make('NIFNUMP')
                ->rules(['max:10', 'nullable']),
            ImportColumn::make('NIFNUM')
                ->rules(['max:8', 'nullable']),
            ImportColumn::make('NIFDC')
                ->rules(['max:1', 'nullable']),
            ImportColumn::make('NIFSW')
                ->requiredMapping()
                ->rules(['required', 'max:1']),
            ImportColumn::make('PERSDCONNIF')
                ->rules(['max:8', 'nullable']),
            ImportColumn::make('PERSDCANNIF')
                ->rules(['max:8', 'nullable']),
            ImportColumn::make('PERSNACIONA')
                ->numeric()
                ->rules(['integer', 'nullable']),
            ImportColumn::make('PERSPASSPORT')
                ->rules(['max:20', 'nullable']),
            ImportColumn::make('PERSNDATA')
                ->rules(['max:8', 'nullable']),
            ImportColumn::make('PERSPARE')
                ->rules(['max:20', 'nullable']),
            ImportColumn::make('PERSMARE')
                ->rules(['max:20', 'nullable']),
            ImportColumn::make('PERSSEXE')
                ->rules(['max:1', 'nullable']),
            ImportColumn::make('PERSSW')
                ->rules(['max:1', 'nullable']),
            ImportColumn::make('IDIOCOD')
                ->rules(['max:1', 'nullable']),
            ImportColumn::make('PERSVNUM')
                ->numeric()
                ->rules(['integer', 'nullable']),
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
            ImportColumn::make('CONTVNUM')
                ->numeric()
                ->rules(['integer', 'nullable']),
            ImportColumn::make('NIFORIG')
                ->rules(['max:10', 'nullable']),
            ImportColumn::make('PERSCODOLD')
                ->rules(['max:30', 'nullable']),
            ImportColumn::make('VALDATA')
                ->rules(['max:8', 'nullable']),
            ImportColumn::make('BAIXASW')
                ->rules(['max:1', 'nullable']),
            ImportColumn::make('GUID')
                ->rules(['max:32', 'nullable']),
        ];
    }

    public function resolveRecord(): ?Person
    {
        // return Person::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Person();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your person import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
