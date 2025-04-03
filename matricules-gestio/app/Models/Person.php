<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Person extends Model
{
    use HasFactory;

    protected $primaryKey = 'PERSCOD';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $guarded=[];

    protected $fillable = [
        'PERSCOD',
        'PAISCOD',
        'PROVCOD',
        'MUNICOD',
        'PERSNOM',
        'PERSCOG1',
        'PERSCOG2',
        'PERSPAR1',
        'PERSPAR2',
        'NIFNUMP',
        'NIFNUM',
        'NIFDC',
        'NIFSW',
        'PERSDCONNIF',
        'PERSDCANNIF',
        'PERSNACIONA',
        'PERSPASSPORT',
        'PERSNDATA',
        'PERSPARE',
        'PERSMARE',
        'PERSSEXE',
        'PERSSW',
        'IDIOCOD',
        'PERSVNUM',
        'STDAPLADD',
        'STDAPLMOD',
        'STDUGR',
        'STDUMOD',
        'STDDGR',
        'STDDMOD',
        'STDHGR',
        'STDHMOD',
        'CONTVNUM',
        'NIFORIG',
        'PERSCODOLD',
        'VALDATA',
        'BAIXASW',
        'GUID',
    ];

    /**
     *  Get the Telecos 
     *
     *  @return hasmany<int, Teleco>
     */
    public function telecos()
    {
        return $this->hasMany(Teleco::class);
    }

    public function instances()
    {
        return $this->hasMany(Instance::class, 'PERSCOD');
    }

    //Obtenir totes les instancies d'un representant
    public function representations()
    {
        return $this->hasMany(Instance::class, 'REPRCOD');
    }

    public function getNomPersonAttribute()
    {
        //return $this->CARCOD . ' ' . $this->CARSIG;
        return "{$this->PERSCOD} {$this->PERSNOM} {$this->PERSCOG1} {$this->PERSCOG2}";
    }
}
