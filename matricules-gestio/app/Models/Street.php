<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Street extends Model
{
    use HasFactory;

    // Quan primaryKey no es id hem d'indicar
    protected $primaryKey = 'CARCOD';

    protected $keyType = 'int';

    // No estem utilitzant auto increment en la primary key
    public $incrementing = false;

    protected $guarded = [];

    /**
     *  Get all of dwellings for the Street
     *
     *  @return HasMany<int, dwelling>
     */
    public function dwellings(): HasMany
    {
        return $this->hasMany(Dwelling::class);
    }
}
