<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use SoftDeletes;

    const PARCEL_SAME_FLY = "SAME_FLY";
    const PARCEL_ANY = "ANY";

    protected $appends=["rate", "remaining_weight", "booking_weight"];

    protected $fillable = ["parcel_restriction", "fly_number", "departure_city_id", "departure_airport_id", "departure_date", "arrival_city_id", "arrival_date", "arrival_airport_id", "available_weight", "weight_unit_price", "info", "traveler_id", "canceled", "cancellation_reason", "auto_accept_booking", "allow_split_luggage"];

    public static function getParcelRestriction()
    {
        return [self::PARCEL_SAME_FLY, self::PARCEL_ANY];
    }

    public function  getRemainingWeightAttribute(){
        return $this->available_weight - $this->booked_weight;
        // return $this->available_weight - $this->luggageRequests()->sum("weight");
    }
    
    public function getBookingWeightAttribute($weight)
    {
        return $weight - $this->luggageRequests()->where("state",LuggageRequest::STATE_ACCEPTED)->sum("weight");
    }

    public function hasAvailableSpace($neededSpace)
    {
        return ($this->allow_split_luggage == false
                and $this->luggageRequests()->where('state', '=', LuggageRequest::STATE_ACCEPTED)->count()==0
                and $this->available_weight >=  $neededSpace
            )
            or
            ($this->available_weight - $this->booked_weight >=  $neededSpace);

    }

    public function scopeAvailable($query){
        return $query->whereColumn("trips.booked_weight","<","trips.available_weight");
    }

    public function getRateAttribute(){
        $stars = TravelerRating::where("traveler_id", $this->traveler_id);
        return [
            "average" => ($stars->avg("star") == null ? "0.0" : $stars->avg("star")) . "/5",
            "count" => $stars->count(),
        ];
    }

    public function luggageRequests()
    {
        return $this->hasMany("App\Models\LuggageRequest")->with(["parcel","trip"])->orderByDesc("created_at");
    }

    public function arrivalAirport()
    {
        return $this->belongsTo("App\Models\Airport", "arrival_airport_id");
    }

    public function departureAirport()
    {
        return $this->belongsTo("App\Models\Airport", "departure_airport_id");
    }

    public function arrivalCity()
    {
        return $this->belongsTo("App\Models\City", "arrival_city_id")->with(["country"]);
    }

    public function departureCity()
    {
        return $this->belongsTo("App\Models\City", "departure_city_id")->with(["country"]);
    }

    public function traveler()
    {
        return $this->belongsTo("App\Models\User", "traveler_id")->with(['devices']);
    }


}
