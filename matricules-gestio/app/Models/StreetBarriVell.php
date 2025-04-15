<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class StreetBarriVell extends Model
{
    use HasFactory;

    // Indicar clau primaria
    protected $primaryKey = 'CARCOD';

   // No estem utilitzant auto increment en la primary key
    public $incrementing = false;

    protected $keyType = 'int';

   protected $guarded=[];

   protected $fillable = [
       'CARCOD',
    ];
    /**
     *  Get the Street 
     *
     *  @return BelongsTo<int, Street>
     */
    public function street()
    {
        return $this->belongsTo(Street::class, 'CARCOD','CARCOD');
    }

    public function getNomCarrerAttribute()
    {
        return $this->street ? $this->street->nom_carrer : null;
        //$this->street()->getNomCarrerAttribute();
    }

    public function instances()
    {
        return $this->belongsToMany(Instance::class, 'instance_street', 'CARCOD', 'RESNUME');
    }

    public function vehicles()
    {
        return $this->hasManyThrough(Vehicle::class, Instance::class, 'RESNUME', 'instance_RESNUME', 'CARCOD', 'RESNUME');
    }

    public static function obtenirLListaCotxes($record)
    {
        $instances = $record->instances;

        $allVehicles = collect();
        foreach ($instances as $instance) {
            //Si esta notificat te un decret favorable mirem els vehicles
            if($instance->is_notificat==true && $instance->VALIDAT=='FAVORABLE'){
                foreach ($instance->vehicles as $vehicle) {        
                    //si el vehicle no ha expirat l'afegim a l'array
                    if (now()->format('Y-m-d') <= $vehicle->DATAEXP) {
                        $vehicles = [
                            'MATRICULA' => $vehicle->MATRICULA,
                            'DATAINICI' => $vehicle->DATAINICI,
                            'DATAEXP' => $vehicle->DATAEXP,
                        ];
                        $allVehicles = $allVehicles->push($vehicles);
                    }
                }
            }
        }
        dd($allVehicles);       
    }
}
