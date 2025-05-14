<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Camera extends Model
{
    use HasFactory;

   protected $guarded=[];

   protected $fillable = [
       'owner_PAISPROVMUNICARCOD',
    ];
    
    public function ownerStreet()
    {
        return $this->belongsTo(StreetBarriVell::class, 'owner_PAISPROVMUNICARCOD', 'PAISPROVMUNICARCOD');
    }

    public function coveredStreets()
    {
        return $this->belongsToMany(StreetBarriVell::class, 'camera_street', 'camera_id', 'PAISPROVMUNICARCOD');
    }
    public function getNomCarrerBaseAttribute()
    {
        return $this->ownerStreetBase() ? $this->ownerStreetBase()->nom_carrer : null;
    }

    public function ownerStreetBase()
    {
        return $this->ownerStreet ? $this->ownerStreet->street : null;
    }
}
