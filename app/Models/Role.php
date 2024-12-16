<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    const ROOT = 1;
    const ADMIN = 2;
    const USER = 3;
    protected $fillable = ["id", "label"];
}
