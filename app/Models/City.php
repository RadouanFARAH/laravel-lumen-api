<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $guarded = [];

    public function getCoordinatesAttribute($coordinates)
    {
        return json_decode($coordinates);
    }

    public function airports(){
        return $this->hasMany("App\Models\Airport");
    }

    public function country(){
        return $this->belongsTo("App\Models\Country");
    }
}
