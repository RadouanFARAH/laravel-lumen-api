<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LuggageRequest extends Model
{
    const STATE_PENDING = "PENDING";
    const STATE_DENIED = "DENIED";
    const STATE_ACCEPTED = "ACCEPTED";
    const STATE_CANCELED = "CANCELED";
    const STATE_CANCEL = "CANCEL";

    const INIT_BY_TRAVELER = "TRAVELER";
    const INIT_BY_SENDER = "SENDER";
    protected $fillable = ["weight","is_assurance", "proposal_unit_price", "state", "initiator", 'parcel_id', 'trip_id', 'transaction_fees'];

    protected $appends = ["is_paid", "histories","as_validator"];

    public static function getState()
    {
        return [
            self::STATE_PENDING,
            self::STATE_DENIED,
            self::STATE_ACCEPTED,
            self::STATE_CANCELED,
        ];
    }

    public static function getInitiator()
    {
        return [
            self::INIT_BY_SENDER,
            self::INIT_BY_TRAVELER,
        ];
    }

    public function amountToTransfer()
    {
        return ceil(($this->proposal_unit_price * $this->weight) - $this->transaction_fees);
    }

    public function trip()
    {
        return $this->belongsTo("App\Models\Trip", "trip_id")->with(["traveler","departureCity","departureAirport","arrivalCity","arrivalAirport"]);
    }

    public function parcel()
    {
        return $this->belongsTo("App\Models\Parcel", "parcel_id")->with(["owner", "recipient"]);
    }

    public function validator()
    {
        if ($this->initiator == self::INIT_BY_SENDER) {
            return $this->trip->traveler;
        } else {
            return $this->parcel->owner;
        }
    }

    public function getIsPaidAttribute()
    {
        return $this->isPaid();
    }


    public function getAsValidatorAttribute(): bool
    {
        return $this->validator()->id == auth()->id();
    }

    public function isPaid()
    {
        $requestWallet = $this->payments()->whereHas('wallet.transaction', function ($q) {
            $q->whereNotNull('verify_at');
        })->first();

        return !empty($requestWallet);
    }

    public function amountToPay()
    {
        return ceil($this->proposal_unit_price * $this->weight);
    }

    public function getHistoriesAttribute()
    {
        return LuggageRequestWallet::whereHas('wallet', function ($q) {
            $q->where("owner_id", auth()->id());
        })->where("luggage_request_id", $this->id)->get();
    }

    public function initiator()
    {
        if ($this->initiator == self::INIT_BY_SENDER) {
            return $this->parcel->owner;
        } else {
            return $this->trip->traveler;
        }
    }

    public function payments()
    {
        return $this->hasMany("App\Models\LuggageRequestWallet");
    }
}
