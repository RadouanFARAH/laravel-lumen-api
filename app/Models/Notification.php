<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $guarded = [];

    public function sender()
    {
        return $this->belongsTo("App\Models\User", "id");
    }

    public function luggageRequests()
    {
        return $this->belongsTo("App\Models\LuggageRequest", "request_id")->with(["parcel","trip"]);
    }
}
