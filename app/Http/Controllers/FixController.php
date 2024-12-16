<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FixController extends Controller {

	/**
     * Get user book.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function myBooking(Request $request, $type="SENDER")
    {
    	$action = ("SENDER" == $type) ? "SENDER" : "TRAVELER";

    	if ( "SENDER" == $action)

	        return $this->liteResponse(config("code.request.SUCCESS"), \App\Models\LuggageRequest::whereHas('parcel', function ($q) {
	            	 $q->where("sender_id", auth()->id());
	        })->with(['parcel','trip'])->orderByDesc("created_at")->paginate(20));
	    else 
	    	return $this->liteResponse(config("code.request.SUCCESS"), \App\Models\LuggageRequest::whereHas('trip', function ($q) {
		            $q->where("traveler_id", auth()->id());
	        })->with(['parcel','trip'])->orderByDesc("created_at")->paginate(20));

    }

    /**
     * Get user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function user(Request $request, $id)
    {
    	return [];

    }
 
    /**
     * Get user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function password(Request $request)
    {
        $rule = [
            'password' => 'required|min:6',
            'repassword' => 'required|min:6',
        ];

        $validator = Validator::make($request->all(), $rule);

        if ($validator->fails())
            return $this->liteResponse(config('code.request.VALIDATION_ERROR'), $validator->errors());

        
        if (auth()->password == Hash::make($data['password']))
            return $this->liteResponse(config('code.request.FAILURE'), null, 'Please use another password');

        auth()->update([
                'password' => Hash::make($data['re-password']),
        ]);

        return $this->liteResponse(config("code.request.SUCCESS"),null, 'Password updated');


    }
 
    public function search(Request $request)
    {
        $context = $request->context;
        if (!in_array($context, \App\Models\SearchHistory::getContext())) {
            return $this->liteResponse(config('code.request.NOT_FOUND'), null, "Sorry but context should be in " . join(",", SearchHistory::getContext()));
        }
        if (!in_array($request->parcel_restriction, \App\Models\Trip::getParcelRestriction())) {
            return $this->liteResponse(config('code.request.NOT_FOUND'), null, "Sorry but restriction should be in " . join(",", Trip::getParcelRestriction()));
        }

        $result = null;

        if ($context == \App\Models\SearchHistory::PARCEL) {
            $result = \App\Models\Parcel::available()->with(["arrivalCity", "departureCity", "owner", "luggageRequests"]);
            if ($request->parcel_restriction == \App\Models\Trip::PARCEL_SAME_FLY) {
                $result->where("parcel_restriction", $request->parcel_restriction);
            }

        } else { 
            $result = \App\Models\Trip::available()->with(["arrivalCity", "departureCity", "arrivalAirport", "departureAirport","traveler","luggageRequests"]);
            if ($request->parcel_restriction == \App\Models\Trip::PARCEL_SAME_FLY)
                $result->where("parcel_restriction", $request->parcel_restriction);
        }

        if ($request->from != null) {
            $result->where("departure_city_id", $request->from);
        }

        if ($request->to != null) {
            $result->where("arrival_city_id", $request->to);
        }

        if ($request->fly_number != null) {
            $result->where("fly_number", $request->fly_number);
        }
        
        if ($request->date != null) {
            $result->whereBetween("departure_date", 
                [ "$request->date 00:00:00", "$request->date 23:59:59"]
            );
        }

        //save to history

        (new \App\Http\Controllers\SearchHistoryController())->add($request);
        //$this->add($request);

        return $this->liteResponse(config('code.request.SUCCESS'), $result->paginate(20));
    }

    public function send(Request $request)
    {
        try {
            $receiverParticipant = User::findOrFail($request->receiver_id);
            $luggageRequest = null;

            if ($request->has("type") and $request->type == Message::OFFER) {
                //store luggage request
                $request->request->add(['proposal_unit_price' => $request->price]);
                $luggageRequest = new DefResponse((new LuggageRequestController())->add($request));
                if (!$luggageRequest->isSuccess()) {
                    return $luggageRequest->getResponse();
                }
                //add luggage request id to store it in message
                $request->request->add(['luggage_request_id' => $luggageRequest->getData()["id"]]);
            }

            //Store or get conversation
            $conversation = (new ConversationController())->store($receiverParticipant);

            //Build message data
            $data = \request()->only((new Message())->getFillable());
            $data["conversation_id"] = $conversation->id;
            $data["sender_id"] = auth()->id();
            $this->saveParcelsImages($data);

            //Store the new massage
            $defResponse = new DefResponse($this->save($data));

            if ($defResponse->isSuccess()) {
                $message = Message::find($defResponse->getData()["id"]);

                //Notify receiver with Firebase
                $message->sender;
                $message->trip;
                $message->conversation->creator;
                $notificationMessage = Message::find($message->id);
                $notificationMessage->conversation->creator;
                $notificationMessage->is_complete = Message::MESSAGE_MAX_LENGTH > strlen($notificationMessage->message);
                $notificationMessage->message = Str::limit($message->message, Message::MESSAGE_MAX_LENGTH);
                FcmController::notify($receiverParticipant, $notificationMessage, $data["type"]);
                //FcmController::notify(auth()->user(), $notificationMessage, self::MESSAGE);

                //Use by the sender app to update message
                $message->temp_id = $request->temp_id == null ? 0 : $request->temp_id;

                return $this->liteResponse(config("code.request.SUCCESS"), $message);
            } else
                return $defResponse->getResponse();
        } catch (Exception $exception) {
            return $this->liteResponse(config("code.request.FAILURE"), $exception->getMessage());
        }
    }
}

?>