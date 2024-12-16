<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Trip;
use App\Models\City;
use App\Models\User;
use App\Jobs\MailJobs;
use App\Models\Parcel;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\LuggageRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\FcmController;
use App\Http\ResponseParser\DefResponse;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Notifications\SmsNotification;

class LuggageRequestController extends Controller
{
    /**
     * @OA\post(
     *     path="/api/user/book/requests",
     *   tags={"Booking"},
     *   summary="Request list",
     *   description="User booking request",
     *   operationId="bookingRequest",
     *     @OA\Parameter(
     *     name="state",
     *     required=false,
     *     in="query",
     *     description="Get specific state",
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
     * Display a listing of the resource.
     *
     * @return array|\Illuminate\Http\JsonResponse|Response
     */
    public function myRequests()
    {
        return $this->liteResponse(config("code.request.SUCCESS"), LuggageRequest::with(["trip", "parcel"])->whereHas('parcel', function ($q) {
            $q->where("sender_id", auth()->id());
        })->orWhereHas('trip', function ($q) {
            $q->where("traveler_id", auth()->id());
        })->orderByDesc("created_at")->paginate(20));
    }

    /**
     * @OA\post(
     *     path="/api/user/book/detail",
     *   tags={"Booking"},
     *   summary="Request detail",
     *   description="User booking request detail",
     *   operationId="bookingRequestDetail",
     *     @OA\Parameter(
     *     name="luggage_request_id",
     *     required=true,
     *     in="query",
     *     description="Get specific state",
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
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return array|\Illuminate\Http\JsonResponse|Response
     */
    public function detail(Request $request)
    {
        // $luggageRequest = LuggageRequest::with(['parcel', 'trip'])->where("id", $request->luggage_request_id)
        //     ->whereHas('parcel', function ($q) {
        //         $q->where("sender_id", auth()->id());
        //     })->orWhereHas('trip', function ($q) {
        //         $q->where("traveler_id", auth()->id());
        //     })->first();

        $luggageRequest = LuggageRequest::with(['parcel', 'trip'])->where("id", $request->luggage_request_id)->first();

        if (empty($luggageRequest)) {
            return $this->liteResponse(config('code.request.NOT_FOUND'), null, "Sorry we can't for found this request");
        }

        return $this->liteResponse(config("code.request.SUCCESS"), $luggageRequest);
    }

    public function getRequestId(Request $request)
    {
        $luggageRequest = LuggageRequest::with(['parcel', 'trip'])
            ->where("trip_id", $request->trip_id)
            ->whereHas('parcel', function ($q) {
                $q->where("sender_id", auth()->id());
            })

            ->orWhereHas('trip', function ($q) {
                $q->where("traveler_id", auth()->id());
            })
            ->where("trip_id", $request->trip_id)

            ->orWhere('parcel_id', $request->parcel_id)
            ->whereHas('parcel', function ($q) {
                $q->where("sender_id", auth()->id());
            })

            ->orWhereHas('trip', function ($q) {
                $q->where("traveler_id", auth()->id());
            })
            ->where("parcel_id", $request->parcel_id)

            ->first();

        if (empty($luggageRequest)) {
            return 0;
        }

        return $luggageRequest->id;
    }

    public function create(array $data)
    {
        return LuggageRequest::create($data);
    }

    public function saved($response)
    {
        FcmController::notify(LuggageRequest::find($response->getData()["id"])->validator(), $response->getData(), self::NOTIFY_CONTEXT_LUGGAGE_REQUEST);
    }

