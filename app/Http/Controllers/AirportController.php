<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AirportController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/resources/airports",
     *   tags={"Resources"},
     *   summary="Airports list",
     *   description="System Airports",
     *   operationId="airports",
     *   @OA\Parameter(
     *     name="city_id",
     *     required=false,
     *     in="query",
     *     description="Get specific city aiports",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="country_id",
     *     required=false,
     *     in="query",
     *     description="Get specific country aiports",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *     @OA\Parameter(
     *     name="name",
     *     required=false,
     *     in="query",
     *     description="city name",
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
     * @return JsonResponse
     * @throws \Exception
     */
    public function index(Request $request)
    {
        $airports = Airport::with("city")->where("flightable",true);

        if (!empty($request->city_id))
            return $this->liteResponse(config("code.request.SUCCESS"), $airports->where("city_id", $request->city_id)->orderBy("name")->paginate(50));
        if (!empty($request->country_id))
            return $this->liteResponse(config("code.request.SUCCESS"), $airports->whereHas("city", function ($query) use($request){
                $query->where("country_id",$request->country_id);
            })->orderBy("name")->paginate(50));
        if (!empty($request->name))
            return $this->liteResponse(config("code.request.SUCCESS"), $airports->search($request->name,null,true)->orderBy("name")->paginate(50));

        return $this->liteResponse(config("code.request.SUCCESS"), $airports->search($request->name,null,true)->paginate(50));
    }
}
