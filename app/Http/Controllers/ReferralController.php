<?php

namespace App\Http\Controllers;

use App\Models\BaseModel;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\Request;

class ReferralController extends CoreController
{
    public function onBeforeAdd(array &$data): void
    {
        $user = User::where("user_code", $data["key"])->first();
        if (!empty($user))
            $data["parent_id"] = $user->id;
    }

    function getModel(): BaseModel
    {
        return new Referral;
    }

    function updateRule($modelId): array
    {
        return [];
    }

    function addRule(): array
    {
        return [];
    }

    function list(){
       $userReferral = Referral::with(['child'])->where('parent_id',auth()->id())->get();
       return $this->liteResponse(config("code.request.SUCCESS"), $userReferral);
    }
}
