<?php

namespace App\Filament\Resources\DwellingResource\Pages;

use App\Filament\Resources\DwellingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDwelling extends EditRecord
{
    protected static string $resource = DwellingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
