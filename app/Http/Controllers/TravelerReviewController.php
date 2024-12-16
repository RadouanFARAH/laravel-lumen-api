<?php

namespace App\Http\Controllers;

use App\Models\TravelerReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TravelerReviewController extends Controller
{
    /**
     * @OA\post(
     *     path="/api/user/review",
     *   tags={"Review"},
     *   summary="User comment",
     *   description="User comment",
     *   operationId="myReview",
     *      @OA\Parameter(
     *     name="user_id",
     *     required=false,
     *     in="query",
     *     description="The user you want to get the comment if not specified connected user is set as default",
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
    public function myReviews(Request $request)
    {
        return $this->liteResponse(config("code.request.SUCCESS"), TravelerReview::with(["author"])
            ->where("traveler_id", empty($request->user_id)? auth()->id():$request->user_id)
            ->paginate(30));
    }

    /**
     * @OA\post(
     *     path="/api/user/review/sent",
     *   tags={"Review"},
     *   summary="User comment",
     *   description="User comment",
     *   operationId="mySentReview",
     *      @OA\Parameter(
     *     name="user_id",
     *     required=false,
     *     in="query",
     *     description="The user you want to get the comment if not specified connected user is set as default",
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
    public function mySentReviews(Request $request)
    {
        return $this->liteResponse(config("code.request.SUCCESS"), TravelerReview::with(["author","traveler"])
            ->where("author_id",  auth()->id())
            ->paginate(30));
    }

    public function create(array $data)
    {
        return TravelerReview::create($data);
    }

    /**
     * @OA\post(
     *     path="/api/user/review/add/comment",
     *   tags={"Review"},
     *   summary="send User rating",
     *   description="send User rating",
     *   operationId="SendRating",
     *     @OA\Parameter(
     *     name="comment",
     *     required=true,
     *     in="query",
     *     description="The comment",
     *     @OA\Schema(
     *         type="string"
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
     *      @OA\Parameter(
     *     name="trip_id",
     *     required=true,
     *     in="query",
     *     description="The id of the trip",
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
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        $data = $request->all((new TravelerReview())->getFillable());
        $data['author_id'] = auth()->id();
        if ($data["author_id"] == $data["traveler_id"]) {
            return $this->liteResponse(config("code.request.NOT_AUTHORIZED"), null, "You can't afford this action");
        }
        return $this->save($data);
    }

    /**
     * @OA\post(
     *     path="/api/user/review/add/rating-comment",
     *   tags={"Review"},
     *   summary="send User rating",
     *   description="send User rating",
     *   operationId="SendCommentRating",
     *      @OA\Parameter(
     *     name="star",
     *     required=true,
     *     in="query",
     *     description="The star in 1,2,3,4,5",
     *     @OA\Schema(
     *         type="integer"
     *     )
     *   ),
     *     @OA\Parameter(
     *     name="comment",
     *     required=true,
     *     in="query",
     *     description="The comment",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),   @OA\Parameter(
     *     name="traveler_id",
     *     required=true,
     *     in="query",
     *     description="The id of the traveler",
     *     @OA\Schema(
     *         type="integer"
     *     )
     *   ),   @OA\Parameter(
     *     name="trip_id",
     *     required=true,
     *     in="query",
     *     description="The id of the trip",
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
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function push(Request $request)
    {
        $data = $request->all((new TravelerReview())->getFillable());
        $data['author_id'] = auth()->id();
        if ($data["author_id"] == $data["traveler_id"]) {
            return $this->liteResponse(config("code.request.NOT_AUTHORIZED"), null, "You can afford this action");
        }
        (new TravelerRatingController())->add($request);
        return $this->save($data);
    }

    protected function validator(&$data)
    {
        return Validator::make($data, [
            "comment" => ["required"],
            "author_id" => ['required', "exists:users,id"],
            "traveler_id" => ['required', "exists:users,id"],
            "trip_id" => ['required', "exists:trips,id"],
        ]);
    }

}
