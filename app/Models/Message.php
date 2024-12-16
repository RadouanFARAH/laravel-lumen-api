<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends BaseModel
{
    public const MESSAGE = "MESSAGE";
    public const OFFER = "OFFER";
    public const MESSAGE_MAX_LENGTH = 1024 * 3;
    protected $assets = ['attachment_url'];
    protected $with = ['luggageRequest'];

    use SoftDeletes;
    protected $fillable = ["conversation_id", "type", "message", "sender_id", "attachment_thumb_url", "attachment_url", "price", "weight", "request_id"];

    public static function getType()
    {
        return [
            self::MESSAGE,
            self::OFFER,
        ];
    }

    public function conversation()
    {
        return $this->belongsTo("App\Models\Conversation")->with(['trip','parcel']);
    }

    public function sender()
    {
        return $this->belongsTo("App\Models\User");
    }

    public function luggageRequest()
    {
        return $this->belongsTo("App\Models\LuggageRequest", "request_id");
    }
}
