<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelerRating extends Model
{
    public static function getValues(){
        return [1,2,3,4,5];
    }

    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable=["author_id","traveler_id","star"];

    public function author(){
        return $this->belongsTo("App\Models\User","author_id");
    }

}
