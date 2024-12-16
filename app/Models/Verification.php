<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    const EMAIL = "EMAIL";
    const PHONE = "PHONE";
    const DOC = "DOC";
    protected $fillable = ["type", "current_value", "update_value", "user_id", "otp"];

    public static function getValues(): array
    {
        return [
            self::EMAIL,
            self::PHONE,
            self::DOC
        ];
    }

    public function user()
    {
        return $this->belongsTo("App\Models\User");
    }
}
