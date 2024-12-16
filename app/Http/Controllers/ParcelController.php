<?php

namespace App\Http\Controllers;

use App\Models\Parcel;
use App\Models\Recipient;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\PaymentMethode;
use App\Http\ResponseParser\DefResponse;
use Illuminate\Support\Facades\Validator;

class ParcelController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/user/parcel/mine",
     *   tags={"Parcels"},
     *   summary="My parcels",
     *   description="List user parcels",
     *   operationId="myParcels",
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
    public function myParcels()
    {
        return $this->liteResponse(config("code.request.SUCCESS"), Parcel::with(['arrivalCity', 'departureCity', 'recipient', "owner", "luggageRequests.trip"])->where("sender_id", auth()->id())->orderByDesc("created_at")->paginate(20));
    }

    public function create(array $data)
    {
        return Parcel::create($data);
    }

    /**
     * @OA\Post(
     *     path="/api/user/parcel/add",
     *   tags={"Parcels"},
     *   summary="Add a new parcel",
     *   description="Publish a new parcel in list",
     *   operationId="addparcel",
     *     @OA\Parameter(
     *     name="parcel_restriction",
     *     required=true,
     *     in="query",
     *     description="Parcel restriction in SAME_FLY or ANY",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),@OA\Parameter(
     *     name="allow_split",
     *     required=true,
     *     in="query",
     *     description="Accepter multiple request for this parcel",
     *     @OA\Schema(
     *         type="boolean"
     *     )
     *   ),
     *    @OA\Parameter(
     *     name="fly_number",
     *     required=false,
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
     *     name="weight",
     *     required=true,
     *     in="query",
     *     description="Parcel space available",
     *     @OA\Schema(
     *         type="integer"
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
     *       @OA\RequestBody(
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
     *     @OA\Schema(type="string")
     *   ),
     * )
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function add(Request $request)
    {

        $data = $request->all((new Parcel())->getFillable());
        $data["sender_id"] = auth()->id();


        $recipient = Recipient::where("owner_id", auth()->id())->where("id", $request->recipient_id)->first();
        if (empty($recipient)) {
            $recipientResponse = new DefResponse((new RecipientController())->store($request));
            if ($recipientResponse->isSuccess()) {
                $recipient = $recipientResponse->getData();
            } else {
                return $recipientResponse->getResponse();
            }
        }
        $data['recipient_id'] = $recipient['id'];
        $this->saveParcelsImages($data);
        //TODO check fly number in case specified
        $isFlightInfoValidated = true; // valid par defaut
        if (!$isFlightInfoValidated) {
            $this->sendVerificationFailedNotifications($parcel);
        }
        // if (empty($data['fly_number'])) {
        //     $data['fly_number'] = 'AT 780';
        // }

        return $this->save(array_filter($data));
    }

    /**
     * @OA\Post(
     *     path="/api/user/parcel/edit",
     *   tags={"Parcels"},
     *   summary="Edit a  parcel",
     *   description="Publish a new parcel in list",
     *   operationId="editParcel",
     *     @OA\Parameter(
     *     name="parcel_id",
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
     *   ),
     *    @OA\Parameter(
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
     *     name="weight",
     *     required=false,
     *     in="query",
     *     description="Parcel space available",
     *     @OA\Schema(
     *         type="integer"
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
     *       @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                    description="Prescription  images that have been deleted as an update exemple [1,2,3] use
     *                    index attribute", property="deleted_images", type="array",
     *                    @OA\Items(type="integer",minimum=0)
     *                 ),
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
     *     @OA\Schema(type="string")
     *   ),
     * )
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function edit(Request $request)
    {

        $parcel = Parcel::where("id", $request->parcel_id)->where("sender_id", auth()->id())->first();
        if (empty($parcel)) {
            return $this->liteResponse(config("code.request.NOT_AUTHORIZED"));
        }

        $data = $request->all((new Parcel())->getFillable());
        $recipient = Recipient::where("owner_id", auth()->id())->where("id", $request->recipient_id)->first();
        if (empty($recipient)) {
            $recipientResponse = new DefResponse((new RecipientController())->store($request));
            if ($recipientResponse->isSuccess()) {
                $recipient = $recipientResponse->getData();
            } else {
                return $recipientResponse->getResponse();
            }
        }
        $data['recipient_id'] = $recipient['id'];
        $this->saveParcelsImages($data);

        //Remove deleted images
        $deletedImagesIndex = $request->get("deleted_images");
        $currentImages = $parcel->images;

        if ($deletedImagesIndex != null) {
            $deletedImagesIndex = collect($deletedImagesIndex)->sortByDesc(function ($last) {
                return $last;
            })->toArray();

            foreach ($deletedImagesIndex as $value) {
                if (Arr::has($currentImages, $value)) {
                    unset($currentImages[$value]);
                }
            }
        }

        //Merge all images
        $newImageArray = array_merge(json_decode($data['images'], true), $currentImages);
        $data['images'] = json_encode($newImageArray);
        $this->sendVerificationSuccessNotifications($parcel);
        return $this->update(array_filter($data), $parcel);
    }

    public function update($data, $parcel)
    {
        if (empty($data))
            return $this->liteResponse(config('code.request.VALIDATION_ERROR'), $data, "Nothing to update");

        //TODO check fly number in case specified
        $isFlightInfoValidated = true; // valid par defaut
        if (!$isFlightInfoValidated) {
            $this->sendVerificationFailedNotifications($parcel);
        }
        $validator = Validator::make(
            $data,
            [
                "fly_number" => "nullable",
                "departure_city_id" => ["nullable", "exists:cities,id"],
                "departure_date" => "nullable|date|after:now",
                "arrival_city_id" => ["nullable", "exists:cities,id"],
                "weight" => "nullable|numeric|min:1",
                "sender_id" => ["nullable", "exists:users,id"],
                "canceled" => "nullable|boolean",
                "cancellation_reason" => "nullable|string",
            ]
        );

        if ($validator->fails())
            return $this->liteResponse(config('code.request.VALIDATION_ERROR'), $validator->errors());
        try {

            $parcel->update($data);
            return $this->liteResponse(config('code.request.SUCCESS'), $parcel->fresh(['arrivalCity', 'departureCity']));
        } catch (\Exception $exception) {
            return $this->liteResponse(config('code.request.FAILURE'), $exception->getMessage());
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
            $otherFilesData = array();
            foreach ($allFiles['images'] ?? array() as $key => $image) {
                array_push($otherFilesData, $this->saveMedia($image, self::PARCEL_DIRECTORY));
            }
            $data['images'] = json_encode($otherFilesData);
        } else {
            $data['images'] = json_encode([]);
        }
    }



    public function saved($response)
    {
        // Retrieve the saved Parcel from the response
        $parcel = (object) $response->getData();

        // Check if the trip requires notifications
        if ($parcel->parcel_restriction === 'SAME_FLY') {
            $this->sendVerificationSuccessNotifications($parcel);
        }
    }

    private function sendVerificationSuccessNotifications($parcel)
    {
        $user = auth()->user();

        // Email Notification
        $source = City::find($parcel->departure_city_id);
        $destination = City::find($parcel->arrival_city_id);
        $mail_data = [
            "name" => $user->first_name,
            "source" => $source->name,
            "destination" => $destination->name,
            "email" => $user->email
        ];
        dispatch(new MailJobs("App\Mail\DemandeEnLigne", $mail_data));

        // SMS Notification
        $smsMessage = trans('notifications.demande_enligne.sms', ['link' => 'https://wxgvdstv:l!Htfvgrrrrrrr']);
        $user->notify(new SmsNotification($smsMessage));


        // Push Notification
        $push_data = config('push_notifications/DemandeEnLigne');
        FcmController::notify($user, $push_data, 'DemandeEnLigne');
    }

    private function sendVerificationFailedNotifications($parcel)
    {
        $user = auth()->user();

        // Email Notification
        $mail_data = [
            "name" => $user->first_name,
            "email" => $user->email
        ];
        dispatch(new MailJobs("App\Mail\DemandeEnLigneFailed", $mail_data));

        // SMS Notification
        $smsMessage = trans('notifications.demande_enligne_failed.sms', ['link' => 'https://example.com']);
        $user->notify(new SmsNotification($smsMessage));


        // Push Notification
        $push_data = config('push_notifications/DemandeEnLigneFailed');
        FcmController::notify($user, $push_data, 'DemandeEnLigneFailed');
    }

    /**
     * @OA\Post(
     *     path="/api/user/parcel/delete",
     *   tags={"Parcels"},
     *   summary="Delete parcel",
     *   description="Remove parcel",
     *   operationId="deleteParcel",
     *     @OA\Parameter(
     *     name="parcel_id",
     *     required=true,
     *     in="query",
     *     description="The id of your parcel to delete",
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
        $parcel = Parcel::where("sender_id", auth()->id())->where("id", $request->parcel_id)->first();
        if (empty($parcel)) {
            return $this->liteResponse(config('code.request.NOT_FOUND'));
        }
        //TODO check parcel booking
        return $this->destroy($parcel);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Parcel $parcel
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy(Parcel $parcel)
    {
        try {
            return $this->liteResponse(config('code.request.SUCCESS'), $parcel->delete());
        } catch (\Exception $exception) {
            return $this->liteResponse(config('code.request.FAILURE'), $exception->getMessage());
        }
    }

    protected function validator(&$data)
    {
        return Validator::make($data, [
            "fly_number" => "nullable",
            "departure_city_id" => ["required", "exists:cities,id"],
            "departure_date" => "required|date",
            "arrival_city_id" => ["required", "exists:cities,id"],
            "weight" => "required|numeric|min:1",
            "sender_id" => ["required", "exists:users,id"],
            "recipient_id" => ["required", "exists:recipients,id"],
            "weight_unit_price" => "numeric|min:0",
        ]);
    }
}
