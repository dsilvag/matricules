<?php

namespace App\Filament\Resources\StreetBarriVellResource\Pages;

use App\Filament\Resources\StreetBarriVellResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStreetBarriVells extends ListRecords
{
    protected static string $resource = StreetBarriVellResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Crear carrer barri vell'),
        ];
    }
}
