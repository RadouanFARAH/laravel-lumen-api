<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ["service_percentage_fees"];

    public static function getServicePercentageFees(){
        return Setting::first()->service_percentage_fees / 100;
    }
}
