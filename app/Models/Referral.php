<?php

namespace App\Models;

class Referral extends BaseModel
{
    const COMMISSION_AMOUNT = 100;

    protected $primaryKey = "child_id";

    protected $fillable = ["key", "parent_id", "child_id"];

    public function parent()
    {
        return $this->belongsTo(User::class, "parent_id");
    }

    public function child()
    {
        return $this->hasMany(User::class,'id', "child_id");
    }
}
