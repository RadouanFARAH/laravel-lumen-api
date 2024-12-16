<?php

namespace App\Models;

class AccountSetting extends BaseModel
{
    protected $fillable = ["allow_notification_sms", "allow_notification_mail", "allow_notification_push","owner_id"];
}
