<?php

namespace App\Filament\Resources\DwellingResource\Pages;

use App\Filament\Resources\DwellingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDwellings extends ListRecords
{
    protected static string $resource = DwellingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
