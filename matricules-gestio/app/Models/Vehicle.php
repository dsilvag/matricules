<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Filament\Notifications\Notification;

class Vehicle extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'MATRICULA',
        'DATAINICI',
        'DATAEXP',
        'DOMCOD',
        'instance_RESNUME'
    ];

   /**
     *  Get the Instance 
     *
     *  @return BelongsTo<int, Instance>
     */
    /*
    public function instance()
    {
        return $this->belongsTo(Instance::class, 'instances_vehicle','RESNUME','MATRICULA');
    }
        */
    public function instance()
    {
        return $this->belongsTo(Instance::class, 'instance_RESNUME', 'RESNUME');
    }
    public static function booted(): void
    {   
        static::creating(function ($record) {
            //Mirar data inici i data fi matricules
            $existingVehicle = \App\Models\Vehicle::where('MATRICULA', $record->MATRICULA)->first();
            if ($existingVehicle){
                Notification::make()
                    ->title('Vehicle duplicat')
                    ->body('Vehicle ja existent ves a consultar-lo')
                    ->warning()
                    ->send();
            }
            if ($record->instance_RESNUME) {
                $instance = \App\Models\Instance::find($record->instance_RESNUME);
                
                if ($instance) {
                    if (is_null($instance->data_inici) || is_null($instance->data_fi)) {
                        \App\Models\Instance::sendErrorNotification('Error de instancia','Cal seleccionar un motiu i fer clic al botó de guardar. Assegura\'t que els camps data inici i data fi continguin un valor','data_inici');
                    }
                    $record->DATAINICI = $instance->data_inici;
                    $record->DATAEXP = $instance->data_fi;
                }
            }
        });
    }
}
