<?php

namespace App\Filament\Resources\StreetBarriVellResource\Pages;

use App\Filament\Resources\StreetBarriVellResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStreetBarriVell extends EditRecord
{
    protected static string $resource = StreetBarriVellResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
