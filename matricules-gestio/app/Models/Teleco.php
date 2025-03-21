<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Teleco extends Model
{
    use HasFactory;
/*
    protected $table = 'telecos';

    protected $primaryKey = ['PERSCOD', 'NUMORDRE'];*/

    protected $fillable = [
        'PERSCOD',
        'NUMORDRE',
        'TIPCONTACTE',
        'CONTACTE',
        'OBSERVACIONS',
        'STDUGR',
        'STDUMOD',
        'STDDGR',
        'STDDMOD',
        'STDHGR',
        'STDHMOD',
        'VALDATA',
        'BAIXASW',
    ];
/*
    public function getKeyName()
    {
        return implode(',', $this->primaryKey);
    }
*/

    public $incrementing = false;

    public function person()
    {
        return $this->belongsTo(People::class, 'PERSCOD', 'PERSCOD');
    }
}