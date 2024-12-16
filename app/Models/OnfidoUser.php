<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnfidoUser extends Model
{
    protected $fillable = ["id", "applicant_id", "id_verification_date","verification_log","owner_id"];
}
