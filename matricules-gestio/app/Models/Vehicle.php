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
                    ->danger()
                    ->send();
            }
        });
    }
}
