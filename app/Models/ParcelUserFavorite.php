<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParcelUserFavorite extends Model
{
    protected $fillable = ["user_id", "parcel_id"];

    public function parcel()
    {
        return $this->belongsTo("App\Models\Parcel")->with([ 'arrivalCity', 'departureCity', 'owner']);
    }
}
