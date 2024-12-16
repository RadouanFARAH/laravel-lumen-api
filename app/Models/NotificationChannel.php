<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationChannel extends BaseModel
{
    protected $fillable =["mail","sms","push","user_id"];
}
