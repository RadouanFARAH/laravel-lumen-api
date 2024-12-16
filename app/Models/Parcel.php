<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parcel extends BaseModel
{
    protected $fillable = ["parcel_restriction","allow_split", "fly_number", "departure_city_id", "departure_date","arrival_date", "arrival_city_id", "arrival_airport_id", "departure_airport_id", "images", "weight", "info", "sender_id", "canceled", "cancellation_reason", "recipient_id", "private","weight_unit_price"];
    protected $assets = ['images'];
    protected $appends = ["rate","booking_weight","remaining_weight", "arrival_airport_id", 'arrival_airport','departure_airport','departure_airport_id'];
    use SoftDeletes;



    public function getArrivalAirportAttribute()
    {
        return Airport::where("city_id", $this->arrival_city_id)->first();
    }

    public function  getRemainingWeightAttribute(){
        return $this->weight - $this->luggageRequests()->sum("weight");
    }

    public function getDepartureAirportAttribute()
    {
        return Airport::where("city_id", $this->departure_city_id)->first();
    }

    public function getArrivalAirportIdAttribute()
    {
        return $this->arrival_city_id;
    }

    public function getDepartureAirportIdAttribute()
    {
        return $this->departure_city_id;
    }

    public function arrivalCity()
    {
        return $this->belongsTo("App\Models\City", "arrival_city_id")->with(["country"]);
    }

    public function hasAvailableSpace($neededSpace)
    {
        return ($this->allow_split == false
                and $this->luggageRequests()->where('state', '=', LuggageRequest::STATE_ACCEPTED)->count()==0
                and $this->weight >=  $neededSpace
            )
            or
            ($this->weight >= $this->luggageRequests()->sum("weight") + $neededSpace);

    }

    public function getBookingWeightAttribute($weight)
    {
        return $weight - $this->luggageRequests()->where("state",LuggageRequest::STATE_ACCEPTED)->sum("weight");
    }

    public function getRateAttribute()
    {
        $stars = TravelerRating::where("traveler_id", $this->sender_id);
        return [
            "average" => ($stars->avg("star") == null ? "0.0" : $stars->avg("star")) . "/5",
            "count" => $stars->count(),
        ];
    }

    public function scopeAvailable($query){
        return $query->whereColumn("booked_weight",'<',"weight");
    }

    public function departureCity()
    {
        return $this->belongsTo("App\Models\City", "departure_city_id")->with(["country"]);
    }

   
    public function recipient()
    {
        return $this->belongsTo("App\Models\Recipient");
    }

    public function scopeNotBooked(Builder $builder)
    {
        return $builder->whereDoesntHave("luggageRequests", function (Builder $query) {
            $query->where('state', '=', LuggageRequest::STATE_ACCEPTED);
        });
    }

    public function owner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo("App\Models\User", "sender_id")->with(['devices']);
    }

    public function luggageRequests()
    {
        return $this->hasMany("App\Models\LuggageRequest")->with(['trip','parcel'])->orderByDesc("created_at");
    }

    public function arrivalAirport()
    {
        return $this->belongsTo("App\Models\Airport", "arrival_airport_id");
    }

    public function departureAirport()
    {
        return $this->belongsTo("App\Models\Airport", "departure_airport_id");
    }

}
