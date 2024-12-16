<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fcm extends Model
{

    public const ANDROID = "ANDROID";
    public const IOS = "IOS";
    public const WEB = "WEB";

    protected $fillable = ['app_version', 'platform', 'token', 'user_id'];

    public static function build($title, $message, $image = null): array
    {
        return [
            "title" => $title,
            "message" => $message,
            "logo" => $image,
        ];
    }

    public static function getPlatforms(): array
    {
        return [
            self::ANDROID,
            self::IOS,
            self::WEB,
        ];
    }
}
