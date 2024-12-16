<?php

namespace App\Http\Controllers;

use App\Models\LuggageRequest;
use App\Models\Parcel;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/dashboard/user/list",
     *   tags={"Dashboard"},
     *   summary="Users",
     *   description="List user",
     *   operationId="users",
     *     @OA\Parameter(
     *     name="user_id",
     *     required=false,
     *     in="query",
     *     description="If need specific user",
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
    public function users(Request $request)
    {
        $users = User::with([]);
        if (!empty($request->user_id)) {
            $users->where("id", $request->user_id);
        }
        return $this->liteResponse(config("code.request.SUCCESS"), $users->paginate(100));
    }


    /**
     * @OA\Post(
     *     path="/api/dashboard/trip/list",
     *   tags={"Dashboard"},
     *   summary="Trips",
     *   description="List trip",
     *   operationId="trips",
     *     @OA\Parameter(
     *     name="user_id",
     *     required=false,
     *     in="query",
     *     description="If need specific user",
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
    public function trips(Request $request)
    {
        $trips = Trip::with([ 'arrivalCity', 'departureCity','departureAirport','traveler','arrivalAirport','luggageRequests']);

        if (!empty($request->id)) {
            $trips->where("id", $request->id);
        }

        if (!empty($request->user_id)) {
            $trips->where("traveler_id", $request->user_id);
        }

        return $this->liteResponse(config("code.request.SUCCESS"), $trips->paginate(100));
    }

    /**
     * @OA\Post(
     *     path="/api/dashboard/parcel/list",
     *   tags={"Dashboard"},
     *   summary="parcels",
     *   description="List parcels",
     *   operationId="parcels",
     *     @OA\Parameter(
     *     name="user_id",
     *     required=false,
     *     in="query",
     *     description="If need specific user",
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
    public function parcels(Request $request)
    {
        $parcels = Parcel::with([ 'arrivalCity', 'departureCity','recipient','owner','luggageRequests']);
        if (!empty($request->id)) {
            $parcels->where("id", $request->id);
        }

        if (!empty($request->user_id)) {
            $parcels->where("owner_id", $request->user_id);
        }
        return $this->liteResponse(config("code.request.SUCCESS"), $parcels->paginate(100));
    }

    /**
     * @OA\Post(
     *     path="/api/dashboard/booking/list",
     *   tags={"Dashboard"},
     *   summary="Booking",
     *   description="List bookingd",
     *   operationId="Bookings",
     *     @OA\Parameter(
     *     name="user_id",
     *     required=false,
     *     in="query",
     *     description="If need specific user",
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
    public function luggage(Request $request)
    {
        $users = LuggageRequest::with([ 'trip', 'parcel']);
        if (!empty($request->user_id)) {
            $users->whereHas("");
        }
        return $this->liteResponse(config("code.request.SUCCESS"), $users->paginate(100));
    }
}
