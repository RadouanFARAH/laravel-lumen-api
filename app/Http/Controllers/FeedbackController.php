<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function send(Request $request)
    {
        $send = Feedback::create([
            'sender_id'=> auth()->id(),
            'context'=>$request->input('context'),
            'usage_reason'=>$request->input('usage'),
            'comment'=>$request->input('comment')
        ]);

        if ($send) {
            return $this->liteResponse(config("code.request.SUCCESS"), $send);
        }
        return $this->liteResponse(config('code.request.FAILURE'), $send);
    }
}
