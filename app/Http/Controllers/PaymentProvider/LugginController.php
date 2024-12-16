<?php

namespace App\Http\Controllers\PaymentProvider;

use App\Models\User;
use App\Models\Provider;
use Illuminate\Http\Request;
use App\Http\Controllers\PaymentProvider\BaseController;

class LugginController extends BaseController
{


    public function payOut($transaction, $user)
    {
        return $this->liteResponse(true,"ee");
        // return $this->reply(true);
    }

    public function onSuccess(Request $request)
    {
        return $this->reply(true);
    }

    public function onCancel(Request $request)
    {
        return $this->reply(true);
    }


    public function getAccount($userId = null)
    {
        $user = User::find(empty($userId) ? auth()->id() : $userId);
        return "$user->id-$user->phone-$user->email";
    }

    public function getId()
    {
       return "LUGGIN";
    }

    public function payIn($transaction, $user)
    {

    }
}
