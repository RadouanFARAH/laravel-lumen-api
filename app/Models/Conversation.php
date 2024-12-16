<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{

    protected $fillable = ['creator_id', "channel", "trip_id", "request_id", "parcel_id"];

    /**
     * Get user conversation
     *
     * @param $creatorId
     * @param $participantId
     * @param $tripId
     * @param $parcelId
     *
     * @return Model|\Illuminate\Database\Query\Builder|object|null
     */

    public static function getConversation($creatorId, $participantId, $tripId, $parcelId)
    {
        return self::findConversations($creatorId, $participantId, $tripId, $parcelId)
            ->select('conversations.*')
            ->first();
    }

    /**
     * Conversation finder with left join
     *
     * @param $creatorId
     * @param $participantId
     * @param $tripId
     * @param $parcelId
     *
     * @return \Illuminate\Database\Query\Builder
     */
    private static function findConversations($creatorId, $participantId, $tripId, $parcelId)
    {
        return Conversation::where("creator_id", $creatorId)
            ->whereHas('participants', function ($q) use ($participantId) {
                $q->where("user_id", $participantId);
            })->where(empty($tripId) ? "parcel_id" : "trip_id", empty($tripId) ? $parcelId : $tripId)
            ->orWhere("creator_id", $participantId)
            ->whereHas('participants', function ($q) use ($creatorId) {
                $q->where("user_id", $creatorId);
            })->where(empty($tripId) ? "parcel_id" : "trip_id", empty($tripId) ? $parcelId : $tripId);
    }

    public function participants()
    {
        return $this->hasMany("App\Models\Participant")->with(["user"]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo("App\Models\User");
    }

    public function messages()
    {
        return $this->hasMany("App\Models\Message")
            ->orderBy("created_at", 'ASC');
    }

    public function trip()
    {
        return $this->belongsTo("App\Models\Trip")->with(["arrivalCity", "departureCity", "arrivalAirport", "departureAirport", "traveler"]);
    }

    public function parcel()
    {
        return $this->belongsTo("App\Models\Parcel")->with(["arrivalCity", "departureCity", "arrivalAirport", "departureAirport", "owner"]);
    }

    public function luggageRequest()
    {
        return $this->belongsTo("App\Models\LuggageRequest", "request_id");
    }
}
