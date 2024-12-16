<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelerReview extends Model
{
    protected $fillable = ["author_id", "traveler_id", "comment", "trip_id"];

    protected $appends = ["rate"];

    public function author()
    {
        return $this->belongsTo("App\Models\User", "author_id");
    }

    public function getRateAttribute(){
        return TravelerRating::where("traveler_id",$this->traveler_id)->first();
    }

    public function traveler() //New by innov
    {
        return $this->belongsTo("App\Models\User", "traveler_id");
    }

}
