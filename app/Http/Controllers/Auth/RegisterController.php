<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Models\Role;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\IdentificationType;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\FcmController;
use App\Http\Controllers\UserController;
use App\Http\ResponseParser\DefResponse;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\PaymentProvider\StripeController;
use App\Notifications\WelcomeNotification;
use App\Mail\WelcomMessage;
use Illuminate\Support\Facades\Mail;
use App\Jobs\MailJobs;

class RegisterController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/user/register",
     *   tags={"Auth"},
     *   summary="Create user into the system",
     *   description="Generate user token by login in the systeme",
     *   operationId="userRegistration",
     *   @OA\Parameter(
     *     name="pseudo",
     *     required=true,
     *     in="query",
     *     description="The user pseudo max:60",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="last_name",
     *     required=true,
     *     in="query",
     *     description="The user last_name max:60",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="first_name",
     *     required=true,
     *     in="query",
     *     description="The user first_name  max:60",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="phone",
     *     required=true,
     *     in="query",
     *     description="The user phone  max:60",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="email",
     *     required=true,
     *     in="query",
     *     description="The user name for login max:60",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="password",
     *     in="query",
     *     required=true,
     *     @OA\Schema(
     *         type="string",
     *     ),
     *     description="The password for login in clear text min:6, max:20",
     *   ),
     *   @OA\Parameter(
     *     name="address",
     *     in="query",
     *     required=true,
     *     @OA\Schema(
     *         type="string",
     *     ),
     *     description="The address you live text min:6, max:60",
     *   ),
     *   @OA\Parameter(
     *     name="place_residence",
     *     in="query",
     *     required=true,
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
     *     @OA\Parameter(
     *     name="token",
     *     required=false,
     *     in="query",
     *     description="The devise token",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="app_version",
     *     required=false,
     *     in="query",
     *     description="The app version",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="platform",
     *     required=false,
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
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function register(Request $request)
    {
        $controller = new UserController();
        $data = $request->all((new User())->getFillable());
        $data['role_id'] = Role::USER;
        $data['identification_type_id'] = IdentificationType::ID_CARD;
        $data['country_id'] = 76;

        //        $validator = Validator::make($data, [
        //            'identification_doc' => 'bail|required|mimes:jpg,jpeg,bmp,png,pdf,doc,odt,docx|max:2048',
        //        ]);
        //
        //        if ($validator->fails())
        //            return $this->liteResponse(config("code.request.VALIDATION_ERROR"), $validator->errors());
        //
        $data["identification_doc"] = "";

        $defResponse = new DefResponse($controller->add($data));

        if ($defResponse->isSuccess()) {
            $token = JWTAuth::attempt($data);
            JWTAuth::setToken($token);
            (new FcmController())->store($request);
            $request->request->add(["key" => $request->referral, "child_id" => auth()->id()]);
            (new ReferralController())->add($request);
            $user = JWTAuth::toUser();
            
            dispatch(new MailJobs("App\Mail\WelcomMessage", $user));
        
            return $this->liteResponse(config('code.request.SUCCESS'), $user, null, $token);
        }
        return $defResponse->getResponse();
    }

    public function updatePassword(Request $request)
    {

        $user = User::where('id', auth()->id())->first();

        if (!Hash::check($request->oldpassword, $user->password)) {
            return $this->liteResponse(config('code.request.FAILURE'),  null, "Ancien mot de passe incorrect");
        }

        $update =  $user->update([
            'password' => Hash::make($request->newpassword),
        ]);

        return $this->liteResponse(config('code.request.SUCCESS'), null,  "Mot de passe modifié");
    }

    public function accountDelete(Request $request)
    {
        $credentials = $request->only('login', 'password');

        try {

            $user = User::where('pseudo', '=', $credentials["login"])->first();

            $is_verify = Verification::where('user_id', $user->id)->first();
            if ($is_verify->verified_at == null) {
                $deleteUser = $user->delete();
                if ($deleteUser) {
                    return $this->liteResponse(config('code.request.SUCCESS'), $user, null);
                } else {
                }
            }
        } catch (Exception $e) {
            return $this->liteResponse(config('code.request.FAILURE'), $e->getMessage());
        }
    }
}
