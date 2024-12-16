<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Auth;

class ForgotPasswordController extends Controller
{
    /**
     *  * @OA\Post(
     *     path="/api/user/password/forgot",
     *   tags={"Auth"},
     *   summary="Send reset password email",
     *   description="User should exist in the systeme",
     *   operationId="forgotPassword",
     *   @OA\Parameter(
     *     name="login",
     *     required=true,
     *     in="query",
     *     description="The user email",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\Schema(type="string"),
     *     )
     *
     * )
     * @param Request $request
     * @return array|JsonResponse
     * @throws \Exception
     */
    public function forgotPassword(Request $request)
    {
        $data = $request->all('login');

        $validator = $this->validator($data);
        if ($validator->fails())
            return $this->liteResponse(config('code.request.VALIDATION_ERROR'), $validator->errors());

        try {
            $user = User::where('email', $data['login'])->orWhere('phone', $data['login'])->orWhere('pseudo', $data['login'])->first();
            $data['code'] = random_int(100000, 999999);
            app('Auth.password.broker')->createToken($user);
            //TODO send reset password
            //Notification::send($user, new ResetPassword(PasswordReset::where('email', $data['login'])->orWhere('phone', $data['login'])->first()->token, $data['code']));
            return $this->liteResponse(config('code.request.SUCCESS'), null, "Un code vous a été envoyé. Veuillez le renseigner pour réinitialiser votre mot de passe.");
        } catch (QueryException $exception) {
            return $this->liteResponse(config('code.request.FAILURE'), $exception->getMessage());
        }
    }

    /**
     * Destroy reset in database
     * @param array $data
     */
    public function destroy(array $data)
    {
        ResetPassword::where('token', $data['token'])->delete();
    }

    public function create(array $data)
    {
        return ResetPassword::create($data);
    }

    protected function validator(&$data)
    {
        return Validator::make($data, [
            'login' => 'required|max:255',
        ]);
    }
}
