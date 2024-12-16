<?php

namespace App\Http\Controllers;

use App\Models\Verification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class VerificationController extends Controller
{
    public function push($type, $currentValue, $updateValue, $userId)
    {
        return $this->add([
            "type" => $type,
            "current_value" => $currentValue,
            "update_value" => $updateValue,
            "user_id" => $userId,
            "otp" => random_int(100000, 999999)
        ]);
    }

    public function add(array $data)
    {
        return $this->save($data);
    }

    public function create(array $data)
    {
        return Verification::create($data);
    }

    protected function validator(&$data)
    {
        return Validator::make($data, [
            "type" => ["required", Rule::in(Verification::getValues())],
            "current_value" => "required",
            "update_value" => "required",
            "otp" => "required",
            "user_id" => ["required", "exists:users,id"],
        ]);
    }
}
