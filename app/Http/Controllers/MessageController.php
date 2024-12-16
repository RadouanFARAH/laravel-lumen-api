<?php

namespace App\Http\Controllers;

use App\Http\ResponseParser\DefResponse;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MessageController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/user/messaging/messages",
     *   tags={"Messaging"},
     *   summary="Get conversation message",
     *   description="List conversation messages",
     *   operationId="myConversationMessages",
     *     @OA\Parameter(
     *     name="conversation_id",
     *     required=true,
     *     in="query",
     *     description="The id of the conversation",
     *     @OA\Schema(
     *         type="integer"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\Schema(type="string"),
     *   ),
     * )
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $conversation = Conversation::with(['trip', 'parcel'])->where("creator_id", auth()->id())
            ->where("id", $request->conversation_id)
            ->orWhereHas("participants", function ($q) {
                $q->where("user_id", auth()->id());
            })->where("id", $request->conversation_id)
            ->first();
        if (empty($conversation)) {
            return $this->liteResponse(config("code.request.FAILURE"));
        }
        return $this->liteResponse(config("code.request.SUCCESS"), Message::with(["conversation"])->where('conversation_id', $request->conversation_id)->paginate(200));
    }


    /**
     * @OA\Post(
     *     path="/api/user/messaging/message/detail",
     *   tags={"Messaging"},
     *   summary="Get complete",
     *   description="complete message",
     *   operationId="messageDetails",
     *     @OA\Parameter(
     *     name="message_id",
     *     required=true,
     *     in="query",
     *     description="The id of the message",
     *     @OA\Schema(
     *         type="integer"
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
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function getMessage(Request $request)
    {
        $message = Message::with(["conversation", "trip", 'luggageRequest'])->find($request->message_id);
        if (empty($message) or empty(Conversation::where("creator_id", auth()->id())
            ->where("id", $message->conversation_id)
            ->orWhereHas("participants", function ($q) {
                $q->where("user_id", auth()->id());
            })->where("id", $message->conversation_id)
            ->first())) {
            return $this->liteResponse(config("code.request.FAILURE"));
        }
        return $this->liteResponse(config("code.request.SUCCESS"), $message);
    }

    /**
     * @OA\Post(
     *     path="/api/user/messaging/message/delete",
     *   tags={"Messaging"},
     *   summary="Delete so",
     *   description="delete message",
     *   operationId="messageDelete",
     *     @OA\Parameter(
     *     name="message_id",
     *     required=true,
     *     in="query",
     *     description="The id of the message",
     *     @OA\Schema(
     *         type="integer"
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
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $message = Message::find($request->message_id);
        if (empty($message) or empty(Conversation::where("creator_id", auth()->id())
            ->where("id", $message->conversation_id)
            ->orWhereHas("participants", function ($q) {
                $q->where("user_id", auth()->id());
            })->where("id", $message->conversation_id)
            ->first())) {
            return $this->liteResponse(config("code.request.FAILURE"));
        }
        return $this->liteResponse(config("code.request.SUCCESS"), $message->delete());
    }

    /**
     * @OA\post(
     *     path="/api/user/messaging/message/send",
     *   tags={"Messaging"},
     *   summary="send User message",
     *   description="send User message",
     *   operationId="SendMessage",
     *      @OA\Parameter(
     *     name="message",
     *     required=true,
     *     in="query",
     *     description="message to send",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *     @OA\Parameter(
     *     name="type",
     *     required=true,
     *     in="query",
     *     description="MESSAGE or OFFER",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),   @OA\Parameter(
     *     name="receiver_id",
     *     required=true,
     *     in="query",
     *     description="The id of the receiver",
     *     @OA\Schema(
     *         type="integer"
     *     )
     *   ),@OA\Parameter(
     *     name="trip_id",
     *     required=false,
     *     in="query",
     *     description="The id of the trip",
     *     @OA\Schema(
     *         type="integer"
     *     )
     *   ),@OA\Parameter(
     *     name="parcel_id",
     *     required=false,
     *     in="query",
     *     description="The id of the parcel",
     *     @OA\Schema(
     *         type="integer"
     *     )
     *   ), @OA\Parameter(
     *     name="weight",
     *     required=false,
     *     in="query",
     *     description="The weight needed",
     *     @OA\Schema(
     *         type="integer"
     *     )
     *   ), @OA\Parameter(
     *     name="price",
     *     required=false,
     *     in="query",
     *     description="The price proposed",
     *     @OA\Schema(
     *         type="integer"
     *     )
     *   ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="description images to upload max size 2MB max number of images 10",
     *                     property="images[]",
     *                     type="array",
     *                     @OA\Items(type="file")
     *                 ),
     *             )
     *         )
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\Schema(type="string"),
     *   ),
     * )
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function send(Request $request)
    {
        try {
            $receiverParticipant = User::findOrFail($request->receiver_id);
            $luggageRequest = null;

            if ($request->has("type") and $request->type == Message::OFFER) {
                //store luggage request

                $luggageRequestController = new LuggageRequestController();

                $request->request->add([
                    'proposal_unit_price' => ($request->price / $request->weight),
                    'luggage_request_id' => $luggageRequestController->getRequestId($request)
                ]);

                //$luggageRequestController->cancellation($request);
                $luggageRequest = new DefResponse($luggageRequestController->add($request));
                if (!$luggageRequest->isSuccess()) {
                    return $luggageRequest->getResponse();
                }
                //add luggage request id to store it in message
                $request->request->add(['request_id' => $luggageRequest->getData()["id"]]);
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
                FcmController::notify(auth()->user(), $notificationMessage, $data["type"]);

                //Use by the sender app to update message
                $message->temp_id = $request->temp_id == null ? 0 : $request->temp_id;

      
                $mail_data = [
                    'sender' => 
                    'message' => 
                    'datetime' => 
                    'departCity' => 
                    'arriveCity' => 
                    'tripdate' => 
                ];
                dispatch(new MailJobs("App\Mail\NouveauMessage", $mail_data));

                $smsMessage = trans('notifications.nouveau_message.sms', ['link' => 'https://example.com']);
                $user->notify(new SmsNotification($smsMessage));

                $push_data = config('push_notifications/NouveauMessage');
                FcmController::notify($user, $push_data, 'NouveauMessage');


                return $this->liteResponse(config("code.request.SUCCESS"), $message);
            } else {
                return $defResponse->getResponse();
            }
        } catch (Exception $exception) {

            return $this->liteResponse(config("code.request.FAILURE"), $exception->getMessage());
        }
    }

    /**
     * @param array $data
     */
    public function saveParcelsImages(array &$data)
    {
        //Store parcels images
        $allFiles = \request()->allFiles();
        if ($allFiles != null and array_key_exists("images", $allFiles)) {
            $otherFilesData = [];
            foreach ($allFiles['images'] ?? [] as $key => $image) {
                array_push($otherFilesData, $this->saveMedia($image, self::PARCEL_DIRECTORY));
            }
            $data['attachment_url'] = json_encode($otherFilesData);
        } else {
            $data['attachment_url'] = json_encode([]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param array $data
     */
    public function create(array $data)
    {
        return Message::create($data);
    }

    protected function validator(&$data)
    {
        return Validator::make($data, [
            "conversation_id" => ["required", "exists:conversations,id"],
            "sender_id" => ["required", "exists:users,id"],
            "message" => ["required"],
            "type" => ["required", Rule::in([
                Message::MESSAGE,
                Message::OFFER,
            ])],
        ]);
    }
}
