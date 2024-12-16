<?php

namespace App\Http\Controllers;

use App\Models\AccountSetting;
use App\Models\BaseModel;
use Illuminate\Http\Request;

class AccountSettingController extends CoreController
{

    function getModel(): BaseModel
    {
        return new AccountSetting;
    }

    function updateRule($modelId): array
    {
        return [];
    }

    function addRule(): array
    {
        return [];
    }

    public function onBeforeAdd(array &$data): void
    {
        $data["owner_id"] = auth()->id();
    }

    public function add(Request $request)
    {
        if (empty($setting = AccountSetting::where("owner_id", auth()->id())->first())) {
            return parent::add($request);
        } else {
            $request->request->add(["id" => $setting->id]);
            return $this->update($request);
        }
    }
}
