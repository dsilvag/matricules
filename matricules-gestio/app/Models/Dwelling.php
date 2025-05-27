<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dwelling extends Model
{
    use HasFactory;

     // Indicar clau primaria
     protected $primaryKey = 'DOMCOD';

    // No estem utilitzant auto increment en la primary key
     public $incrementing = false;
 
     protected $keyType = 'int';

    protected $guarded=[];

    protected $fillable = [
        'PAISPROVMUNICARCOD',
        'DOMCOD',
        'PAISCOD',
        'PROVCOD',
        'MUNICOD',
        'CARCOD',
        'PSEUDOCOD',
        'GISCOD',
        'DOMNUM',
        'DOMBIS',
        'DOMNUM2',
        'DOMBIS2',
        'DOMESC',
        'DOMPIS',
        'DOMPTA',
        'DOMBLOC',
        'DOMPTAL',
        'DOMKM',
        'DOMHM',
        'DOMTLOC',
        'APCORREUS',
        'DOMTIP',
        'DOMOBS',
        'VALDATA',
        'BAIXASW',
        'STDAPLADD',
        'STDAPLMOD',
        'STDUGR',
        'STDUMOD',
        'STDDGR',
        'STDDMOD',
        'STDHGR',
        'STDHMOD',
        'DOMCP',
        'X',
        'Y',
        'POBLDESC',
        'GUID',
        'SWREVISAT',
        'REFCADASTRAL',
        'SWPARE',
        'CIV',
    ];

    /**
     *  Get the Street 
     *
     *  @return BelongsTo<int, Street>
     */
    public function street()
    {
        return $this->belongsTo(Street::class, 'PAISPROVMUNICARCOD','PAISPROVMUNICARCOD');
    }

    public function getNomHabitatgeAttribute()
    {
        $parts = [
            //Mira si es null es l'ho mateix que fer $this->street ? $this->street->nom_carrer : null
            optional($this->street)->nom_carrer,
            $this->DOMNUM,
            $this->DOMBIS,
            $this->DOMNUM2,
            $this->DOMBIS2,
            $this->DOMESC,
            $this->DOMPIS,
            $this->DOMPTA,
            $this->DOMBLOC,
            $this->DOMPTAL,
            $this->DOMKM,
            $this->DOMHM,
        ];

        // Elimina els valors nulls o buits i ajunta amb espais
        return collect($parts)
            ->filter(fn($part) => filled($part))
            ->implode(' ');
    }/*
    public function getNomHabitatgeAttribute()
    {
        return $this->street->nom_carrer . " {$this->DOMNUM} {$this->DOMBIS} {$this->DOMNUM2} {$this->DOMBIS2} {$this->DOMESC} {$this->DOMPIS} {$this->DOMPTA} {$this->DOMBLOC} {$this->DOMPTAL} {$this->DOMKM} {$this->DOMHM}";
    }*/
}
