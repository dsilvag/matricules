<?php

namespace App\Filament\Resources\TelecoResource\Pages;

use App\Filament\Resources\TelecoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeleco extends EditRecord
{
    protected static string $resource = TelecoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
