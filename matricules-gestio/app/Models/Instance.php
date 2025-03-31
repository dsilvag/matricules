<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class Instance extends Model
{
     // Indicar clau primaria
    protected $primaryKey = 'RESNUME';

    // No estem utilitzant auto increment en la primary key
    public $incrementing = false;

    protected $guarded=[];

    protected $fillable = [
        'RESNUME',
        'NUMEXP',
        'DECRETAT',
        'VALIDAT',
        'PERSCOD',
        'REPRCOD',
        'DOMCOD'
    ];

    /**
     *  Get all of vehicles for the instance
     *
     *  @return HasMany<int, vehicles>
     */
    /*
    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'instances_vehicle','RESNUME','MATRICULA')->withTimestamps();
    }

    */
    // Modelo Instance
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'instance_RESNUME', 'RESNUME');
    }

    public function person()
    {
        return $this->belongsTo(Person::class, 'PERSCOD', 'PERSCOD');
    }

    public function domicili()
    {
        return $this->belongsTo(Dwelling::class, 'DOMCOD', 'DOMCOD');
    }

    public function carrersBarriVell()
    {
        return $this->belongsToMany(StreetBarriVell::class, 'instance_street','RESNUME','CARCOD')->withTimestamps();
    }

    public static function booted(): void
    {
        static::creating(function ($record) {
            $resnume = $record->RESNUME;
            $url = "https://g5.banyoles.cat:443/GenesysWS/services/RegistreEntradaWS";
            $xmlData = <<<XML
            <request>
            <arg0>
                <aplicacio>MARTICULES</aplicacio>
                <nivell>9999</nivell>
                <usuari>robot</usuari>
                <numeroRegistreEntrada>$resnume</numeroRegistreEntrada>
            </arg0>
            </request>
            XML;
        });
    }
}
