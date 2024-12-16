<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\LuggageRequest;
use App\Models\Message;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConversationController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/user/messaging/conversations",
     *   tags={"Messaging"},
     *   summary="My conversations",
     *   description="List user conversations",
     *   operationId="myConversations",
     *   @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\Schema(type="string"),
     *   ),
     * )
     * Display a listing of the resource.
     *
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index()
    {
        return $this->liteResponse(config("code.request.SUCCESS"), Conversation::with("luggageRequest", "messages", "creator", "participants", "trip", "parcel")->where("creator_id", auth()->id())->orWhereHas("participants", function ($q) {
            $q->where("user_id", auth()->id());
        })->paginate(50));
    }

    /**
     * @OA\Post(
     *     path="/api/user/messaging/conversations/details",
     *   tags={"Messaging"},
     *   summary="My conversations details",
     *   description="User conversations details",
     *   operationId="myConversations",
     *   @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\Schema(type="string"),
     *   ),
     * )
     * Display a listing of the resource.
     *
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function conversationDetails(Request $request)
    {
        return $this->liteResponse(config("code.request.SUCCESS"), Conversation::with("luggageRequest", "messages", "creator", "participants", "trip", "parcel")->where('id', $request->conversation_id)->paginate(1));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param User   $participant
     * @param string $messageType
     * @param null   $channel
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object
     */
    public function store(User $participant)
    {

        $trip = Trip::find(\request()->get("trip_id"));

        $creatorId = auth()->id();
        $channel = $creatorId . "_with_" . $participant->id . "_trip_" . \request()->get("trip_id");
        $channel2 = $participant->id . "_with_". $creatorId . "_trip_" . \request()->get("trip_id");
        
        //Find if this user
        // $conversation = Conversation::getConversation($creatorId, $participant->id, $channel);
        // $conversation = Conversation::getConversation($creatorId, $participant->id, $participant->trip_id, $participant->parcel_id);
        
        $conversation = Conversation::where('channel',$channel)->orwhere('channel',$channel2)->first();
        
        if (empty($conversation)) {
            //Create a new conversation
            $conversation = $this->create(
                [
                    "creator_id" => $creatorId,
                    "channel" => $channel,
                ]
            );
            $participantController = new ParticipantController();
            //add participant to the conversation
            $participantController->store($participant, $conversation);
        }

        if (\request()->has("type") and \request()->get("type") == Message::OFFER) {
            if ($conversation->request_id != null) {
                (new LuggageRequestController())->acceptOrDecline(\request()->merge(['state' => LuggageRequest::STATE_DENIED, 'luggage_request_id' => $conversation->request_id]));
            }
        }

        $conversation->update(array_filter([
            "trip_id" => \request()->get("trip_id"),
            "parcel_id" => \request()->get("parcel_id"),
            "request_id" => \request()->get("request_id"),
        ]));
        return $conversation;
    }

    public function create(array $data)
    {
        return Conversation::updateOrCreate($data,);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Conversation $conversation
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Conversation $conversation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Conversation $conversation
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Conversation $conversation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Conversation $conversation
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Conversation $conversation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Conversation $conversation
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Conversation $conversation)
    {
        //
    }

    protected function validator(&$data)
    {
        return Validator::make($data, [
            "channel" => ["required"],
            "creator_id" => ['required', "exists:users,id"],
            "parcel_id" => ['nullable', "exists:parcels,id"],
            "trip_id" => ['nullable', "exists:trips,id"],
            "luggage_request_id" => ['nullable', "exists:luggage_requests,id"],
        ]);
    }
}
