<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Trip;
use App\Models\Parcel;
use Illuminate\Http\Request;
use App\Models\SearchHistory;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\ResponseParser\DefResponse;
use Illuminate\Support\Facades\Validator;

class SearchHistoryController extends Controller
{
    /**
     * @OA\post(
     *     path="/api/user/search",
     *   tags={"Research"},
     *   summary="Search parcels or trips",
     *   description="User looking for parcel or trips",
     *   operationId="search",
     *     @OA\Parameter(
     *     name="context",
     *     required=true,
     *     in="query",
     *     description="The type of search must be TRIP or PARCEL",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *     @OA\Parameter(
     *     name="from",
     *     required=false,
     *     in="query",
     *     description="departure city id",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *     @OA\Parameter(
     *     name="to",
     *     required=false,
     *     in="query",
     *     description="arrival city id",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *     @OA\Parameter(
     *     name="date",
     *     required=false,
     *     in="query",
     *     description="departure date",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *     @OA\Parameter(
     *     name="minWeight",
     *     required=false,
     *     in="query",
     *     description="min weight",
     *     @OA\Schema(
     *         type="integer"
     *     )
     *   ),
     *     @OA\Parameter(
     *     name="maxWeight",
     *     required=false,
     *     in="query",
     *     description="max weight",
     *     @OA\Schema(
     *         type="integer"
     *     )
     *   ),
     *     @OA\Parameter(
     *     name="maxPrice",
     *     required=false,
     *     in="query",
     *     description="max price",
     *     @OA\Schema(
     *         type="integer"
     *     )
     *   ),
     *     @OA\Parameter(
     *     name="minPrice",
     *     required=false,
     *     in="query",
     *     description="min price",
     *     @OA\Schema(
     *         type="integer"
     *     )
     *   ),
     *     @OA\Parameter(
     *     name="isVerify",
     *     required=false,
     *     in="query",
     *     description="active user",
     *     @OA\Schema(
     *         type="boolean"
     *     )
     *   ),
     *     @OA\Parameter(
     *     name="fly_number",
     *     required=false,
     *     in="query",
     *     description="search specific fly",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *     @OA\Parameter(
     *     name="parcel_restriction",
     *     required=true,
     *     in="query",
     *     description="specified parcel fly restriction SAME_FLY or ANY",
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
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function search(Request $request)
    {

        $context = $request->context;
        if (!in_array($context, SearchHistory::getContext())) {
            return $this->liteResponse(config('code.request.NOT_FOUND'), null, "Sorry but context should be in " . join(",", SearchHistory::getContext()));
        }
        if (!in_array($request->parcel_restriction, Trip::getParcelRestriction())) {
            return $this->liteResponse(config('code.request.NOT_FOUND'), null, "Sorry but restriction should be in " . join(",", Trip::getParcelRestriction()));
        }

        $result = null;

        if ($context == SearchHistory::PARCEL) {
            $result = Parcel::available()->with(["arrivalCity", "departureCity", "owner", "luggageRequests"])->where('sender_id',"<>", auth()->id());
            if ($request->parcel_restriction == Trip::PARCEL_SAME_FLY) {
                $result->where("parcel_restriction", $request->parcel_restriction);
            }
        } else {
            $result = Trip::available()->with(["arrivalCity", "departureCity", "arrivalAirport", "departureAirport", "traveler", "luggageRequests"])->where('traveler_id',"<>", auth()->id());
            if ($request->parcel_restriction == Trip::PARCEL_SAME_FLY) {
                $result->where("parcel_restriction", $request->parcel_restriction);
            }
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

        if ($request->fromAirport != null) {
            $result->where("departure_airport_id", $request->fromAirport);
        }

        if ($request->toAirport != null) {
            $result->where("arrival_airport_id", $request->toAirport);
        }

        if ($request->date != null) {
            $result->whereDate("departure_date",'>=', $request->date);
        }

        if ($request->minWeight != null) {
            $result->where(DB::raw($context == SearchHistory::PARCEL ? 'weight - booked_weight' : 'available_weight - booked_weight'), ">=", $request->minWeight);
        }

        if ($request->maxWeight != null) {
            $result->where(DB::raw($context == SearchHistory::PARCEL ? 'weight - booked_weight' : 'available_weight - booked_weight'), "<=", $request->maxWeight);
        }

        if ($request->minPrice != null) {
            $result->where("weight_unit_price", ">=", $request->minPrice);
        }

        if ($request->maxPrice != null) {
            $result->where("weight_unit_price", '<=', $request->maxPrice);
        }

        if ($request->isVerify != null) {
            if ($request->isVerify) {
                $result->whereHas($context == SearchHistory::PARCEL ? "sender_id" : "traveler_id", function ($q) {
                    $q->whereHas('verifications', function ($q) {
                        $q->where("verified_at", "!=", null);
                    });
                });
            }

        }

        //remove old one
        if($context == SearchHistory::PARCEL){
            $result->whereDate("departure_date", "<=", Carbon::now())
            ->whereDate("arrival_date", ">=", Carbon::now());
        }else{
            $result->whereDate("departure_date", ">=", Carbon::now());
        }

        $result->orderBy('departure_date', 'asc');
        //save to history

        $rest = $result->paginate(20);

        if ($request->from != null || $request->to != null) {
          $hytory =  new DefResponse($this->add($request));
          $rest[0]['history_id']= $hytory->getData()['id'];
        }

        return $this->liteResponse(config('code.request.SUCCESS'), $rest );
    }

    public function add(Request $request)
    {
        $data = $request->all((new SearchHistory())->getFillable());
        $data['user_id'] = auth()->id();
        return $this->save($data);
    }

    public function create(array $data)
    {
        return SearchHistory::create($data);
    }

    /**
     * @OA\Post(
     *     path="/api/user/search/history",
     *   tags={"Research"},
     *   summary="My research history",
     *   description="List user search history",
     *   operationId="mySearchHistory",
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
    public function history()
    {
        return $this->liteResponse(config("code.request.SUCCESS"), SearchHistory::with(['departure', 'destination'])->where("user_id", auth()->id())->orderBy('created_at', 'desc')->paginate(30));
    }

    /**
     * @OA\Post(
     *     path="/api/user/search/save",
     *   tags={"Research"},
     *   summary="Save and delete in research history",
     *   description="Save and delete in search history",
     *   operationId="saveSearchHistory",
     *     @OA\Parameter(
     *     name="history_id",
     *     required=true,
     *     in="query",
     *     description="The id of the history to save",
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
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function saveHistory(Request $request)
    {
        $history = SearchHistory::where("id", $request->history_id)->where("user_id", auth()->id())->first();
        if (empty($history)) {
            return $this->liteResponse(config('code.request.NOT_FOUND'), null, "Sorry but we can't save it for you");
        }
        try {
            if ($request->is_alert) {
                $history->saved = !$history->saved;
            } else {
                $history->alert_me = !$history->alert_me;
            }

            $history->save();
            return $this->liteResponse(config('code.request.SUCCESS'), $history->refresh());
        } catch (\Exception $e) {
            return $this->liteResponse(config('code.request.FAILURE'), $e->getMessage());
        }
    }

    public function saveAlert(Request $request)
    {
        $history = SearchHistory::where("id", $request->history_id)->where("user_id", auth()->id())->first();
        if (empty($history)) {
            return $this->liteResponse(config('code.request.NOT_FOUND'), null, "Sorry but we can't save it for you");
        }
        try {
            $history->alert_me = !$history->alert_me;
            $history->save();
            return $this->liteResponse(config('code.request.SUCCESS'), $history->refresh());
        } catch (\Exception $e) {
            return $this->liteResponse(config('code.request.FAILURE'), $e->getMessage());
        }
    }

    public function myAlerts()
    {
        $histories = SearchHistory::where("alert_me", true);
        if (empty($histories)) {
            return $this->liteResponse(config('code.request.NOT_FOUND'), null, "Sorry but we can't save it for you");
        }
        return $this->liteResponse(config('code.request.SUCCESS'), $histories);
    }

    protected function validator(&$data)
    {
        return Validator::make($data, [
            "context" => ["required", Rule::in(SearchHistory::getContext())],
            "parcel_restriction" => ["required", Rule::in(Trip::getParcelRestriction())],
        ]);
    }
}
