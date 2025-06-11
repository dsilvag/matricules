<?php

namespace App\Filament\Resources\InstanceResource\Pages;

use App\Filament\Resources\InstanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInstance extends EditRecord
{
    protected static string $resource = InstanceResource::class;
    
    protected $listeners = ['refresh' => 'refreshForm','save' => 'saveForm'];

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function refreshForm()
    {
        $this->fillForm();
    }
    
    public function saveForm()
    {
        if($this->record->is_notificat)
        {
            \App\Models\Instance::sendErrorNotification('Error Notificat','No pots modificar una instància si ja ha estat notificada','is_notificat');
        }else {
            //Obtenir les dades del formulari
            $data = $this->form->getState();
            //Emplenar les dades del formulari
            $this->record->fill($data);
            //Saltar validació
            $this->record->skipValidation();
            //Guardar Formulari
            $this->record->save();
        }
    }
}
