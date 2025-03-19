<?php

namespace App\Filament\Resources\TelecoResource\Pages;

use App\Filament\Resources\TelecoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTelecos extends ListRecords
{
    protected static string $resource = TelecoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
