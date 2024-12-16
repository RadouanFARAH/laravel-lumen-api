<?php

namespace App\Http\Controllers\PaymentProvider;

use App\Http\Controllers\TransactionController;
use App\Models\Provider;
use App\Models\Transaction;
use Dohone\Facades\DohonePayIn;
use Dohone\PayOut\DohonePayOut;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class MobileMoneyController extends BaseController
{


    /**
     * @OA\Post(
     *    path="/api/user/wallet/mobile-sms-verify",
     *   tags={"Wallet"},
     *   summary="MObile money sms verification",
     *   description="",
     *   operationId="MomoVerify",
     *   @OA\Parameter(
     *         name="code",
     *         in="query",
     *         description="sms code",
     *         required=true,
     *         @OA\Schema(
     *         type="string"
     *         ),
     *         style="form"
     *     ),
     *   @OA\Parameter(
     *         name="phone",
     *         in="query",
     *         description="sms code receiver number",
     *         required=true,
     *         @OA\Schema(
     *         type="string"
     *         ),
     *         style="form"
     *     ),
     *     @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\Schema(type="json"),
     *
     *   ),
     * )
     * @param Request $request
     *
     * @return array|JsonResponse
     */
    public function SMSConfirmation(Request $request)
    {
        $response = DohonePayIn::sms()
            ->setCode($request->code)
            ->setPhone($request->phone)
            ->get();

        return $this->liteResponse($response->isSuccess() ? config('code.request.SUCCESS') : config('code.request.FAILURE'), $response->getMessage());
    }

    public function payIn($transaction, $user)
    {
        $api = DohonePayIn::payWithAPI()
            ->setAmount($transaction->balance)
            ->setClientPhone(request()->get('phone'))
            ->setClientEmail($user->email)
            ->setClientName("$user->first_name $user->last_name")
            ->setCommandID($transaction->id)
            ->setNotifyPage(route('dohone.callback', ['ref' => $transaction->getRouteKey()]),)
            ->setOTPCode(request()->get('otp'))
            ->setDescription($transaction->reason)
            ->setMethod(request()->get('mode'));
        $api->setClientID(auth()->id());
        $response = $api->get();
        //$transaction->notify(new TransactionAlert($transaction, false));

        return $this->liteResponse($response->isSuccess() ? config('code.request.SUCCESS') : config('code.request.FAILURE'), $response->isSuccess() ? $response->getMessage() : $response->getErrors());
    }

    public function payOut($transaction, $user)
    {
        $api = DohonePayOut::mobile()
            ->setAmount($transaction->balance)
            ->setReceiverAccount($user->phone)
            ->setReceiverCountry($user->country->label)
            ->setReceiverCity("Yaounde")
            ->setReceiverName("$user->first_name $user->last_name")
            ->setReceiverID($user->id)
            ->setNotifyUrl(route('dohone.callback', ['ref' => $transaction->getRouteKey()]),)
            ->setTransactionID($transaction->id)
            ->setMethod(request()->get('mode'));
        $response = $api->post();
        //$transaction->notify(new TransactionAlert($transaction, false));

        return $this->liteResponse($response->isSuccess() ? config('code.request.SUCCESS') : config('code.request.FAILURE'), $response->isSuccess() ? $response->getMessage() : $response->getErrors());

    }

    public function getAccount($userId = null)
    {
        return config("dohone.merchantToken");
    }

    public function onSuccess(Request $request)
    {
        $transaction = Transaction::find(Crypt::decryptString($request->ref));
        if (empty($transaction))
            return $this->liteResponse(config('code.request.FAILURE'), null, "we can't found this order");

        $data = $request->all();
        try {
            //file_put_contents("confidential/" . $transaction->id . ".json", ["transaction" => $transaction, "data" => json_encode($data)]);
            if ($request->hash == md5($data["idReqDoh"] . $data["rI"] . $data["rMt"] . config("dohone.payOutHashCode"))) {
                //  FcmController::paymentAlert($user, $model, $context);

                $transaction->payment_token = $data['idReqDoh'];
                $transaction->account = config('dohone.start.rH');
                $transaction->verification_log = json_encode($request->all());
                TransactionController::verify($transaction);

                //file_put_contents("confidential/" . $transaction->id . "-verify.json", ["transaction" => $transaction, "data" => json_encode($data)]);
                //$transaction->notify(new TransactionAlert($transaction, true));
                return $this->liteResponse(config('code.request.SUCCESS'));
            }
            return $this->liteResponse(config('code.request.FAILURE'));
        } catch (Exception $exception) {
            //file_put_contents("confidential/exception-".$transaction->id.".md", $exception->getMessage());
            return $this->liteResponse(config('code.request.FAILURE'), null, $exception->getMessage());
        }
    }

    public function onCancel(Request $request)
    {

    }

    public function getId()
    {
        return Provider::DOHONE;
    }
}
