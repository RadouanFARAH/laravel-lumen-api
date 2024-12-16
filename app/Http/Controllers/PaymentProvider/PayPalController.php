<?php

namespace App\Http\Controllers\PaymentProvider;

use App\Models\Provider;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Srmklive\PayPal\Services\ExpressCheckout;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Carbon;

class PayPalController extends BaseController
{

    //
    public function payIn($transaction, $user)
    {
        $product = [];
        $amount = $transaction->balance;

        $product['items'] = [
            [
                'name' => $user->user_name,
                'price' => number_format($amount, 2),
                'qty' => 1,
            ],
        ];

        $product['invoice_id'] = $transaction->id;
        $product['invoice_description'] = "Order #{$product['invoice_id']} " . $transaction->reason;
        $product['return_url'] = route('paypal.success');
        $product['cancel_url'] = route('paypal.cancel');
        $product['total'] = number_format($amount, 2);

        $provider = new ExpressCheckout;      // To use express checkout.
        $res = $provider->setExpressCheckout($product, true);

        if (empty($res['paypal_link'])) {
            return $this->liteResponse(config("code.request.FAILURE"), 'Paypal not available', [$product, $res]);
        }

        return $this->liteResponse(config("code.request.SUCCESS"), ['url' => $res['paypal_link']], null);
    }

    public function payOut($transaction, $user)
    {

        $accessToken = $this->getAccessToken();
       
        $response = Http::withToken($accessToken)
            ->post("https://api-m.sandbox.paypal.com/v1/payments/payouts", [
                'sender_batch_header' => [
                    "sender_batch_id"=> uniqid(),
                    "email_subject"=> "You have a payout!",
                    "email_message"=> "You have received a payout! Thanks for using our service!"
                ],
                'items' => [
                    [
                        'recipient_type' => 'EMAIL',
                        'receiver' => request()->account,
                        'note' => 'Thanks for your patronage!',
                        'amount' => [
                            'value' => abs($transaction->balance),
                            'currency' => 'USD',
                        ],
                        'sender_item_id' => uniqid(),
                    ],
                ],
        ]);

        if($response->transferStats->getResponse()->getStatusCode()=== 201){
            Transaction::where("id", $transaction->id)
            ->update([
                "verify_at" => Carbon::now(),
                "payment_token" => "",
                "verification_log" => "",
            ]);
            return $this->liteResponse(config("code.request.SUCCESS"),$response->transferStats->getResponse()->getStatusCode(), null);
        }

        return $this->liteResponse(config("code.request.FAILURE"),$response->transferStats->getResponse()->getStatusCode(), null);
    }

    public function onSuccess(Request $request)
    {
        $token = $request->get('token');
        $PayerID = $request->get('PayerID');
        $paypalModule = new ExpressCheckout();
        $response = $paypalModule->getExpressCheckoutDetails($request->token);


        if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {
            $transaction = Transaction::find($response['INVNUM']);
            if (empty($transaction)) {
                return $this->liteResponse(config("code.request.NOT_FOUND"), "Transaction not found");
            }

            // Perform transaction on PayPal
            // and get the payment status

            $cardItem = $this->getCard($response);

            $payment_status = $paypalModule->doExpressCheckoutPayment($cardItem, $token, $PayerID);
            $status = $payment_status['PAYMENTINFO_0_PAYMENTSTATUS'];
            $paymentInfo = $payment_status['PAYMENTINFO_0_ACK'];
            $ack = $payment_status['ACK'];

            if ("COMPLETED" == strtoupper($status) && "SUCCESS" == strtoupper($paymentInfo) && "SUCCESS" == strtoupper($ack)) {

                $transaction->verification_log = $response;
                $transaction->payment_token = $request->token;
                TransactionController::verify($transaction);

                return $this->liteResponse(config("code.request.SUCCESS"), 'Payment was successful.', $response);
            }
        }
        return $this->liteResponse(config("code.request.FAILURE"), "Something went wrong");
    }

    public function getCard($transactionData)
    {
        return [
            // if payment is recurring cart needs only one item
            // with name, price and quantity
            'items' => [
                [
                    // 'name' => $transactionData['L_NAME0'],
                    'name' => $transactionData['FIRSTNAME'],
                    'price' => $transactionData['PAYMENTREQUEST_0_AMT'],
                    'qty' => $transactionData['L_PAYMENTREQUEST_0_QTY0'],
                ],
            ],

            'invoice_id' => $transactionData['PAYMENTREQUEST_0_INVNUM'],
            'invoice_description' => $transactionData['PAYMENTREQUEST_0_DESC'],
            'return_url' => route('paypal.success', ['provider' => $transactionData['PAYMENTREQUEST_0_CURRENCYCODE']]),
            'cancel_url' => route('paypal.cancel'),
            'total' => $transactionData['PAYMENTREQUEST_0_AMT'],
        ];
    }

    public function onCancel(Request $request)
    {
        return $this->liteResponse(config("code.request.FAILURE"), "Please Retry later");
    }

    public function getAccount($userId = null)
    {
        return "sb-nek5h3412442@personal.example.com";
        // return env('PAYPAL_SANDBOX_API_USERNAME');
    }

    public function getId()
    {
        return Provider::PAYPAL;
    }

    protected function getAccessToken()
    {
        $client_id =  env('PAYPAL_CLIENT_ID');
        $client_secret =  env('PAYPAL_CLIENT_SECRET');
        $response = Http::withBasicAuth($client_id, $client_secret)
            ->asForm()
            ->post('https://api-m.sandbox.paypal.com/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
        ]);

        return $response['access_token'];
    }

}
