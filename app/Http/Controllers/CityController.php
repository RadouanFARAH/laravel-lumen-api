<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/resources/cities",
     *   tags={"Resources"},
     *   summary="Cities list",
     *   description="System Cities",
     *   operationId="cities",
     *     @OA\Parameter(
     *     name="country_id",
     *     required=false,
     *     in="query",
     *     description="Get specific country cities",
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
        $cities = City::with(["airports","country"]);

        if (!empty($request->country_id))
            $cities->where("country_id",$request->country_id);
        if (!empty($request->name))
            $cities->where("name","like","$request->name%");

            return $this->liteResponse(config("code.request.SUCCESS"),$cities->orderBy("name")->get());
    }
}
