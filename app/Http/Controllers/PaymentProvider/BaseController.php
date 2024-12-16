<?php

namespace App\Http\Controllers\PaymentProvider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

abstract class BaseController extends Controller
{
    public const TRANSFER = "TRANSFER";

    public abstract function payIn($transaction, $user);

    public abstract function payOut($transaction, $user);

    public abstract function getAccount($userId = null);

    public abstract function onSuccess(Request $request);

    public abstract function onCancel(Request $request);

    public abstract function getId();
}
