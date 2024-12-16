<?php

namespace App\Http\Controllers;

use App\Jobs\MailJobs;
use Onfido\Configuration;
use Onfido\Model\Address;
use App\Models\OnfidoUser;
use Onfido\Api\DefaultApi;
use Illuminate\Http\Request;
use Onfido\Model\CheckRequest;
use Onfido\Model\SdkTokenRequest;
use Onfido\Model\ApplicantRequest;
use App\Notifications\SmsNotification;
use App\Services\SmsLoggerService;

class OnfidoController extends Controller
{

    public function onfidoClient()
    {

        $config = Configuration::getDefaultConfiguration();
        $config->setApiKey('Authorization', 'token=' . 'api_sandbox.GdI5n9HR60n.SjI5vcDJDKAtQ4D95wgadfsXkZx2w5xA');
        $config->setApiKeyPrefix('Authorization', 'Token');
        $apiInstance = new DefaultApi(null, $config);

        return $apiInstance;
    }



    public function createApplicant(Request $request)
    {
        $onfidoUser =  OnfidoUser::where("owner_id", auth()->id())->first();

        if ($onfidoUser) {
            $request->request->add(['applicant_id' => $onfidoUser->applicant_id]);
            return $this->generateToken(request());
        }


        // Setting applicant details

        $applicantDetails = new ApplicantRequest();

        $applicantDetails->setFirstName(auth()->user()->last_name);
        $applicantDetails->setLastName(auth()->user()->first_name);
        $applicantDetails->setDob(auth()->user()->birthdate);

        $address = new Address();
        $address->setBuildingNumber('104');
        $address->setStreet('Main Street');
        $address->setTown('Yaounde');
        $address->setPostcode('SW4 6EH');
        $address->setCountry('CMR');

        // $applicantDetails->setFirstName('Cedric');
        // $applicantDetails->setLastName('Doe');
        // $applicantDetails->setDob('1994-01-31');

        // $address = new Address();
        // $address->setBuildingNumber('104');
        // $address->setStreet('Main Street');
        // $address->setTown('Yaounde');
        // $address->setPostcode('SW4 6EH');
        // $address->setCountry('CMR');

        $applicantDetails->setAddress($address);

        // Setting check details

        $checkData = new CheckRequest();
        $checkData->setReportNames(array('identity_standard'));

        // Create an applicant and then a check with an Identity report

        try {

            $applicantResult = $this->onfidoClient()->createApplicant($applicantDetails);
            $applicantId = $applicantResult->getId();
            $checkData->setApplicantId($applicantId);
            $checkResult = $this->onfidoClient()->createCheck($checkData);
            $checkId = $checkResult->getId();
            $checkStatus = $this->onfidoClient()->getCheck($checkId);

            $user = auth()->user();

            if ($checkStatus->getStatus() === 'failed') {

                // Notify via email
                $mail_data = [
                    "user_pseudo" => $user->first_name,
                    "email" => $user->email,
                    "isSuccess" => false
                ];
                dispatch(new MailJobs("App\Mail\Certification", $mail_data));

                // Notify via SMS
                $smsMessage = trans('notifications.certification_failed.sms', ['link' => 'https://example.com']);
                $user->notify(new SmsNotification($smsMessage));

                // Notify via push notification
                $push_data = config('push_notifications/CertificationFailed');
                FcmController::notify($user, $push_data, 'CertificationFailed');
            }


            $onfidoUser = new OnfidoUser();
            $onfidoUser->applicant_id = $applicantId;
            $onfidoUser->owner_id = auth()->id();
            $onfidoUser->save();

            $request->request->add(['applicant_id' => $applicantId]);

            $user = auth()->user();
            $token = request()->route('token');

            // notify via email
            $mail_data = [
                "user_pseudo" => $user->first_name,
                "email" => $user->email,
                "isSuccess" => true
                ];
            dispatch(new MailJobs("App\Mail\Certification", $mail_data));

            // notify via sms
            $smsMessage = trans('notifications.certification_success.sms');
            $user->notify(new SmsNotification($message));

            // notify via push
            $push_data = config('push_notifications/CertificationSuccess');
            FcmController::notify($user, $push_data, 'CertificationSuccess');

            return $this->generateToken($request);
        } catch (Exception $e) {
            print_r($e->getResponseBody());
        }
    }

    public function generateToken(Request $request)
    {

        $referrer = "https://lugginapi-prod.innov237.com/*";

        $applicant_id = $request->applicant_id;

        $sdk_token_request = new SdkTokenRequest();
        $sdk_token_request->setApplicantId($applicant_id);
        $sdk_token_request->setReferrer($referrer);

        try {
            $result = $this->onfidoClient()->generateSdkToken($sdk_token_request);

            return $this->liteResponse(config("code.request.SUCCESS"), $result->getToken());
        } catch (Exception $e) {
            echo 'Exception when calling DefaultApi->generateSdkToken: ', $e->getMessage(), PHP_EOL;
        }
    }
}
