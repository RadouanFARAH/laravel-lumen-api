<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\City;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Jobs\MailJobs;
use App\Notifications\SmsNotification;
use Illuminate\Support\Facades\Log;

class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $trips = Trip::with([]);

        if ($request->has("owner_id")) {
        }
    }

    /**
     * @OA\Post(
     *     path="/api/user/trip/mine",
     *   tags={"Trips"},
     *   summary="My trips",
     *   description="List user trips",
     *   operationId="myTrips",
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
    public function myTrips(Request $request)
    {
        return $this->liteResponse(config("code.request.SUCCESS"), Trip::with(['arrivalAirport', 'arrivalCity', 'departureAirport', 'departureCity', "traveler", "luggageRequests.parcel"])->where("traveler_id", auth()->id())->orderByDesc("created_at")->paginate(20));
    }

    /**
     * @OA\Post(
     *     path="/api/user/trip/popular",
     *   tags={"Trips"},
     *   summary="Popular destination",
     *   description="List user popular des",
     *   operationId="myPopularTrips",
     *     @OA\Parameter(
     *     name="from",
     *     required=false,
     *     in="query",
     *     description="DEPARTURE or ARRIVAL",
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
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function popular(Request $request)
    {
        return $this->liteResponse(config("code.request.SUCCESS"), Trip::with(['arrivalAirport', 'arrivalCity', 'departureAirport', 'departureCity'])
            ->where("departure_date", ">=", Carbon::now())
            ->groupBy($request->from == "ARRIVAL" ? "arrival_city_id" : "departure_city_id")
            ->orderByDesc("created_at")->paginate(20));
    }

    public function create(array $data)
    {
        return Trip::create($data);
    }

    /**
     * @OA\Post(
     *     path="/api/user/trip/add",
     *   tags={"Trips"},
     *   summary="Add new trips",
     *   description="Publish a new trip in list",
     *   operationId="addTrip",
     *     @OA\Parameter(
     *     name="parcel_restriction",
     *     required=true,
     *     in="query",
     *     description="Parcel restriction in SAME_FLY or ANY",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="fly_number",
     *     required=true,
     *     in="query",
     *     description="The fly number this value should be verified",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="departure_city_id",
     *     required=true,
     *     in="query",
     *     description="Departure city id",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="departure_airport_id",
     *     required=true,
     *     in="query",
     *     description="Departure airport id",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="departure_date",
     *     required=true,
     *     in="query",
     *     description="Departure date",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="arrival_city_id",
     *     required=true,
     *     in="query",
     *     description="Arrival city id",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="arrival_date",
     *     required=true,
     *     in="query",
     *     description="Arrival date",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="arrival_airport_id",
     *     required=true,
     *     in="query",
     *     description="Arrival airport id",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="available_weight",
     *     required=true,
     *     in="query",
     *     description="Parcel space available",
     *     @OA\Schema(
     *         type="integer"
     *     )
     *   ),@OA\Parameter(
     *     name="weight_unit_price",
     *     required=true,
     *     in="query",
     *     description="Unit price",
     *     @OA\Schema(
     *         type="integer"
     *     )
     *   ),@OA\Parameter(
     *     name="auto_accept_booking",
     *     required=true,
     *     in="query",
     *     description="Auto accept user booking",
     *     @OA\Schema(
     *         type="boolean"
     *     )
     *   ),@OA\Parameter(
     *     name="allow_split_luggage",
     *     required=true,
     *     in="query",
     *     description="Accepter multiple request for this travel",
     *     @OA\Schema(
     *         type="boolean"
     *     )
     *   ),@OA\Parameter(
     *     name="info",
     *     required=false,
     *     in="query",
     *     description="additionnal information",
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
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function add(Request $request)
    {
        $data = $request->all((new Trip())->getFillable());
        $data["traveler_id"] = auth()->id();
        //TODO check check fly number in case of existence
        $isFlightInfoValidated = true; // valid par defaut
        if (!$isFlightInfoValidated) {
            $this->sendVerificationFailedNotifications($trip);
        }
        return $this->save(array_filter($data));
    }

    public function saved($response)
    {
        // Retrieve the saved trip from the response
        $trip = (object) $response->getData();

        // Check if the trip requires notifications
        if ($trip->parcel_restriction === 'SAME_FLY') {
            $this->sendVerificationSuccessNotifications($trip);
        }
    }



    private function sendVerificationSuccessNotifications($trip)
    {
        $user = auth()->user();

        // Email Notification
        Log::info(' email ');

        $source = City::find($trip->departure_city_id);
        $destination = City::find($trip->arrival_city_id);
        $mail_data = [
            "name" => $user->first_name,
            "date" => $trip->departure_date,
            "source" => $source->name,
            "destination" => $destination->name,
            "email" => $user->email
        ];
        dispatch(new MailJobs("App\Mail\TrajetEnLigne", $mail_data));

        // SMS Notification
        Log::info(' sms ');

        $smsMessage = trans('notifications.trajet_enligne.sms', ['link' => 'https://example.com']);
        $user->notify(new SmsNotification($smsMessage));


        // Push Notification
        Log::info(' push ');

        $push_data = config('push_notifications/TrajetEnLigne');
        FcmController::notify($user, $push_data, 'TrajetEnLigne');
    }

    private function sendVerificationFailedNotifications($trip)
    {
        $user = auth()->user();

        // Email Notification
        $source = City::find($data->departure_city_id);
        $destination = City::find($data->arrival_city_id);
        $mail_data = [
            "name" => $user->first_name,
            "source" => $source->name,
            "destination" => $destination->name,
            "email" => $user->email
        ];
        dispatch(new MailJobs("App\Mail\TrajetEnLigneFailed", $mail_data));

        // SMS Notification
        $smsMessage = trans('notifications.trajet_enligne_failed.sms', ['link' => 'https://example.com']);
        $user->notify(new SmsNotification($smsMessage));


        // Push Notification
        $push_data = config('push_notifications/TrajetEnLigneFailed');
        FcmController::notify($user, $push_data, 'TrajetEnLigneFailed');
    }

    /**
     * @OA\Post(
     *     path="/api/user/trip/edit",
     *   tags={"Trips"},
     *   summary="Edit new trips",
     *   description="Edit a trip",
     *   operationId="editTrip",
     *     @OA\Parameter(
     *     name="trip_id",
     *     required=true,
     *     in="query",
     *     description="The id of your trip nedd that need update",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *     @OA\Parameter(
     *     name="parcel_restriction",
     *     required=false,
     *     in="query",
     *     description="Parcel restriction in SAME_FLY or ANY",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="fly_number",
     *     required=false,
     *     in="query",
     *     description="The fly number this value should be verified",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="departure_city_id",
     *     required=false,
     *     in="query",
     *     description="Departure city id",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="departure_airport_id",
     *     required=false,
     *     in="query",
     *     description="Departure airport id",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="departure_date",
     *     required=false,
     *     in="query",
     *     description="Departure date",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="arrival_city_id",
     *     required=false,
     *     in="query",
     *     description="Arrival city id",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="arrival_date",
     *     required=false,
     *     in="query",
     *     description="Arrival date",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="arrival_airport_id",
     *     required=false,
     *     in="query",
     *     description="Arrival airport id",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="available_weight",
     *     required=false,
     *     in="query",
     *     description="Parcel space available",
     *     @OA\Schema(
     *         type="integer"
     *     )
     *   ),@OA\Parameter(
     *     name="weight_unit_price",
     *     required=false,
     *     in="query",
     *     description="Unit price",
     *     @OA\Schema(
     *         type="double"
     *     )
     *   ),@OA\Parameter(
     *     name="info",
     *     required=false,
     *     in="query",
     *     description="additionnal information",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="auto_accept_booking",
     *     required=false,
     *     in="query",
     *     description="Auto accept user booking",
     *     @OA\Schema(
     *         type="boolean"
     *     )
     *   ),@OA\Parameter(
     *     name="allow_split_luggage",
     *     required=false,
     *     in="query",
     *     description="Accepter multiple request for this travel",
     *     @OA\Schema(
     *         type="boolean"
     *     )
     *   ),@OA\Parameter(
     *     name="canceled",
     *     required=false,
     *     in="query",
     *     description="Cancel trip",
     *     @OA\Schema(
     *         type="boolean"
     *     )
     *   ),@OA\Parameter(
     *     name="cancellation_reason",
     *     required=false,
     *     in="query",
     *     description="Why did you cancel this trip",
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
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function edit(Request $request)
    {

        $data = $request->all((new Trip())->getFillable());
        $trip = Trip::where("traveler_id", auth()->id())->where("id", $request->trip_id)->first();
        if (empty($trip)) {
            return $this->liteResponse(config('code.request.NOT_FOUND'), $data);
        }

        return $this->update(array_filter($data), $trip);
    }

    public function update(array $data, $trip)
    {

        if (empty($data))
            return $this->liteResponse(config('code.request.VALIDATION_ERROR'), $data, "Nothing to update");

        //TODO check check fly number in case of existence
        $isFlightInfoValidated = true; // valid par defaut
        if (!$isFlightInfoValidated) {
            $this->sendVerificationFailedNotifications($trip);
        }
        //TODO check spare booking count before updating available space
        //TODO send notification to validated requester and destroy request

        $validator = Validator::make(
            $data,
            [
                "parcel_restriction" => ["nullable", Rule::in(Trip::getParcelRestriction())],
                "fly_number" => "nullable",
                "departure_city_id" => ["nullable", "exists:cities,id"],
                "departure_airport_id" => ["nullable", "exists:airports,id"],
                "departure_date" => "nullable|date",
                "arrival_city_id" => ["nullable", "exists:cities,id"],
                "arrival_airport_id" => ["nullable", "exists:airports,id"],
                "arrival_date" => "nullable|date",
                "available_weight" => "nullable|numeric|min:1",
                "weight_unit_price" => "nullable|numeric|min:0",
                "canceled" => "nullable|boolean",
                "cancellation_reason" => "nullable|string",
                "traveler_id" => ["nullable", "exists:users,id"]
            ]
        );

        if ($validator->fails()) return $this->liteResponse(config('code.request.VALIDATION_ERROR'), $validator->errors());
        
        try {
            $trip->update($data);
            Log::info('sending notif');
            $this->sendVerificationSuccessNotifications($trip);
            return $this->liteResponse(config('code.request.SUCCESS'), $trip->fresh(['arrivalAirport', 'arrivalCity', 'departureAirport', 'departureCity']));
        } catch (\Exception $exception) {
            Log::info($exception->getMessage());
            return $this->liteResponse(config('code.request.FAILURE'), $exception->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/user/trip/delete",
     *   tags={"Trips"},
     *   summary="Delete",
     *   description="Remove trip in list",
     *   operationId="deleteTrip",
     *     @OA\Parameter(
     *     name="trip_id",
     *     required=true,
     *     in="query",
     *     description="The id of your trip to delete",
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
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $trip = Trip::where("traveler_id", auth()->id())->where("id", $request->trip_id)->first();
        if (empty($trip)) {
            return $this->liteResponse(config('code.request.NOT_FOUND'));
        }
        //TODO Check trip booking
        return $this->destroy($trip);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Trip $trip
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy(Trip $trip)
    {
        try {
            return $this->liteResponse(config('code.request.SUCCESS'), $trip->delete());
        } catch (\Exception $exception) {
            return $this->liteResponse(config('code.request.FAILURE'), $exception->getMessage());
        }
    }

    protected function validator(&$data)
    {
        return Validator::make($data, [
            "parcel_restriction" => ["required", Rule::in(Trip::getParcelRestriction())],
            "fly_number" => "nullable",
            "departure_city_id" => ["required", "exists:cities,id"],
            "departure_airport_id" => ["required", "exists:airports,id"],
            "departure_date" => "required|date",
            "arrival_city_id" => ["required", "exists:cities,id"],
            "arrival_airport_id" => ["required", "exists:airports,id"],
            "arrival_date" => "required|date",
            "available_weight" => "required|numeric|min:1",
            "weight_unit_price" => "required|numeric|min:0",
            "traveler_id" => ["required", "exists:users,id"],
        ]);
    }
}
