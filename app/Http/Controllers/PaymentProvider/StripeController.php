<?php


namespace App\Http\Controllers\PaymentProvider;


use App\Http\Controllers\TransactionController;
use App\Models\Provider;
use App\Models\User;
use Cartalyst\Stripe\Stripe;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StripeController extends BaseController
{
  
    public function payIn($transaction, $user)
    {
        $validator = Validator::make(array_merge(request()->all(), ["amount" => $transaction->balance]), [
            'card_no' => 'required',
            'ccExpiryMonth' => 'required',
            'ccExpiryYear' => 'required',
            'cvvNumber' => 'required',
            // 'amount' => 'min:300',
        ]);
        if ($validator->passes()) {

            try {
                $stripe = (new Stripe())->setApiKey(env('STRIPE_SECRET'));
                $token = $stripe->tokens()->create([
                    'card' => [
                        'number' => request()->get('card_no'),
                        'exp_month' => request()->get('ccExpiryMonth'),
                        'exp_year' => request()->get('ccExpiryYear'),
                        'cvc' => request()->get('cvvNumber'),
                    ],
                ]);
                if (!isset($token['id'])) {
                    return $this->liteResponse(config("code.request.FAILURE"), $token);
                }
                $charge = $stripe->charges()->create([
                    'card' => $token['id'],
                    //'customer' =>  $transaction->id,
                    'currency' => 'EUR',
                    //'receipt_email' => $user->email,
                    'amount' => $transaction->balance,
                    'description' => $transaction->reason,
                ]);

                if ($charge['status'] == 'succeeded') {
                    $transaction->payment_token = $charge['id'];
                    $transaction->verification_log = json_encode($charge);
                    TransactionController::verify($transaction);
                    return $this->liteResponse(config("code.request.SUCCESS"), $charge);
                } else {
                    return $this->liteResponse(config("code.request.FAILURE"), $charge);
                }
            } catch (Exception $e) {
                return $this->liteResponse(config("code.request.EXCEPTION"), $e->getTraceAsString(), $e->getMessage());
            }
        }
        return $this->liteResponse(config("code.request.FAILURE"), $validator->errors());
    }

    public function createCustomer(Request $request){

      try {

        $stripe = (new Stripe())->setApiKey(env('STRIPE_SECRET'));

        $email = $request->email;

        $customers =  $stripe->customers()->create([
            'email' => $email,
            'payment_method' => 'pm_card_visa',
            'invoice_settings' => ['default_payment_method' => 'pm_card_visa'],
        ]);

        dd($customers);
      
      } catch (\Exception $e) {
        return $this->liteResponse(config("code.request.EXCEPTION"), $e->getTraceAsString(), $e->getMessage());
      }
    }

    public function getCustomer(Request $request){

        try {

            $stripe = (new Stripe())->setApiKey(env('STRIPE_SECRET'));
    
            $email = $request->email;
    
            $list =  $stripe->customers()->all(['email' => $email]);
    
            dd($list);
          
        } catch (\Exception $e) {
            return $this->liteResponse(config("code.request.EXCEPTION"), $e->getTraceAsString(), $e->getMessage());
        }
          
    }

    public function deleteAccount(Request $request){

        try {

            $stripe = (new Stripe())->setApiKey(env('STRIPE_SECRET'));
    
            $email = $request->email;
    
            $list = $stripe->customers()->delete($request->customer_id);
    
            dd($list);
          
        } catch (\Exception $e) {
            return $this->liteResponse(config("code.request.EXCEPTION"), $e->getTraceAsString(), $e->getMessage());
        }
    }

    public function transfer(Request $request){

        try {

         $stripe = (new Stripe())->setApiKey(env('STRIPE_SECRET'));

        //    $payment =  $stripe->paymentIntents()->create([
        //         'amount' => $request->amount,
        //         'currency' => 'usd',
        //         'customer'=> $request->customer_id,
        //         'transfer_group' => 'ORDER10',
        //     ]);
            
        //     dd($payment);

            $transfer = $stripe->payouts()->create([
                'amount'    => $request->amount,
                'currency'  => 'USD',
                'destination' => $request->destination,
            ]);

            dd($transfer);

        } catch (Exception $e) {
            return $this->liteResponse(config("code.request.EXCEPTION"), $e->getTraceAsString(), $e->getMessage());
        }

    }


    public function payOut($transaction, $user)
    {
        if (empty($user->stripeConnect)) {
            return $this->liteResponse(config("code.request.NOT_FOUND"), null, "Stripe connect account required. Please create");
        }


    }

    public function connectAccount()
    {
        return $this->liteResponse(config("code.request.SUCCESS"),self::createAccount(auth()->user()));
    }

    public static function createAccount(User $user)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $account = $stripe->accounts->create([
            'type' => 'express',
            'business_type' => 'individual',
            'country' => $user->country->alpha2,
            'email' => $user->email,
            'capabilities' => [
                'card_payments' => ['requested' => true],
                'transfers' => ['requested' => true],
            ],
            "metadata" => ["id" => $user->id],
            "business_profile" => \request()->get("business_profile")
        ]);

        $user->stripeConnect = $account->id;
        $user->save();
        return $account->toArray();
    }

    public function getAccount($userId = null)
    {
        return env('STRIPE_SECRET');
    }

    public function onSuccess(Request $request)
    {
        // TODO: Implement onSuccess() method.
    }

    public function onCancel(Request $request)
    {
        // TODO: Implement onCancel() method.
    }

    public function getId()
    {
        return Provider::STRIPE;
    }
}
