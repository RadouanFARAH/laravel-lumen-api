<?php

namespace App\Http\Controllers;

use App\Models\TravelerRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TravelerRatingController extends Controller
{
    /**
     * @OA\post(
     *     path="/api/user/review/rating",
     *   tags={"Review"},
     *   summary="User rating",
     *   description="User rating",
     *   operationId="Rating",
     *      @OA\Parameter(
     *     name="user_id",
     *     required=false,
     *     in="query",
     *     description="The user you want to get the rating if not specified connected user is set as default",
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
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function stars(Request $request)
    {
        $stars = TravelerRating::where("traveler_id", empty($request->user_id) ? auth()->id() : $request->user_id);
        return $this->liteResponse(config("code.request.SUCCESS"), [
            "average" => ($stars->avg("star") == null ? "0.0" : number_format((float)$stars->avg("star") , 1, '.', '')) . "/5",
            "count" => $stars->count(),
            "stars" => [
                1 => TravelerRating::where("traveler_id", empty($request->user_id) ? auth()->id() : $request->user_id)->where("star", 1)->count(),
                2 => TravelerRating::where("traveler_id", empty($request->user_id) ? auth()->id() : $request->user_id)->where("star", 2)->count(),
                3 => TravelerRating::where("traveler_id", empty($request->user_id) ? auth()->id() : $request->user_id)->where("star", 3)->count(),
                4 => TravelerRating::where("traveler_id", empty($request->user_id) ? auth()->id() : $request->user_id)->where("star", 4)->count(),
                5 => TravelerRating::where("traveler_id", empty($request->user_id) ? auth()->id() : $request->user_id)->where("star", 5)->count(),
            ],
        ]);
    }

    public function create(array $data)
    {
        return TravelerRating::updateOrCreate(["author_id" => $data["author_id"], "traveler_id" => $data["traveler_id"]], ["star"=>$data["star"]]);
    }

    protected function validator(&$data)
    {
        return Validator::make($data, [
            "star" => [Rule::in(TravelerRating::getValues())],
            "author_id" => ['required', "exists:users,id"],
            "traveler_id" => ['required', "exists:users,id"],
        ]);
    }

    /**
     * @OA\post(
     *     path="/api/user/review/add/rating",
     *   tags={"Review"},
     *   summary="send User rating",
     *   description="send User rating",
     *   operationId="SendComment",
     *     @OA\Parameter(
     *     name="star",
     *     required=true,
     *     in="query",
     *     description="The star in 1,2,3,4,5",
     *     @OA\Schema(
     *         type="integer"
     *     )
     *   ),   @OA\Parameter(
     *     name="traveler_id",
     *     required=true,
     *     in="query",
     *     description="The id of the traveler",
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
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        $data = $request->all((new TravelerRating())->getFillable());
        $data['author_id'] = auth()->id();
        if ($data["author_id"] == $data["traveler_id"]) {
            return $this->liteResponse(config("code.request.NOT_AUTHORIZED"), null, "You can afford this action");
        }
        return $this->save($data);
    }

}
