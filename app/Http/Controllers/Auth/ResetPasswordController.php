<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class ResetPasswordController extends Controller
{
    /**
     * @param Request $request
     * @return array|JsonResponse
     */
    public function resetPassword(Request $request)
    { 
        $data = $request->all(['email', 'code', 'password', 'password_confirmation']);

        $rule = [
            'email' => 'required|email|max:255|exists:users,email',
            'password' => 'required|confirmed|min:6',
            'code' => 'required|numeric'
        ];

        $user = User::where('email', $data['email'])->first();
        $validator = Validator::make($data, $rule);
        if ($validator->fails())
            return $this->liteResponse(config('code.request.VALIDATION_ERROR'), $validator->errors());

        try {
            //get unexpired token
            $result = DB::table('password_resets')
                ->where('code', $data['code'])
                ->where('email', $data['email'])
                ->where('created_at', '>', Carbon::now()->subHours(2))
                ->first();

            //check if code exist
            if (empty($result) || $result === null) {
                return $this->liteResponse(config('code.request.EXPIRED'), "Wrong code or expired");
            }
            DB::table('password_resets')->where('email', $data['email'])->delete();
            $user->update([
                'password' => Hash::make($data['password'])
            ]);

            try {
                return $this->login($request);
            } catch (JWTException $e) {
                return $this->liteResponse(config('code.request.FAILURE'), $e->getMessage());
            }
        } catch (QueryException $exception) {
            return $this->liteResponse(config('code.request.FAILURE'), $exception->getMessage());
        }
    }
}
