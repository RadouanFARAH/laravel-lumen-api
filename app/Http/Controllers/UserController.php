<?php

namespace App\Http\Controllers;

use Aws\Exception\AwsException;
use Aws\Sns\SnsClient;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Http\ResponseParser\DefResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Notifications\EmailVerificationNotification;
use App\Notifications\PhoneVerificationNotification;

class UserController extends Controller
{
    public function resetPassword(Request $request)
    {
        $data = $request->all(['email', 'password', 'password_confirmation']);

        $user = User::where('email', $data['email'])->first();
  
        try {
            if (empty($user))
                return $this->liteResponse(config('code.token.USER_NOT_FOUND'));

            if ($user->password == Hash::make($data['password']))
                return $this->liteResponse(config('code.request.FAILURE'), null, 'Please use another password');

            $user->update([
                'password' => Hash::make($data['password']),
            ]);
            return $this->liteResponse(config('code.request.SUCCESS'));
        } catch (\Exception $exception) {
            return $this->liteResponse(config('code.request.FAILURE'), $exception->getMessage());
        }
    }

    public function add(array $data)
    {
        return $this->save($data);
    }

    public function save(array $data)
    {
        $verificationController = new VerificationController();
        $validator = $this->validator($data);
        if ($validator->fails()) {
            if ($validator->errors()->has('pseudo')
            ) {
                return $this->liteResponse(config('code.request.TRYING_TO_INSERT_DUPLICATE'), null, "Ce pseudo existe déjà");
            }
            if ($validator->errors()->has('email') && $validator->errors()->first('email') == 'The email must be a valid email address.') {
                return $this->liteResponse(config('code.request.VALIDATION_ERROR'), null, "L'adresse mail renseignée n'est pas valide!");
            }
            return $this->liteResponse(config('code.request.VALIDATION_ERROR'), $validator->errors());
        }

        try {
            $user = $this->create($data);

            $verificationController->push(Verification::EMAIL, $data["email"], $data["email"], $user->id);
            $verificationController->push(Verification::PHONE, $data["phone"], $data["phone"], $user->id);
            //$verificationController->push(Verification::DOC, $data["identification_doc"], $data["identification_doc"], $user->id);

            return $this->liteResponse(config('code.request.SUCCESS'), $user);
        } catch (\Exception $exception) {
            return $this->liteResponse(config('code.request.FAILURE'), $exception->getMessage());
        }
    }

    protected function validator(&$data)
    {
        return Validator::make($data, [
            'pseudo' => 'required|string|regex:`^([a-zA-Z0-9-_]{4,24})$`|unique:users',
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'email' => 'required|string|email:rfc|max:255|unique:users',
            'phone' => 'required|string|min:9|max:15|unique:users',
            'about_me' => 'nullable|string|max:300',
            'password' => 'required|string|min:6',
            'birthdate' => 'nullable|date',
            'role_id' => 'required|exists:roles,id',
        ], ['regex' => "Only allow alphabetical characters and underscore (_)"]);
    }