    /**
     * @OA\Post(
     *     path="/api/user/book/now",
     *   tags={"Booking"},
     *   summary="Book a trip or a parcel",
     *   description="Book traveler space or book sender spares",
     *   operationId="bookNow",
     *     @OA\Parameter(
     *     name="initiator",
     *     required=true,
     *     in="query",
     *     description="Instigate by TRAVELER or SENDER",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="weight",
     *     required=false,
     *     in="query",
     *     description="Parcel space needed",
     *     @OA\Schema(
     *         type="integer"
     *     )
     *   ),@OA\Parameter(
     *     name="proposal_unit_price",
     *     required=false,
     *     in="query",
     *     description="Parcel space needed",
     *     @OA\Schema(
     *         type="integer"
     *     )
     *   ),@OA\Parameter(
     *     name="transaction_fees",
     *     required=false,
     *     in="query",
     *     description="Seremo fees",
     *     @OA\Schema(
     *         type="integer"
     *     )
     *   ),@OA\Parameter(
     *     name="parcel_id",
     *     required=false,
     *     in="query",
     *     description="parcel id",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="trip_id",
     *     required=true,
     *     in="query",
     *     description="Trip id",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *     @OA\Parameter(
     *     name="name",
     *     required=false,
     *     in="query",
     *     description="recipient name",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="address",
     *     required=false,
     *     in="query",
     *     description="recipient address",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="phone",
     *     required=false,
     *     in="query",
     *     description="recipient phone",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="recipient_id",
     *     required=false,
     *     in="query",
     *     description="the id of the selected recipient from list",
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
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return array|\Illuminate\Http\JsonResponse|Response
     * @throws \Exception
     */
    public function add(Request $request)
    {

        $data = $request->all((new LuggageRequest())->getFillable());
        $data['state'] = LuggageRequest::STATE_PENDING;

        $trip = Trip::with(['departureAirport', 'arrivalAirport'])->where("id", $data['trip_id'])->first();
        if (empty($trip)) {
            return $this->liteResponse(config('code.request.NOT_FOUND'), null, "Sorry we can't found this trip");
        }

        $parcel = Parcel::with(['departureAirport', 'arrivalAirport'])->where("id", $data['parcel_id'])->first();
        if (empty($parcel)) {
            //set initiator as INIT_BY_SENDER
            $data['initiator'] = LuggageRequest::INIT_BY_SENDER;
            //if user specified his parcel
            if (!empty($data['parcel_id'])) {
                return $this->liteResponse(config('code.request.NOT_FOUND'), null, "Sorry but this parcel is already booked");
            } else {
                //create private parcel for this book
                $request->request->add(
                    [
                        "fly_number" => $trip->fly_number,
                        "departure_city_id" => $trip->departure_city_id,
                        "departure_date" => Carbon::now()->addMinute(),
                        "arrival_city_id" => $trip->arrival_city_id,
                        "weight_unit_price" => $trip->weight_unit_price,
                        "weight" => empty($request->get("weight")) ? $trip->available_weight : $request->get("weight"),
                        "private" => true,
                    ]
                );

                $response = new DefResponse((new ParcelController())->add($request));

                if ($response->isSuccess()) {
                    $parcel = $response->getData();
                    $data['parcel_id'] = $parcel['id'];
                    $parcel = Parcel::find($parcel["id"]);
                } else {
                    return $response->getResponse();
                }
            }
        }

        if (!$trip->hasAvailableSpace($parcel['weight']) or !$parcel->hasAvailableSpace($parcel['weight'])) {
            return $this->liteResponse(config('code.request.NOT_FOUND'), null, "Sorry there is no more space");
        }

        $initiator = $data["initiator"];
        // recheck fly number
        $isFlightInfoValidated = true; // valid par defaut
        if (!$isFlightInfoValidated) {
            $this->sendVerificationFailedNotifications($initiator);
        }


        

        if (!in_array($initiator, LuggageRequest::getInitiator())) {
            return $this->liteResponse(config('code.request.NOT_FOUND'), $data, "Sorry but initiator should be in " . join(",", LuggageRequest::getInitiator()));
        }

        //in case of booking wit private parcel
        if ($data['weight'] == 0) {
            $data['weight'] = $trip->available_weight;
        }

        //in case of booking wit private parcel
        if ($data['proposal_unit_price'] == 0) {
            $data['proposal_unit_price'] = $trip->weight_unit_price;
        }

        if ($initiator == LuggageRequest::INIT_BY_SENDER) {
            if ($trip->auto_accept_booking) {
                $data['state'] = LuggageRequest::STATE_ACCEPTED;
                // $data['proposal_unit_price'] = $trip->weight_unit_price; //ADD Fees here
                //$data['proposal_unit_price'] = $data['proposal_unit_price']; //NEW
                $data['weight'] = $parcel['weight'];
            }
        }

        $data['transaction_fees'] = ($data['proposal_unit_price'] * $data['weight']) * Setting::getServicePercentageFees();


        $result = new DefResponse($this->save($data));
        if ($result->isSuccess()) {
            $trip->booked_weight = $data['weight'] + $trip->booked_weight;
            $trip->save();
            $parcel->booked_weight = $data['weight'] + $parcel["booked_weight"];
            $parcel->save();
        }
        //Add notification message
        $data['trip'] = $trip;
        $data['id'] = $result->getData()['id'];
        $data['parcel'] = $parcel;
        $data['message'] = "Vous avez reçu une demande sur votre annonce";


        $isValidator = $result->getData()['as_validator'];


        $data['title'] = "Nouvelle demande";

        $luggageRequest = LuggageRequest::with(['trip', 'parcel'])->where('id', $data['id'])->first();

        $notifMessage = (new NotificationController())->notifMessage($luggageRequest);

        $request->request->add([
            'notif_title' => $data['title'],
            'notif_message' => $notifMessage,
            'notif_request_id' => $data['id'],
            'notif_type' => "OFFER",
            'notif_owner_id' =>  $trip->traveler_id != auth()->id() ? $trip->traveler_id : $parcel->sender_id,
            'notif_sender_id' =>  auth()->id(),
        ]);

        FcmController::notify($initiator == LuggageRequest::INIT_BY_SENDER ? User::find($trip->traveler_id) : User::find($parcel->sender_id), $data, self::NOTIFY_CONTEXT_LUGGAGE_REQUEST);

        (new NotificationController())->store($request);

        // $request->request->add([
        //     'message'=> $notifMessage,
        //     'type'=>"MESSAGE",
        //     // 'request_id'=> $data['id'],
        //     'receiver_id'=> $trip->traveler_id != auth()->id() ? $trip->traveler_id : $parcel->sender_id,
        // ]);

        // (new MessageController())->send($request);
        // FcmController::notify(LuggageRequest::INIT_BY_SENDER ? User::find($parcel->sender_id):User::find($trip->traveler_id), $data, self::NOTIFY_CONTEXT_LUGGAGE_REQUEST);

        // return $result->getResponse(); //OLD
        // Send Notifications Based on Initiator
        if ($data['state'] == LuggageRequest::STATE_ACCEPTED) {
            $user = auth()->user();
            $source = City::find($trip->departure_city_id);
            $destination = City::find($trip->arrival_city_id);

            if ($initiator == LuggageRequest::INIT_BY_TRAVELER) {
                // Notification for Traveler Booking (OffreEnvoyee)
                $mail_data = [
                    "name" => $user->first_name,
                    "date" => $trip->departure_date,
                    "source" => $source->name,
                    "destination" => $destination->name,
                    "email" => $user->email
                ];
                dispatch(new MailJobs("App\Mail\OffreEnvoyee", $mail_data));

                $smsMessage = trans('notifications.offre_envoyee.sms', ['link' => 'https://example.com']);
                $user->notify(new SmsNotification($smsMessage));

                $push_data = config('push_notifications/OffreEnvoyee');
                FcmController::notify($user, $push_data, 'OffreEnvoyee');
            } elseif ($initiator == LuggageRequest::INIT_BY_SENDER) {
                $travler = User::find($trip->traveler_id);
                $destinataire = $travler->first_name;
                // Notification for Sender Booking (DemandeEnvoyee)
                $mail_data = [
                    "name" => $user->first_name,
                    "date" => $trip->departure_date,
                    "source" => $source->name,
                    "destination" => $destination->name,
                    "destinataire" => $destinataire,
                    "email" => $user->email
                ];
                dispatch(new MailJobs("App\Mail\DemandeEnvoyee", $mail_data));


                $smsMessage = trans('notifications.demande_envoyee.sms', ['link' => 'https://example.com', 'destinataire' => $destinataire]);
                $user->notify(new SmsNotification($smsMessage));

                $push_data = config('push_notifications/DemandeEnvoyee');
                FcmController::notify($user, $push_data, 'DemandeEnvoyee');
            }
        }
        return $this->liteResponse(config("code.request.SUCCESS"), $luggageRequest);
    }

