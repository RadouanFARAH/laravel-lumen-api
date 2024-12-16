<?php

namespace App\Http\Controllers;

use App\Models\MovementType;
use App\Models\Provider;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    private $balance;
    private $account;
    private $channel;
    private $reason;
    private $verify_at;
    private $movementTypeId;

    /**
     * TransactionController constructor.
     * @param $amount
     * @param $account
     * @param $method
     * @param $reason
     * @param $countryId
     */
    public function __construct($amount, $account, $method, $reason)
    {
        $this->balance = $amount;
        $this->account = $account;
        $this->channel = $method;
        $this->reason = $reason;
    }

    public function deposit($context = self::TRANSACTION)
    {
        $this->movementTypeId = MovementType::DEPOSIT;
        return $this->init($this->dataBuilder(), $context);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param array $data
     * @param $context
     * @return array|\Illuminate\Http\JsonResponse
     */
    private function init(array $data, $context)
    {
        try {
            //Setting correct balance according to the movement type
            if ($data['movement_type_id'] == MovementType::WITHDRAWAL)
                $data['balance'] = -abs($this->balance);
            $data['id'] = $this->genId($context);
            $validator = $this->validator($data);
            if ($validator->fails())
                return $this->liteResponse(config('code.request.VALIDATION_ERROR'), $validator->errors());
            $transaction = $this->create($data);

            if (Provider::LUGGIN == $transaction->channel) {
                $transaction->payment_token = $transaction->account;
                $transaction->verification_log = request()->all();
                self::verify($transaction);
            }

            return $this->liteResponse(config('code.request.SUCCESS'), $transaction);
        } catch (QueryException $exception) {
            return $this->liteResponse( config('code.request.FAILURE'), $exception->getMessage());
        }
    }



    protected function validator(&$data, array $rules = array())
    {
        return Validator::make($data, [
            'id' => ['required', 'string', "min:8"],
            'balance' => ['required', 'numeric'],
            'account' => ['required', 'string'],
            'channel' => ['required'],
            'reason' => ['required', 'string', 'max:255'],
            'movement_type_id' => ['required', 'exists:movement_types,id'],
        ]);
    }

    /**
     * @param array $data
     * @return \Illuminate\Http\Response|null
     */
    public function create(array $data)
    {
        return Transaction::create($data);
    }

    public static function verify(Transaction $transaction)
    {
        //Notification::send($transaction, new InvoicePaid($invoice));
        $transaction->verify_at = Carbon::now();
        $transaction->save();
    }

    private function dataBuilder()
    {
        return [
            'balance' => $this->balance,
            'account' => $this->account,
            'channel' => $this->channel,
            'reason' => $this->reason,
            'movement_type_id' => $this->movementTypeId,
        ];
    }

    public function withdrawal($context = self::TRANSACTION)
    {
        $this->movementTypeId = MovementType::WITHDRAWAL;
        return $this->init($this->dataBuilder(), $context);
    }

    public function internalMovement($context = self::TRANSACTION)
    {
        $this->movementTypeId = MovementType::INTERNAL_MOVEMENT;
        return $this->init($this->dataBuilder(), $context);
    }
}
