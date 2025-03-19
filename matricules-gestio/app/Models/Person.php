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

    /**
     *  Get the Telecos 
     *
     *  @return hasmany<int, Teleco>
     */
    public function telecos()
    {
        return $this->hasMany(Teleco::class);
    }
}