    public function edit(Request $request) {}

    /**
     * @OA\post(
     *     path="/api/user/book/accept-decline",
     *   tags={"Booking"},
     *   summary="Request accept to decline",
     *   description="User booking request update",
     *   operationId="bookingRequestAcceptDecline",
     *     @OA\Parameter(
     *     name="luggage_request_id",
     *     required=true,
     *     in="query",
     *     description="The luggage to update",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *     @OA\Parameter(
     *     name="state",
     *     required=true,
     *     in="query",
     *     description="Set specific state",
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
     *
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function acceptOrDecline(Request $request)
    {

        $luggageRequest = LuggageRequest::where("id", $request->luggage_request_id)
            ->whereHas('parcel', function ($q) {
                $q->where("sender_id", auth()->id());
            })->orWhereHas('trip', function ($q) {
                $q->where("traveler_id", auth()->id());
            })->where("id", $request->luggage_request_id)
            ->first();

        if (empty($luggageRequest)) {
            return $this->liteResponse(config('code.request.NOT_FOUND'), null, "Sorry we can't found this request");
        }

        $state = $request->state;


        if (!in_array($state, LuggageRequest::getState())) {
            return $this->liteResponse(config('code.request.NOT_FOUND'), null, "Sorry but state should be in " . join(",", LuggageRequest::getState()));
        }

        if ($luggageRequest->state != LuggageRequest::STATE_PENDING) {
            return $this->liteResponse(config('code.request.NOT_FOUND'), null, "Sorry but state has already been updated");
        }


        try {

            if ($state == LuggageRequest::STATE_DENIED) {
                $trip = Trip::find($luggageRequest->trip_id);
                Trip::where('id', $luggageRequest->trip_id)->update(['booked_weight' => $trip->booked_weight - $luggageRequest->weight]);
            }

            $luggageRequest->state = $state;
            $luggageRequest->save();


            $notifMessage = (new NotificationController())->notifMessage($luggageRequest);

            //Add notification message
            $request->request->add([
                'title' => 'Réservation',
                'message' => $notifMessage,
                'type' => "MESSAGE",
                'parcel_id' => $luggageRequest->parcel_id,
                'trip_id' => $luggageRequest->trip_id,
                'receiver_id' => $luggageRequest->initiator()->id,
                'owner_id' => $luggageRequest->initiator()->id,
                'sender_id' =>  auth()->id(),
                'request_id' => $luggageRequest->id,
            ]);

            // (new MessageController())->send($request);

            $request->request->add([
                'notif_title' => 'Réservation',
                'notif_message' => $notifMessage,
                'notif_type' => "MESSAGE",
                'notif_owner_id' => $luggageRequest->initiator()->id,
                'notif_sender_id' =>  auth()->id(),
                'notif_request_id' => $luggageRequest->id,
            ]);

            (new NotificationController())->store($request);


            //FOR MESSAGE refresh
            FcmController::notify(User::findOrFail($request->receiver_id), $request, "MESSAGE");
            FcmController::notify(auth()->user(), $request, "MESSAGE");
            // FcmController::notify(User::findOrFail($request->receiver_id), $request, self::NOTIFY_CONTEXT_LUGGAGE_REQUEST);

            return $this->liteResponse(config('code.request.SUCCESS'), $luggageRequest->refresh());
        } catch (\Exception $e) {

            return $this->liteResponse(config('code.request.FAILURE'), $e->getMessage());
        }
    }

    /**
     * @OA\post(
     *     path="/api/user/book/cancel",
     *   tags={"Booking"},
     *   summary="Request cancellation",
     *   description="User booking request cancellation",
     *   operationId="bookingRequestCancellation",
     *     @OA\Parameter(
     *     name="luggage_request_id",
     *     required=true,
     *     in="query",
     *     description="The luggage to cancel",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *     @OA\Parameter(
     *     name="cancellation_reason",
     *     required=true,
     *     in="query",
     *     description="Set reason of the cancellation",
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
     *
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function cancellation(Request $request)
    {
        $luggageRequest = LuggageRequest::where("id", $request->luggage_request_id)
            ->whereHas('parcel', function ($q) {
                $q->where("sender_id", auth()->id());
            })->orWhereHas('trip', function ($q) {
                $q->where("traveler_id", auth()->id());
            })->where("id", $request->luggage_request_id)
            ->first();

        if (empty($luggageRequest)) {
            return $this->liteResponse(config('code.request.NOT_FOUND'), null, "Sorry we can't found this request");
        }

        if (!empty($luggageRequest->cancel_at)) {
            return $this->liteResponse(config('code.request.NOT_FOUND'), $request->luggage_request_id, "Sorry but state has already been canceled");
        }

        //TODO refund user according to the author of the cancellation
        (new WalletController())->refund($luggageRequest);
        try {
            $luggageRequest->cancel_at = Carbon::now();
            $luggageRequest->cancellation_reason = $request->cancellation_reason;
            $luggageRequest->save();
            FcmController::notify($luggageRequest->initiator()->id == auth()->id() ? $luggageRequest->validator() : $luggageRequest->initiator(), $luggageRequest, self::NOTIFY_CONTEXT_LUGGAGE_REQUEST);

            $notifMessage = (new NotificationController())->notifMessage($luggageRequest);

            $request->request->add([
                'title' => "Annulation",
                'message' => "Reservation annulée",
                'type' => "MESSAGE",
                'parcel_id' => $luggageRequest->parcel_id,
                'trip_id' => $luggageRequest->trip_id,
                'receiver_id' => $luggageRequest->initiator()->id,
                "sender_id" => auth()->id(),
                "owner_id" => $luggageRequest->initiator()->id,
                "request_id" => $luggageRequest->id,
            ]);

            (new NotificationController())->store($request);

            return $this->liteResponse(config('code.request.SUCCESS'), $luggageRequest->refresh());
        } catch (\Exception $e) {
            return $this->liteResponse(config('code.request.FAILURE'), $e->getMessage());
        }
    }

    /**
     * @OA\post(
     *     path="/api/user/book/delivery/confirmation",
     *   tags={"Booking"},
     *   summary="Request delivery confirmation",
     *   description="User booking request delivery confirmation",
     *   operationId="bookingRequestDeliveryConfirmation",
     *     @OA\Parameter(
     *     name="luggage_request_id",
     *     required=true,
     *     in="query",
     *     description="The luggage to confirmation",
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
     *
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function confirmDelivery(Request $request)
    {
        $luggageRequest = LuggageRequest::whereHas('parcel', function ($q) use ($request) {
            $q->where("sender_id", auth()->id());
        })->where("id", $request->luggage_request_id)->first();


        if (empty($luggageRequest)) {
            return $this->liteResponse(config('code.request.NOT_FOUND'), null, "Sorry we can't found this request");
        }

        if (!empty($luggageRequest->delivery_at)) {
            return $this->liteResponse(config('code.request.NOT_FOUND'), null, "Sorry but state has already been confirmed");
        }

        try {

            $requestWallet = $luggageRequest->payments()->whereHas('wallet.transaction')->with('wallet.transaction')->first();

            if (!$luggageRequest->isPaid() or empty($requestWallet)) {
                return $this->liteResponse(config('code.request.NOT_AUTHORIZED'), $requestWallet, "Sorry but this booking is not paid");
            }

            $paymentResponse = new DefResponse((new WalletController())->transfer($requestWallet->wallet->transaction));

            if ($paymentResponse->isSuccess()) {
                $luggageRequest->delivery_at = Carbon::now();


                $luggageRequest->save();

                $luggageRequest['title'] = "Livraison";
                $luggageRequest['message'] = "Confirmation de livraison";

                FcmController::notify($luggageRequest->validator(), $luggageRequest, self::NOTIFY_CONTEXT_LUGGAGE_REQUEST);
                FcmController::notify($luggageRequest->initiator(), $luggageRequest, self::NOTIFY_CONTEXT_LUGGAGE_REQUEST);

                $notifMessage = (new NotificationController())->notifMessage($luggageRequest);

                $request->request->add([
                    'title' => $luggageRequest['title'],
                    'message' => $luggageRequest['message'],
                    'type' => "MESSAGE",
                    'parcel_id' => $luggageRequest->parcel_id,
                    'trip_id' => $luggageRequest->trip_id,
                    'receiver_id' => $luggageRequest->initiator()->id,
                    "sender_id" => auth()->id(),
                    "owner_id" => $luggageRequest->initiator()->id,
                    "request_id" => $luggageRequest->id,
                ]);

                (new NotificationController())->store($request);

                return $this->liteResponse(config('code.request.SUCCESS'), $luggageRequest->refresh());
            }

            return $paymentResponse->getResponse();
        } catch (\Exception $e) {
            return $this->liteResponse(config('code.request.FAILURE'), $e->getLine());
        }
    }

    /**
     * @OA\post(
     *     path="/api/user/book/delivery/request",
     *   tags={"Booking"},
     *   summary="Request delivery confirmation request",
     *   description="User booking request delivery confirmation",
     *   operationId="bookingRequestDeliveryConfirmation",
     *     @OA\Parameter(
     *     name="luggage_request_id",
     *     required=true,
     *     in="query",
     *     description="The luggage to request confirmation",
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
     *
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function requestDelivery(Request $request)
    {
        $luggageRequest = LuggageRequest::where("id", $request->luggage_request_id)
            ->whereHas('trip', function ($q) {
                $q->where("traveler_id", auth()->id());
            })->first();

        if (empty($luggageRequest)) {
            return $this->liteResponse(config('code.request.NOT_FOUND'), null, "Sorry we can't found this request");
        }

        try {
            $luggageRequest->delivery_request_at = Carbon::now();
            $luggageRequest->save();

            $request->request->add([
                'title' => "Livraison",
                'message' => "Confirmation de livraison",
                'type' => "MESSAGE",
                'parcel_id' => $luggageRequest->parcel_id,
                'trip_id' => $luggageRequest->trip_id,
                'receiver_id' => $luggageRequest->initiator()->id,
                "sender_id" => auth()->id(),
                "owner_id" => $luggageRequest->initiator()->id,
                "request_id" => $luggageRequest->id,
            ]);

            FcmController::notify($luggageRequest->initiator(), $luggageRequest, self::NOTIFY_CONTEXT_LUGGAGE_REQUEST);
            FcmController::notify($luggageRequest->validator(), $luggageRequest, self::NOTIFY_CONTEXT_LUGGAGE_REQUEST);

            (new NotificationController())->store($request);

            $notifMessage = (new NotificationController())->notifMessage($luggageRequest);


            return $this->liteResponse(config('code.request.SUCCESS'), $luggageRequest->refresh());
        } catch (\Exception $e) {
            return $this->liteResponse(config('code.request.FAILURE'), $e->getMessage());
        }
    }

    public function restore(Request $request)
    {

        try {
            DB::beginTransaction();
            $luggageRequest = LuggageRequest::where("id", $request->luggage_request_id)->first();
            $findTrip = Trip::where("id", $request->trip_id)->first();

            if (!empty($luggageRequest) && !empty($findTrip)) {
                $findTrip->booked_weight -= $luggageRequest->weight;
                $save = $findTrip->Save();
                if ($luggageRequest->delete() && $save) {

                    DB::commit();
                    return $this->liteResponse(config('code.request.SUCCESS'), null, 'luggage restaure');
                }
            } else {
                return $this->liteResponse(config('code.request.FAILURE'), null, "Sorry luggage not found");
            }
        } catch (\Throwable $th) {
            return $th;
            DB::rollBack();
        }
    }

    protected function validator(&$data)
    {
        return Validator::make($data, [
            "weight" => ['required', "numeric"],
            "proposal_unit_price" => ['required', "numeric"],
            "transaction_fees" => ['required', "numeric"],
            "state" => ['required', Rule::in(LuggageRequest::getState())],
            "initiator" => ['required', Rule::in(LuggageRequest::getInitiator())],
            "parcel_id" => ['required', "exists:parcels,id"],
            "trip_id" => ['required', "exists:trips,id"],
        ]);
    }

    private function sendVerificationFailedNotifications($initiator)
    {
        $user = auth()->user();

        // Email Notification
        $mail_data = [
            "name" => $user->first_name,
            "email" => $user->email
        ];

        $initiator == 'TRAVELER'? dispatch(new MailJobs("App\Mail\OffreEnvoyeeFailed", $mail_data)):dispatch(new MailJobs("App\Mail\DemandeEnvoyeeFailed", $mail_data));

        // SMS Notification
        $smsMessage = $initiator == 'TRAVELER'? trans('notifications.offre_envoyee_failed.sms', ['link' => 'https://example.com']) : trans('notifications.demande_envoyee_failed.sms', ['link' => 'https://example.com']);
        $user->notify(new SmsNotification($smsMessage));


        // Push Notification
        $push_data = $initiator == 'TRAVELER'? config('push_notifications/OffreEnvoyeeFailed') : config('push_notifications/DemandeEnvoyeeFailed');
        FcmController::notify($user, $push_data, $initiator == 'TRAVELER'? 'OffreEnvoyeeFailed' : 'DemandeEnvoyeeFailed');

    }
}
