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
        'DOMCOD'
    ];

    public function carrersBarriVell()
    {
        return $this->belongsToMany(StreetBarriVell::class, 'vehicle_street','MATRICULA','CARCOD')->withTimestamps();
    }

    public function habitatge()
    {
        return $this->belongsTo(Dwelling::class, 'DOMCOD', 'DOMCOD');
    }
}
