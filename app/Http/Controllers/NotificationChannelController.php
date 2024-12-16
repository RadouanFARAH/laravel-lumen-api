<?php

namespace App\Http\Controllers;

use App\Models\BaseModel;
use App\Models\NotificationChannel;
use Illuminate\Http\Request;

class NotificationChannelController extends CoreController
{
    protected $excludedUpdateAttributes = ["user_id"];

    function getModel(): BaseModel
    {
        return new NotificationChannel;
    }

    function updateRule($modelId): array
    {
        return [];
    }

    function addRule(): array
    {
        return [];
    }

    public function onBeforeUpdate(array &$data): void
    {

    }
}
