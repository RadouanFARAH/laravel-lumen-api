<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Fcm;
use App\Models\User;
use LaravelFCM\Facades\FCM as FIREBASE;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use LaravelFCM\Message\OptionsBuilder;
use Illuminate\Support\Facades\Validator;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class FcmController extends Controller
{

    public function create(array $data)
    {
        return Fcm::create($data);
    }

    /**
     * @OA\Post(
     *     path="/api/user/notification/token/add",
     *   tags={"Notifications"},
     *   summary="Firebase",
     *   description="Add user token in list",
     *   operationId="addFcmToken",
     *     @OA\Parameter(
     *     name="token",
     *     required=true,
     *     in="query",
     *     description="The devise token",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="app_version",
     *     required=true,
     *     in="query",
     *     description="The app version",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="platform",
     *     required=true,
     *     in="query",
     *     description="The running plateform [ANDROID, IOS, WEB]",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\Schema(type="string"),
     *   ),
     * )
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(Request $request)
    {

        $request->request->add(["token"=>$request->device_token,"platform"=>$request->platform]);

        $data = $request->all((new Fcm())->getFillable());
        
        $validator = $this->validator($data);

        if ($validator->fails())
            return $this->liteResponse(config('code.request.VALIDATION_ERROR'), $validator->errors());
        try {
            $data['user_id'] = auth()->id();
            $data['token']= $request->input('device_token');
            Fcm::where('token', $data['token'])->delete();
            return $this->save($data);
        } catch (Exception $exception) {
            return $this->liteResponse(config('code.request.EXCEPTION'), $exception->getMessage());
        }
    }

    protected function validator(&$data)
    {
        return Validator::make($data, [
            "token" => "required|min:8",
            "platform" => ["required",Rule::in(Fcm::getPlatforms())],
            "app_version" => "required"
        ]);
    }

    /**
     * @param array  $users
     * @param        $data
     * @param string $context
     */
    public static function notifies(array $users, $data, $context = "")
    {
        foreach ($users as $user) {
            self::notify($user, $data, $context);
        }
    }

    /**
     * @param User $user
     * @param $data
     * @param string $context
     */
    public static function notify(User $user, $data, $context = "")
    {
        $data['date'] = Carbon::now()->format('Y-m-d H:i:s');
        $data['click_action'] = "FLUTTER_NOTIFICATION_CLICK"; 

        return self::alert($user, [
            "channel" => $context,
            "data" => $data,
            "priority" => "high"
        ]);
    }

    /**
     * @param User $user
     * @param $data
     * @param string $context
     */
    public static function alert(User $user, array $data)
    {
        try {
            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60*20);
            
            $notificationBuilder = new PayloadNotificationBuilder($data['data']['title']);
            $notificationBuilder->setBody($data['data']['message'])->setSound('default');

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData($data);
 
            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

            $tokens = $user->devices->pluck('token')->toArray();

            // $tokens ="ceGah67uQlaelju5CyZIQ7:APA91bGBelhxj7uSoC6Ny5pjX7FwuweG59qSYsH_sG7pUzdBVbW1-CBdO2iGVhBH2AMBXwFqj8JfP5IQTaSGTb9L8TCQdM9M8OTrjEgllhx93o7sHIoVKJHT-CABR0peCkBBwVAgmz2-";
            
            if (empty($tokens)) {
                return;
            } 

            $downstreamResponse = FIREBASE::sendTo($tokens, $option,$notification, $data);
            // return $downstreamResponse;

        } catch (Exception $exception) {
            return $exception;
            // file_put_contents(public_path("confidential/firebaseException.md"), $exception->getMessage());
        }
    }
}
