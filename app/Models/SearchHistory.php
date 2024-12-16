<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchHistory extends Model
{
    const TRIP = "TRIP";
    const PARCEL = "PARCEL";

    protected $fillable =["from","to","fly_number","context","date","parcel_restriction","user_id","alert_me"];

    public static function getContext(){
        return [self::TRIP,self::PARCEL];
    }

    public function departure(){
        return $this->belongsTo("App\Models\City","from");
    }

    public function destination(){
        return $this->belongsTo("App\Models\City","to");
    }
}
