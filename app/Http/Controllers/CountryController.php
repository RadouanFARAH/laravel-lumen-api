<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/resources/countries",
     *   tags={"Resources"},
     *   summary="Countries list",
     *   description="System countries",
     *   operationId="countries",
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
    public function index()
    {
        return $this->liteResponse(config("code.request.SUCCESS"),Country::orderBy("name")->get());
    }
}
