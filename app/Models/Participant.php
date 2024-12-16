<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    public $incrementing = false;
    protected $fillable = ["user_id", "conversation_id"];
    protected $primaryKey = null;

    public function user()
    {
        return $this->belongsTo("App\Models\User");
    }
}
