<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FcmController;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Models\Verification;

class LoginController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/user/login",
     *   tags={"Auth"},
     *   summary="Logs user into the system",
     *   description="Generate user token by login in the systeme",
     *   operationId="loginDrugstore",
     *   @OA\Parameter(
     *     name="login",
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
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('login', 'password');
        $ErrorMessage = "Nom d'utilisateur (pseudo) ou mot de passe incorrect";
        $validator = Validator::make($credentials, [
            'login' => 'bail|required|min:6|max:60',
            'password' => 'bail|required|min:6|max:20',
        ]);

        $emailCredentials = [
            "email" => $credentials["login"],
            "password" => $credentials["password"]
        ];

        $pseudoCredentials = [
            "pseudo" => $credentials["login"],
            "password" => $credentials["password"]
        ];

        $phoneCredentials = [
            "phone" => $credentials["login"],
            "password" => $credentials["password"]
        ];


        if ($validator->fails()) {
            \Log::info("validation error: ", $ErrorMessage);
            return $this->liteResponse(config("code.request.VALIDATION_ERROR"), null, $ErrorMessage);
        }

        //
        //        $user=User::where('pseudo', '=',$credentials["login"])->first();
        //
        //        $is_verify=Verification::where('user_id',$user->id)->first();
        //
        //        if ($is_verify->verified_at==null) {
        //            $deleteUser=$user->delete();
        //            if (!$deleteUser) {
        //                DB::rollBack();
        //
        //            }
        //        }
        //
        //        $exit = User::where('pseudo', '=',$credentials["login"])->exists();
        //
        //        if (!$exit) {
        //            return $this->liteResponse(config('code.auth.WRONG_CREDENTIALS'), null, 'Ce compte est inexistant ');
        //        }

        try {
            if (!$token = JWTAuth::attempt($emailCredentials)) {
                if (!$token = JWTAuth::attempt($pseudoCredentials)) {
                    if (!$token = JWTAuth::attempt($phoneCredentials)) {
                        \Log::info("wrong credentials :", $ErrorMessage);
                        return $this->liteResponse(config('code.auth.WRONG_CREDENTIALS'), null, $ErrorMessage);
                    }
                }
            }

            JWTAuth::setToken($token);

            $user = JWTAuth::toUser();

            //Store Notification token
            (new FcmController())->store($request);

        } catch (Exception $e) {
            \Log::info("login failure :", $e->getMessage());
            return $this->liteResponse(config('code.request.FAILURE'), $e->getMessage());
        }
        \Log::info("SUCCESS ");

        return $this->liteResponse(config('code.request.SUCCESS'), $user, null, $token);
    }

    /**
     * * @OA\Post(
     *     path="/api/user/logout",
     *   tags={"Auth"},
     *   summary="Logout user from system",
     *   description="Disconnecting user to the system by destroying his token session",
     *   operationId="logoutDrugstore",
     *   @OA\Header(
     *         header="api_key",
     *         description="Api key header",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Parameter(
     *     name="token",
     *     required=true,
     *     in="query",
     *     description="user Auth token",
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
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        JWTAuth::parseToken()->invalidate();
        return $this->liteResponse(config('code.request.SUCCESS'));
    }
}
