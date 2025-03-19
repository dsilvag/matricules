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

    /**
     *  Get the Street 
     *
     *  @return BelongsTo<int, Street>
     */
    public function street()
    {
        return $this->belongsTo(Street::class);
    }
}
