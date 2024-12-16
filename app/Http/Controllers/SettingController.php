<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function create(array $data)
    {
        return Setting::updateOrCreate(["id" => 1], $data);
    }

    protected function validator(&$data)
    {
        return Validator::make($data, [
            "service_percentage_fees" => ["nullable", "min:1", "max:100"],
        ]);
    }

    /**
     *
     * @OA\Post(
     *    path="/api/dashboard/setting/update",
     *   tags={"Dashboard"},
     *   summary="Setting Update",
     *   description="",
     *   operationId="settingAUpdate",
     *   @OA\Parameter(
     *         name="service_percentage_fees",
     *         in="query",
     *         description="the fees taken on every request",
     *         required=false,
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
     *
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $data = $request->all((new Setting())->getFillable());
    
        $response = $this->save(array_filter($data));
    
        if ($response->isSuccess()) {
            $users = User::all();     
    
            foreach ($users as $user) {
                $emailData = [
                    'conditions' => $request->get('conditions', []),
                    'terms_link' => $request->get('terms_link', '#'),
                    'privacy_link' => $request->get('privacy_link', '#'),
                    'previous_terms_link' => $request->get('previous_terms_link', '#'),
                    'name' => $user->first_name,
                ];
                dispatch(new MailJobs("App\Mail\ConditionsGeneralesUpdated", $emailData));

                $push_data = config('push_notifications/ConditionsGeneralesUpdated');
                FcmController::notify($user, $push_data, 'ConditionsGeneralesUpdated');
            }
        }
    
        return $response;
    }
    

    /**
     *
     * @OA\Post(
     *     path="/api/dashboard/setting/detail",
     *   tags={"Dashboard"},
     *   summary="Setting list",
     *   description="",
     *   operationId="settingList",
     *     @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\Schema(type="json"),
     *
     *   ),
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->liteResponse(config("code.request.SUCCESS"), Setting::first());
    }


    /**
     * Notify all users about updated terms or privacy policy.
     */
    protected function notifyUsersAboutPolicyUpdates($data)
    {
        $user = auth()->user();

        // Email Notification
        $mail_data = [
            "name" => $user->first_name,
            "email" => $user->email
        ];

        $initiator == 'TRAVELER' ? dispatch(new MailJobs("App\Mail\OffreEnvoyeeFailed", $mail_data)) : dispatch(new MailJobs("App\Mail\DemandeEnvoyeeFailed", $mail_data));

        // SMS Notification
        $smsMessage = $initiator == 'TRAVELER' ? trans('notifications.offre_envoyee_failed.sms', ['link' => 'https://example.com']) : trans('notifications.demande_envoyee_failed.sms', ['link' => 'https://example.com']);
        $user->notify(new SmsNotification($smsMessage));


        // Push Notification
        $push_data = $initiator == 'TRAVELER' ? config('push_notifications/OffreEnvoyeeFailed') : config('push_notifications/DemandeEnvoyeeFailed');
        FcmController::notify($user, $push_data, $initiator == 'TRAVELER' ? 'OffreEnvoyeeFailed' : 'DemandeEnvoyeeFailed');
    }
}