    public function create(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    /**
     * @OA\Post(
     *     path="/api/user/update",
     *   tags={"User"},
     *   summary="Check user data",
     *   description="Generate user token by login in the system",
     *   operationId="userChecking",
     *   @OA\Parameter(
     *     name="pseudo",
     *     required=false,
     *     in="query",
     *     description="The user pseudo max:60",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="last_name",
     *     required=false,
     *     in="query",
     *     description="The user last_name max:60",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="first_name",
     *     required=false,
     *     in="query",
     *     description="The user first_name  max:60",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="phone",
     *     required=false,
     *     in="query",
     *     description="The user phone  max:60",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="email",
     *     required=false,
     *     in="query",
     *     description="The user name for login max:60",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="password",
     *     in="query",
     *     required=false,
     *     @OA\Schema(
     *         type="string",
     *     ),
     *     description="The password for login in clear text min:6, max:20",
     *   ),
     *   @OA\Parameter(
     *     name="address",
     *     in="query",
     *     required=false,
     *     @OA\Schema(
     *         type="string",
     *     ),
     *     description="The address you live text min:6, max:60",
     *   ),
     *   @OA\Parameter(
     *     name="place_residence",
     *     in="query",
     *     required=false,
     *     @OA\Schema(
     *         type="string",
     *     ),
     *     description="The place of residence you live text min:6, max:60",
     *   ),
     *   @OA\Parameter(
     *     name="about_me",
     *     in="query",
     *     required=false,
     *     @OA\Schema(
     *         type="string",
     *     ),
     *     description="The user bio, max:300",
     *   ),
     *   @OA\Parameter(
     *     name="birthdate",
     *     in="query",
     *     required=false,
     *     @OA\Schema(
     *         type="string",
     *     ),
     *     description="The user bio, max:300",
     *   ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *
     *                @OA\Property(
     *                    property="identification_doc",
     *                    type="file",
     *                    description="Documento para enviar, menor igual que 2MB, formatos (.doc, .docx, .pdf)",
     *                    @OA\Items(type="string", format="file"),
     *                 ),
     *                @OA\Property(
     *                    property="profile",
     *                    type="file",
     *                    description="Documento para enviar, menor igual que 2MB, formatos (image)",
     *                    @OA\Items(type="string", format="file"),
     *                 ),
     *     ),
     *     ),
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\Schema(type="string"),
     *   ),
     * )
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function updateProfile(Request $request)
    {
        $data = $request->all((new User())->getFillable());

        $validator = Validator::make($data, [
            'identification_doc' => 'nullable|mimes:jpg,jpeg,bmp,png,pdf,doc,odt,docx|max:2048',
            'profile' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails())
            return $this->liteResponse(config("code.request.VALIDATION_ERROR"), $validator->errors());

        $data["identification_doc"] = $this->saveMedia($request->file("identification_doc"), self::DOC_DIRECTORY);
        $data["profile"] = $this->saveMedia($request->file("profile"), self::PROFILE_DIRECTORY);
        return $this->update(array_filter($data), auth()->user());
    }

    /**
     * Update the preferred language.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePreferredLanguage(Request $request)
    {
        $request->validate([
            'preferred_language' => 'required|string|in:en,fr,es',
        ]);

        $user = Auth::user();
        $user->preferred_language = $request->preferred_language;
        $user->save();

        return response()->json([
            'message' => trans('api.language_updated'),
            'preferred_language' => $user->preferred_language,
        ], 200);
    }
    protected function update(array $data, $user)
    {

        if (empty($data))
            return $this->liteResponse(config('code.request.VALIDATION_ERROR'), $data, "Nothing to update");

        $validator = Validator::make($data, [
            'pseudo' => 'nullable|string|regex:`^([a-zA-Z0-9-_]{4,24})$`|unique:users,pseudo,' . $user->id,
            'last_name' => 'nullable|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|min:9|max:15|unique:users,phone,' . $user->id,
            'about_me' => 'nullable|string|max:300',
            'password' => 'nullable|string|min:6',
            'identification_doc' => 'nullable|string',
            'profile' => 'nullable|string',
            'birthdate' => 'nullable|date',
            'role_id' => 'nullable|exists:roles,id',
        ], ['regex' => "Only allow alphabetical characters and underscore (_)"]);

        if ($validator->fails())
            return $this->liteResponse(config('code.request.VALIDATION_ERROR'), $validator->errors());
        try {

            $verificationController = new VerificationController();
            if (array_key_exists("email",$data) and !empty($data['email'])) {
                $response = new DefResponse($verificationController->push(Verification::EMAIL, $user->email, $data["email"], $user->id));
                if ($response->isSuccess()) {
                    $this->sendVerificationNotification($response->getData());
                }
            }

            if (array_key_exists("phone",$data) and !empty($data['phone'])) {
                $response = new DefResponse($verificationController->push(Verification::PHONE, $user->phone, $data["phone"], $user->id));
                if ($response->isSuccess()) {
                    $this->sendVerificationNotification(Verification::find($response->getData()['id']));
                }
            }

            if (array_key_exists("identification_doc",$data) and !empty($data['identification_doc']))
                (new VerificationController())->push(Verification::EMAIL, $user->identification_doc, $data["identification_doc"], $user->id);


            if (array_key_exists("password",$data) and !empty($data["password"])) {
                $data["password"] = Hash::make($data["password"]);
            }

            $user->update($data);
            return $this->liteResponse(config('code.request.SUCCESS'), $user->fresh());
        } catch (\Exception $exception) {
            return $this->liteResponse(config('code.request.FAILURE'), $exception->getMessage());
        }
    }

    protected function sendVerificationNotification(Verification $verification)
    {
        try {
            $user = auth()->user();
            $user->setToVerified($verification->update_value);
            switch ($verification->type) {
                case Verification::PHONE:
                    Notification::sendNow($user, new PhoneVerificationNotification($verification->otp));
//                    $sms = AWS::createClient('sns');
//
//                    try {
//                        $sms->publish([
//                            'Message' => "Your verification code {$verification->otp}",
//                            'PhoneNumber' =>$verification->update_value,
//                            'MessageAttributes' => [
//                                'AWS.SNS.SMS.SMSType'  => [
//                                    'DataType'    => 'String',
//                                    'StringValue' => 'Transactional',
//                                ]
//                            ],
//                        ]);
//                    } catch (\Exception $e) {
//                        //   return $this->reply(false,'error',$e->getMessage());
//                    }

                    break;
                case Verification::EMAIL:
                    Notification::sendNow($user, new EmailVerificationNotification($verification));
            }
            return $this->liteResponse(config('code.request.SUCCESS'));
        } catch (\Exception $exception) {
            return $this->liteResponse(config('code.request.FAILURE'), $exception->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/user/check/data",
     *   tags={"Auth"},
     *   summary="Check user data",
     *   description="Generate user token by login in the system",
     *   operationId="userChecking",
     *   @OA\Parameter(
     *     name="pseudo",
     *     required=false,
     *     in="query",
     *     description="The user pseudo max:60",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="last_name",
     *     required=false,
     *     in="query",
     *     description="The user last_name max:60",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="first_name",
     *     required=false,
     *     in="query",
     *     description="The user first_name  max:60",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="phone",
     *     required=false,
     *     in="query",
     *     description="The user phone  max:60",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="email",
     *     required=false,
     *     in="query",
     *     description="The user name for login max:60",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="password",
     *     in="query",
     *     required=false,
     *     @OA\Schema(
     *         type="string",
     *     ),
     *     description="The password for login in clear text min:6, max:20",
     *   ),
     *   @OA\Parameter(
     *     name="address",
     *     in="query",
     *     required=false,
     *     @OA\Schema(
     *         type="string",
     *     ),
     *     description="The address you live text min:6, max:60",
     *   ),
     *   @OA\Parameter(
     *     name="place_residence",
     *     in="query",
     *     required=false,
     *     @OA\Schema(
     *         type="string",
     *     ),
     *     description="The place of residence you live text min:6, max:60",
     *   ),
     *   @OA\Parameter(
     *     name="about_me",
     *     in="query",
     *     required=false,
     *     @OA\Schema(
     *         type="string",
     *     ),
     *     description="The user bio, max:300",
     *   ),
     *   @OA\Parameter(
     *     name="birthdate",
     *     in="query",
     *     required=false,
     *     @OA\Schema(
     *         type="string",
     *     ),
     *     description="The user bio, max:300",
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\Schema(type="string"),
     *   ),
     * )
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function validateInput(Request $request)
    {
        $data = array_filter($request->all((new User())->getFillable()));
        $validator = Validator::make($data, [
            'pseudo' => 'nullable|string|regex:`^([a-zA-Z0-9-_]{4,24})$`|unique:users',
            'last_name' => 'nullable|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'phone' => 'nullable|string|min:9|max:15|unique:users',
            'about_me' => 'nullable|string|max:300',
            'birthdate' => 'nullable|date',
            'password' => 'nullable|string|min:6',
        ], ['regex' => "Only allow alphabetical characters and underscore (_)"]);
        if ($validator->fails())
            return $this->liteResponse(config('code.request.VALIDATION_ERROR'), $validator->errors());
        return $this->liteResponse(config('code.request.SUCCESS'), $data);
    }

    /**
     * @OA\Post(
     *     path="/api/user/verify",
     *   tags={"Auth"},
     *   summary="Verify user",
     *   description="Enter OTP receive via SMS or EMAIL",
     *   operationId="verify",
     *   @OA\Parameter(
     *     name="update_value",
     *     required=true,
     *     in="query",
     *     description="The user pseudo max:60",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="type",
     *     required=true,
     *     in="query",
     *     description="DOC; PHONE; EMAIL;",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="otp",
     *     required=true,
     *     in="query",
     *     description="The user first_name  max:60",
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
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function verify(Request $request)
    {
        try {
            $user = auth()->user();
            $verification = Verification::where("update_value", $request->update_value)
                ->where("current_value", $user->{$this->mapVerificationTypeWithUser()[$request->type]})
                ->where("otp", $request->otp)
                ->where("user_id", $user->id)
                // ->where("verified_at", null)
                ->first();
            if (empty($verification))
                return $this->liteResponse(config('code.request.NOT_FOUND'));

            $verification->verified_at = Carbon::now();
            $verification->current_value = $verification->update_value;
            $verification->save();
            return $this->liteResponse(config('code.request.SUCCESS'));
        } catch (\Exception $exception) {
            return $this->liteResponse(config('code.request.FAILURE'), $exception->getMessage());
        }
    }

    private function mapVerificationTypeWithUser()
    {
        return [Verification::PHONE => "phone", Verification::DOC => "identification_doc", Verification::EMAIL => "email"];
    }

    /**
     * @OA\Post(
     *     path="/api/user/verify/me",
     *   tags={"Auth"},
     *   summary="Send Verify OTP to user",
     *   description="Send OTP via SMS or EMAIL",
     *   operationId="verifyMe",
     *   @OA\Parameter(
     *     name="update_value",
     *     required=true,
     *     in="query",
     *     description="The user pseudo max:60",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="type",
     *     required=true,
     *     in="query",
     *     description="DOC; PHONE; EMAIL;",
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
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function verifyMe(Request $request)
    {
        try {
            $user = auth()->user();

            $verification = Verification::where("type", $request->type)
                ->where("update_value", $user->{$this->mapVerificationTypeWithUser()[$request->type]})
                ->where("user_id", $user->id)
                // ->where("verified_at", null)
                ->first();
            if (empty($verification))
                return $this->liteResponse(config('code.request.NOT_FOUND'));

            return $this->sendVerificationNotification($verification);
        } catch (\Exception $exception) {
            return $this->liteResponse(config('code.request.FAILURE'), $exception->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/user/me",
     *   tags={"Auth"},
     *   summary="User info",
     *   description="My account info",
     *   operationId="me",
     *   @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\Schema(type="string"),
     *   ),
     * )
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function me(Request $request)
    {
        return $this->liteResponse(config('code.request.SUCCESS'), auth()->user());
    }

    public function idVerification($token){
        return view('OnfidoView',['token'=> $token]);
    }
}
