<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Vehicle extends Model
{
    use HasFactory;

    // Quan primaryKey no es id hem d'indicar
    protected $primaryKey = 'MATRICULA';

    // No estem utilitzant auto increment en la primary key
    public $incrementing = false;

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
}
