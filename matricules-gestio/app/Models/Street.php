<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Street extends Model
{
    use HasFactory;

    // Quan primaryKey no es id hem d'indicar
    protected $primaryKey = 'PAISPROVMUNICARCOD';

    protected $keyType = 'string';

    // No estem utilitzant auto increment en la primary key
    public $incrementing = false;

    protected $guarded = [];

    protected $fillable = [
        'PAISPROVMUNICARCOD',
        'PAISCOD',
        'PROVCOD',
        'MUNICOD',
        'CARCOD',
        'CARSIG',
        'CARPAR',
        'CARDESC',
        'CARDESC2',
        'STDUGR',
        'STDUMOD',
        'STDDGR',
        'STDDMOD',
        'STDHGR',
        'STDHMOD',
        'VALDATA',
        'BAIXASW',
        'INICIFI',
        'OBSERVACIONS',
        'ORGCOD',
        'ORGDATA',
        'ORGOBS',
        'PLACA',
        'GENERIC',
        'ESPECIFIC',
        'TEMATICA',
        'SEXE',
        'LOCAL',
    ];

    /**
     *  Get all of dwellings for the Street
     *
     *  @return HasMany<int, dwelling>
     */
    public function dwellings(): HasMany
    {
        return $this->hasMany(Dwelling::class);
    }

    public function getNomCarrerAttribute()
    {
        //return $this->CARCOD . ' ' . $this->CARSIG;
        return "{$this->CARSIG} {$this->CARDESC}";
    }
}
