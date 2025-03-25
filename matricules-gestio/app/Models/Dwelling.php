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
        return $this->belongsTo(Street::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'DOMCOD');
    }
}
