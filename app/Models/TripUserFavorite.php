<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripUserFavorite extends Model
{
    protected $fillable = ["user_id", "trip_id"];

    public function trip()
    {
        return $this->belongsTo("App\Models\Trip")->with(['arrivalAirport', 'arrivalCity', 'departureAirport', 'departureCity', 'traveler']);
    }
}
