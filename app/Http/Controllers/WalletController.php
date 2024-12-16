<?php

namespace App\Http\Controllers;

use App\Http\Controllers\PaymentProvider\BaseController;
use App\Http\Controllers\PaymentProvider\LugginController;
use App\Http\Controllers\PaymentProvider\MobileMoneyController;
use App\Http\Controllers\PaymentProvider\PayPalController;
use App\Http\Controllers\PaymentProvider\StripeController;
use App\Http\ResponseParser\DefResponse;
use App\Models\LuggageRequest;
use App\Models\LuggageRequestWallet;
use App\Models\MovementType;
use App\Models\Provider;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    public function create(array $data)
    {
        return Wallet::create($data);
    }

    /**
     * * @OA\Post(
     *     path="/api/finance/deposit",
     *   tags={"Wallet"},
     *   summary="Solidarity",
     *   description="",
     *   operationId="walletDeposit",
     *    @OA\Parameter(
     *         name="otp",
     *         in="query",
     *         description="otp from orange get by ussd #150*4*4# required when provider is MOBILE Money and mode is
     *         Orange", required=false,
     *         @OA\Schema(
     *         type="string"
     *         ),
     *         style="form"
     *     ),
     *    @OA\Parameter(
     *         name="mode",
     *         in="query",
     *         description="Mobile money paiment type 1=MTN, 2=Orange, 10=Dohone",
     *         required=false,
     *         @OA\Schema(
     *         type="string"
     *         ),
     *         style="form"
     *     ),
     *    @OA\Parameter(
     *         name="phone",
     *         in="query",
     *         description="the buyer phone SET USER PHONE BY DEFAULT",
     *         required=true,
     *         @OA\Schema(
     *         type="integer"
     *         ),
     *         style="form"
     *     ),
     *    @OA\Parameter(
     *         name="card_no",
     *         in="query",
     *         description="card number  for stripe",
     *         required=true,
     *         @OA\Schema(
     *         type="string"
     *         ),
     *         style="form"
     *     ),
     *    @OA\Parameter(
     *         name="ccExpiryMonth",
     *         in="query",
     *         description="card expiration month for stripe",
     *         required=true,
     *         @OA\Schema(
     *         type="string"
     *         ),
     *         style="form"
     *     ),
     *    @OA\Parameter(
     *         name="ccExpiryYear",
     *         in="query",
     *         description="card expiration Year for stripe",
     *         required=true,
     *         @OA\Schema(
     *         type="string"
     *         ),
     *         style="form"
     *     ),
     *    @OA\Parameter(
     *         name="cvvNumber",
     *         in="query",
     *         description="card cvv for stripe",
     *         required=true,
     *         @OA\Schema(
     *         type="string"
     *         ),
     *         style="form"
     *     ),
     *    @OA\Parameter(
     *         name="provider_id",
     *         in="query",
     *         description="the channel you want to use 1=PAYPAL 2=STRIPE  3=MOBILE MONEY",
     *         required=true,
     *         @OA\Schema(
     *         type="integer"
     *         ),
     *         style="form"
     *     ),
     *    @OA\Parameter(
     *         name="luggage_request_id",
     *         in="query",
     *         description="the id of the request to pay",
     *         required=true,
     *         @OA\Schema(
     *         type="integer"
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
     * Call when the payment has been validate in the transaction table
     *
     * @param Request $request
     *
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function payIn(Request $request)
    {
        try {

            $luggageRequest = LuggageRequest::find($request->luggage_request_id);
            if (empty($luggageRequest)) {
                return $this->liteResponse(config("code.request.NOT_FOUND"));
            }

            if ($luggageRequest->isPaid()) {
                return $this->liteResponse(config("code.request.SUCCESS"), $luggageRequest->payments, "Hello you have already paid this");
            }

            $buyer = auth()->user();
            $request->request->add([
                'owner_id' => User::LUGGIN,
                'balance' => $luggageRequest->amountToPay(),
                'reason' => "payment of the request [$luggageRequest->id] by [$buyer->id, $buyer->phone, $buyer->email]",
            ]);

            $data = $request->all((new Wallet())->getFillable());
            $this->buildWallet($data);
            //Insert a positive balance and specifying transaction type to DEPOSIT to represent the operation in user
            $transactionResponse = $this->initTransaction($request->balance, $this->loadProvider()->getAccount(), MovementType::DEPOSIT);
            $walletResponse = new DefResponse($this->store($data, $transactionResponse));
            if ($walletResponse->isSuccess()) {
                $request->request->add([
                    'wallet_id' => $walletResponse->getData()["id"],
                    'luggage_request_id' => $luggageRequest->id,
                ]);
                (new LuggageRequestWalletController())->store($request);

                //Notify
                $request->request->add([
                    'title' => "Paiement",
                    'message' => "Paiement de la réseravtion",
                    'type' => "PAYMENT",
                    "sender_id" => auth()->id(),
                    "owner_id" => $luggageRequest->initiator()->id,
                    "request_id" => $luggageRequest->id,
                ]);

                (new NotificationController())->store($request);

                return $this->loadProvider()->payIn(Transaction::find($transactionResponse->getData()['id']), $this->getUser());
            }


            return $walletResponse->getResponse();
        } catch (\Exception $e) {
            return $this->liteResponse(config('code.request.FAILURE'), $e->getTrace(), $e->getMessage());
        }
    }

    /**
     * * @OA\Post(
     *     path="/api/finance/withdrawal",
     *   tags={"Wallet"},
     *   summary="Solidarity",
     *   description="",
     *   operationId="walletWithdrawal",
     *    @OA\Parameter(
     *         name="mode",
     *         in="query",
     *         description="Mobile money paiment type 1=MTN, 2=Orange, 10=Dohone",
     *         required=false,
     *         @OA\Schema(
     *         type="string"
     *         ),
     *         style="form"
     *     ),
     *    @OA\Parameter(
     *         name="phone",
     *         in="query",
     *         description="the buyer phone SET USER PHONE BY DEFAULT",
     *         required=true,
     *         @OA\Schema(
     *         type="integer"
     *         ),
     *         style="form"
     *     ),
     *    @OA\Parameter(
     *         name="card_no",
     *         in="query",
     *         description="card number  for stripe",
     *         required=true,
     *         @OA\Schema(
     *         type="string"
     *         ),
     *         style="form"
     *     ),
     *    @OA\Parameter(
     *         name="ccExpiryMonth",
     *         in="query",
     *         description="card expiration month for stripe",
     *         required=true,
     *         @OA\Schema(
     *         type="string"
     *         ),
     *         style="form"
     *     ),
     *    @OA\Parameter(
     *         name="ccExpiryYear",
     *         in="query",
     *         description="card expiration Year for stripe",
     *         required=true,
     *         @OA\Schema(
     *         type="string"
     *         ),
     *         style="form"
     *     ),
     *    @OA\Parameter(
     *         name="cvvNumber",
     *         in="query",
     *         description="card cvv for stripe",
     *         required=true,
     *         @OA\Schema(
     *         type="string"
     *         ),
     *         style="form"
     *     ),
     *    @OA\Parameter(
     *         name="provider_id",
     *         in="query",
     *         description="the channel you want to use 1=PAYPAL 2=STRIPE  3=MOBILE MONEY",
     *         required=true,
     *         @OA\Schema(
     *         type="integer"
     *         ),
     *         style="form"
     *     ),
     *    @OA\Parameter(
     *         name="amount",
     *         in="query",
     *         description="the amount to pay",
     *         required=true,
     *         @OA\Schema(
     *         type="integer"
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
     * Call when the payment has been validate in the transaction table
     *
     * @param Request $request
     *
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function payOut(Request $request)
    {
        try {

            $user = auth()->user();
            $request->request->add([
                'owner_id' => $user->id,
                'balance' => $request->amount,
                'reason' => "withdrawal of $request->amount",
            ]);

            $data = $request->all((new Wallet())->getFillable());
            $this->buildWallet($data);
            
            //Insert a positive balance and specifying transaction type to WITHDRAWAL to represent the operation in user
            $transactionResponse = $this->initTransaction($request->balance, $this->loadProvider()->getAccount(), MovementType::WITHDRAWAL);
            $walletResponse = new DefResponse($this->store($data, $transactionResponse));
            if ($walletResponse->isSuccess()) {
                return $this->loadProvider()->payOut(Transaction::find($transactionResponse->getData()['id']), $this->getUser());
            }


            return $walletResponse->getResponse();
        } catch (\Exception $e) {
            return $this->liteResponse(config('code.request.FAILURE'), $e->getTrace(), $e->getMessage());
        }
    }

    private function buildWallet(array &$data, $sourceWallet = null)
    {
        //Generate Wallet Id
        $data['id'] = $this->genId(self::WALLET);
        //$data['movement_type_id'] = $movementType;
        $data['credit_wallet_id'] = empty($sourceWallet) ? $data['id'] : $sourceWallet;
        $data['reason'] = request()->reason;
    }

    /**
     * @param        $amount
     * @param        $account
     * @param int    $movement
     * @param null   $reason
     *
     * @return DefResponse
     */
    private function initTransaction($amount, $account, int $movement, $reason = null): DefResponse
    {
        $transaction = new TransactionController($amount, $account, $this->loadProvider()->getId(), empty($reason) ? \request()->request->get('reason') : $reason);
        error_log($reason);
        switch ($movement) {
            case MovementType::DEPOSIT:
                return new DefResponse($transaction->deposit());
            case MovementType::WITHDRAWAL:
                return new DefResponse($transaction->withdrawal());
            case MovementType::INTERNAL_MOVEMENT:
                return new DefResponse($transaction->internalMovement());
            default:
                return new DefResponse($this->liteResponse(config('code.request.FAILURE')));
        }
    }

    private function loadProvider(): BaseController
    {
        switch (\request()->provider_id) {
            case Provider::PAYPAL:
                return new PayPalController();
            case Provider::STRIPE:
                return new StripeController();
            case Provider::DOHONE:
                return new MobileMoneyController();
            default:
                return new LugginController();
        }
    }

    /**
     * @param array $data
     * @param       $transaction
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(array $data, DefResponse $transaction)
    {
        if ($transaction->isSuccess()) {
            $data['transaction_id'] = $transaction->getData()['id'];
            //Insert a negative balance and specifying transaction type to WITHDRAWAL to represent the operation in user
            return parent::save($data);
        }

        return $transaction->getResponse();
    }

    private function getUser($userId = null)
    {
        return empty($userId) ? auth()->user() : User::find($userId);
    }

    public function refund(LuggageRequest $request)
    {
        try {

            $requestWallet = $request->payments()->whereHas('wallet', function ($q) use ($request) {
                $q->whereHas('transaction', function ($q) use ($request) {
                    $q->where("balance", $request->amountToPay());
                });
            })->first();

            if (!$request->isPaid() or empty($requestWallet)) {
                return $this->liteResponse(config('code.request.NOT_AUTHORIZED'), $requestWallet, "Sorry but this book is not paid");
            }


            $buyer = $request->parcel->owner;

            $amountToRefund = $requestWallet->wallet->transaction->balance;

            // $amountToRefund = $requestWallet->wallet->transaction->balance - $requestWallet->wallet->transaction->balance * Setting::getServicePercentageFees();

            $amountToTransfer = $amountToRefund;

            if($buyer->id != auth()->id()){ 
               $amountToTransfer = 0;
            //    $amountToRefund = $requestWallet->wallet->transaction->balance + $requestWallet->wallet->transaction->balance * Setting::getServicePercentageFees();
            }else{
                
                //Booking less than 24h before departure
                if(Carbon::parse($request->created_at)->diffInHours(Carbon::parse($request->trip->departure_date)) <= 24){
                
                    //cancellation less than 30min before departure refund 50% except fees
                    if (Carbon::parse($request->trip->departure_date)->diffInMinutes(Carbon::now('Africa/Douala')->toDateTimeString()) <= 30) {
                        $amountToTransfer = $amountToRefund - $amountToRefund * (50 / 100);
                        $amountToRefund = $amountToTransfer;
                    //cancellation more than 30min before departure refund 100% except fees
                    }else{
                        $amountToTransfer = 0;
                    }
                
                    //Booking more than 24h before departure
                }else{
                
                    //cancellation more than 24h before departure refund all except fees
                    if(Carbon::parse($request->trip->departure_date)->diffInHours(Carbon::now('Africa/Douala')->toDateTimeString()) > 24){
                        $amountToTransfer = 0;
                    }else{
                        //cancellation less than 24h before departure refund 50% except fees
                        $amountToTransfer = $amountToRefund - $amountToRefund * (50 / 100);
                        $amountToRefund = $amountToTransfer;
                    }
                }
            }

            $reason = "refund of the request [$requestWallet->luggage_request_id] to [$buyer->id, $buyer->phone, $buyer->email]";
            //withdrawal from app account the amount to send to receiver
            $appWalletData = [
                'owner_id' => User::LUGGIN,
                'balance' => $amountToRefund,
                'reason' => $reason,
            ];

            $this->buildWallet($appWalletData, $requestWallet->wallet->id);
            $providerController = new LugginController();
            $transactionResponse = $this->initTransaction($amountToRefund, $providerController->getAccount(User::LUGGIN), MovementType::WITHDRAWAL, $reason);
            $senderWalletResponse = new DefResponse($this->store($appWalletData, $transactionResponse));
            if ($senderWalletResponse->isSuccess()) {
                //withdrawal from app account the amount to send to receiver
                $receiverWalletData = [
                    'owner_id' => $buyer->id,
                    'balance' => $amountToRefund,
                    'reason' => $reason,
                ];

                $this->buildWallet($receiverWalletData, $senderWalletResponse->getData()['id']);
                //Insert a positive balance and specifying transaction type to DEPOSIT to represent the operation in user
                $receiverResponse = new DefResponse($this->store($receiverWalletData, $this->initTransaction($amountToRefund, $providerController->getAccount(User::LUGGIN), MovementType::DEPOSIT, $reason)));
                if ($receiverResponse->isSuccess()) {
                    //send money to validator
                    if ($amountToTransfer > 0) {
                        $validatorWalletData = [
                            'owner_id' => $request->validator()->id,
                            'balance' => $amountToRefund,
                            'reason' => $reason,
                        ];
                        $this->buildWallet($validatorWalletData, $senderWalletResponse->getData()['id']);
                        $validatorResponse = new DefResponse($this->store($validatorWalletData, $this->initTransaction($amountToTransfer, $providerController->getAccount(User::LUGGIN), MovementType::DEPOSIT, $reason)));
                        if ($receiverResponse->isSuccess()) {
                            Transaction::where("id", $senderWalletResponse->getData()["transaction_id"])
                                ->orWhere("id", $receiverResponse->getData()['transaction_id'])
                                ->orWhere("id", $validatorResponse->getData()['transaction_id'])
                                ->update(
                                    [
                                        "verify_at" => Carbon::now(),
                                        "payment_token" => $validatorResponse->getData()["transaction_id"] . "-" . $validatorResponse->getData()['transaction_id'],
                                        "verification_log" => [$validatorResponse->getData(), $validatorResponse->getData()],
                                    ]
                                );
                        }
                        return $validatorResponse->getResponse();
                    } else {
                        Transaction::where("id", $senderWalletResponse->getData()["transaction_id"])
                            ->orWhere("id", $receiverResponse->getData()['transaction_id'])
                            ->update(
                                [
                                    "verify_at" => Carbon::now(),
                                    "payment_token" => $senderWalletResponse->getData()["transaction_id"] . "-" . $receiverResponse->getData()['transaction_id'],
                                    "verification_log" => [$senderWalletResponse->getData(), $receiverResponse->getData()],
                                ]
                            );
                    }
                }
                return $receiverResponse->getResponse();
            }
            return $senderWalletResponse->getResponse();
        } catch (\Exception $e) {
            return $this->liteResponse(config('code.request.FAILURE'), $e->getTraceAsString(), $e->getMessage());
        }
    }

    // public function refund(LuggageRequest $request)
    // {
    //     try {

    //         $requestWallet = $request->payments()->whereHas('wallet', function ($q) use ($request) {
    //             $q->whereHas('transaction', function ($q) use ($request) {
    //                 $q->where("balance", $request->amountToPay());
    //             });
    //         })->first();

    //         if (!$request->isPaid() or empty($requestWallet)) {
    //             return $this->liteResponse(config('code.request.NOT_AUTHORIZED'), $requestWallet, "Sorry but this book is not paid");
    //         }


    //         $buyer = $request->parcel->owner;

    //         $amountToRefund = $requestWallet->wallet->transaction->balance;

    //         // $amountToRefund = $requestWallet->wallet->transaction->balance - $requestWallet->wallet->transaction->balance * Setting::getServicePercentageFees();

    //         $amountToTransfer = $amountToRefund;

    //         //cancellation more than 24h before departure refund all except fees
    //         if (Carbon::parse($request->trip->departure_date)->diffInHours(Carbon::now()->toDateTimeString()) > 24) {
    //             $amountToTransfer = 0;
    //         } elseif (Carbon::parse($request->trip->departure_date)->diffInHours(Carbon::now()->toDateTimeString()) <= 24) {
    //             #cancellation less than 24h
    //             $amountToTransfer = 0;
    //             if (Carbon::parse($request->created_at)->diffInHours(Carbon::now()->toDateTimeString()) < 24) {
    //                 if (Carbon::parse($request->trip->departure_date)->diffInMinutes(Carbon::now()->toDateTimeString()) <= 30) {
    //                     //cancellation when booking made less than 24h of the departure and less than 30 minute before de departure refund 50% except fees
    //                     $amountToTransfer = $amountToRefund - $amountToRefund * (50 / 100);
    //                     $amountToRefund = $amountToTransfer;
    //                 }
    //             } else {
    //                 #cancellation less than 24h before departure refund 50% except fees
    //                 $amountToTransfer = $amountToRefund - $amountToRefund * (50 / 100);
    //                 $amountToRefund = $amountToTransfer;
    //             }

    //         } else {
    //             $this->transfer($requestWallet->wallet->transaction);
    //         }


    //         $reason = "refund of the request [$requestWallet->luggage_request_id] to [$buyer->id, $buyer->phone, $buyer->email]";

    //         //withdrawal from app account the amount to send to receiver
    //         $appWalletData = [
    //             'owner_id' => User::LUGGIN,
    //             'balance' => $amountToRefund,
    //             'reason' => $reason,
    //         ];

    //         $this->buildWallet($appWalletData, $requestWallet->wallet->id);
    //         $providerController = new LugginController();
    //         $transactionResponse = $this->initTransaction($amountToRefund, $providerController->getAccount(User::LUGGIN), MovementType::WITHDRAWAL, $reason);
    //         $senderWalletResponse = new DefResponse($this->store($appWalletData, $transactionResponse));
    //         if ($senderWalletResponse->isSuccess()) {
    //             //withdrawal from app account the amount to send to receiver
    //             $receiverWalletData = [
    //                 'owner_id' => $buyer->id,
    //                 'balance' => $amountToRefund,
    //                 'reason' => $reason,
    //             ];

    //             $this->buildWallet($receiverWalletData, $senderWalletResponse->getData()['id']);
    //             //Insert a positive balance and specifying transaction type to DEPOSIT to represent the operation in user
    //             $receiverResponse = new DefResponse($this->store($receiverWalletData, $this->initTransaction($amountToRefund, $providerController->getAccount(User::LUGGIN), MovementType::DEPOSIT, $reason)));
    //             if ($receiverResponse->isSuccess()) {
    //                 //send money to validator
    //                 if ($amountToTransfer > 0) {
    //                     $validatorWalletData = [
    //                         'owner_id' => $request->validator()->id,
    //                         'balance' => $amountToRefund,
    //                         'reason' => $reason,
    //                     ];
    //                     $this->buildWallet($validatorWalletData, $senderWalletResponse->getData()['id']);
    //                     $validatorResponse = new DefResponse($this->store($validatorWalletData, $this->initTransaction($amountToTransfer, $providerController->getAccount(User::LUGGIN), MovementType::DEPOSIT, $reason)));
    //                     if ($receiverResponse->isSuccess()) {
    //                         Transaction::where("id", $senderWalletResponse->getData()["transaction_id"])
    //                             ->orWhere("id", $receiverResponse->getData()['transaction_id'])
    //                             ->orWhere("id", $validatorResponse->getData()['transaction_id'])
    //                             ->update(
    //                                 [
    //                                     "verify_at" => Carbon::now(),
    //                                     "payment_token" => $validatorResponse->getData()["transaction_id"] . "-" . $validatorResponse->getData()['transaction_id'],
    //                                     "verification_log" => [$validatorResponse->getData(), $validatorResponse->getData()],
    //                                 ]
    //                             );
    //                     }
    //                     return $validatorResponse->getResponse();
    //                 } else {
    //                     Transaction::where("id", $senderWalletResponse->getData()["transaction_id"])
    //                         ->orWhere("id", $receiverResponse->getData()['transaction_id'])
    //                         ->update(
    //                             [
    //                                 "verify_at" => Carbon::now(),
    //                                 "payment_token" => $senderWalletResponse->getData()["transaction_id"] . "-" . $receiverResponse->getData()['transaction_id'],
    //                                 "verification_log" => [$senderWalletResponse->getData(), $receiverResponse->getData()],
    //                             ]
    //                         );
    //                 }
    //             }
    //             return $receiverResponse->getResponse();
    //         }
    //         return $senderWalletResponse->getResponse();
    //     } catch (\Exception $e) {
    //         return $this->liteResponse(config('code.request.FAILURE'), $e->getTraceAsString(), $e->getMessage());
    //     }
    // }

    public function transfer(Transaction $transaction)
    {
        try {

            $wallet = Wallet::where("transaction_id", $transaction->id)->first();
            if (empty($wallet)) {
                return $this->liteResponse(config("code.request.NOT_FOUND"));
            }

            $walletRequest = LuggageRequestWallet::where("wallet_id", $wallet->id)->first();

            if (empty($walletRequest)) {
                return $this->liteResponse(config("code.request.NOT_FOUND"));
            }

            $buyer = $walletRequest->request->parcel->owner;

            $amountToTransfer = $walletRequest->request->amountToTransfer();

            // $amountToTransfer = $transaction->balance;

            $reason = "payment of the request [$walletRequest->luggage_request_id] by [$buyer->id, $buyer->phone, $buyer->email]";

            //withdrawal from app account the amount to send to receiver
            $appWalletData = [
                'owner_id' => User::LUGGIN,
                'balance' => $amountToTransfer,
                'reason' => $reason,
            ];

            $this->buildWallet($appWalletData, $wallet->id);
            $providerController = new LugginController();
            $transactionResponse = $this->initTransaction($amountToTransfer, $providerController->getAccount(User::LUGGIN), MovementType::WITHDRAWAL, $reason);
            $senderWalletResponse = new DefResponse($this->store($appWalletData, $transactionResponse));
            if ($senderWalletResponse->isSuccess()) {
                //withdrawal from app account the amount to send to receiver
                $receiverWalletData = [
                    'owner_id' => $walletRequest->request->trip->traveler->id,
                    'balance' => $amountToTransfer,
                    'reason' => $reason,
                ];

                $this->buildWallet($receiverWalletData, $senderWalletResponse->getData()['id']);
                //Insert a positive balance and specifying transaction type to DEPOSIT to represent the operation in user
                $receiverResponse = new DefResponse($this->store($receiverWalletData, $this->initTransaction($amountToTransfer, $providerController->getAccount(User::LUGGIN), MovementType::DEPOSIT, $reason)));
                if ($receiverResponse->isSuccess()) {
                    Transaction::where("id", $senderWalletResponse->getData()["transaction_id"])
                        ->orWhere("id", $receiverResponse->getData()['transaction_id'])
                        ->update(
                            [
                                "verify_at" => Carbon::now(),
                                "payment_token" => $senderWalletResponse->getData()["transaction_id"] . "-" . $receiverResponse->getData()['transaction_id'],
                                "verification_log" => [$senderWalletResponse->getData(), $receiverResponse->getData()],
                            ]
                        );
                }
                return $receiverResponse->getResponse();
            }
            return $senderWalletResponse->getResponse();
        } catch (\Exception $e) {
            return $this->liteResponse(config('code.request.FAILURE'), $e->getTraceAsString(), $e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/user/wallet/my-balance",
     *   tags={"Wallet"},
     *   summary="Solidarity",
     *   description="",
     *   operationId="myWalletBalance",
     *     @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\Schema(type="json"),
     *
     *   ),
     * )
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function myBalance()
    {
        return $this->liteResponse(config('code.request.SUCCESS'), ['balance' => "{$this->getUserBalance($this->getUser())}",]);
    }

    public function getUserBalance($user)
    {
        return Wallet::join('transactions', 'transactions.id', 'wallets.transaction_id')->where('wallets.owner_id', $user->id)->sum('transactions.balance');
    }

    /**
     * @OA\Post(
     *     path="/api/user/wallet/my-activity",
     *   tags={"Wallet"},
     *   summary="Solidarity",
     *   description="",
     *   operationId="myWalletActivities",
     *     @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\Schema(type="json"),
     *
     *   ),
     * )
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function activities()
    {
        return $this->liteResponse(config('code.request.SUCCESS'), Wallet::with(['owner', 'luggageRequests'])
            ->where('owner_id', $this->getUser()->id)
            ->orderBy('created_at', 'DESC')
            ->paginate(15));
    }

    protected function validator(&$data, array $rules = [])
    {
        return Validator::make($data, [
            'id' => 'required',
            'credit_wallet_id' => ['required'],
            'owner_id' => ['required', 'exists:users,id'],
        ]);
    }
}
