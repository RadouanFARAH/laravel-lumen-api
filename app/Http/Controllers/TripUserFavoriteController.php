<?php

namespace App\Http\Controllers;

use App\Models\TripUserFavorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TripUserFavoriteController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/user/trip/favorite",
     *   tags={"Trips"},
     *   summary="My favorite trips",
     *   description="List user favorite trips",
     *   operationId="myFavTrips",
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
    public function index()
    {
        return $this->liteResponse(config("code.request.SUCCESS"), TripUserFavorite::with(["trip"])->where("user_id", auth()->id())->paginate(20));
    }

    public function create(array $data)
    {
        return TripUserFavorite::create($data);
    }

    /**
     * @OA\Post(
     *     path="/api/user/trip/favorite/update",
     *   tags={"Trips"},
     *   summary="Favorite",
     *   description="Add or remove trip in favorites",
     *   operationId="updatefavList",
     *     @OA\Parameter(
     *     name="trip_id",
     *     required=true,
     *     in="query",
     *     description="The trip id",
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
     * @throws \Exception
     */
    public function update(Request $request)
    {
        $data = $request->all((new TripUserFavorite())->getFillable());
        $data["user_id"] = auth()->id();
        $fav = TripUserFavorite::where("user_id", $data["user_id"])->where("trip_id", $data["trip_id"]);
        if (empty($fav->first())) {
            return $this->save($data);
        } else {
            return $this->destroy($fav);
        }
    }
    
      public function check(Request $request)
    {
       
        $fav = TripUserFavorite::where("user_id", auth()->id())->where("trip_id", $request->trip_id)->first();
       
        if (empty($fav)) {
            return "false";
        } else {
            return "true";
        }
    }

    protected function validator(&$data)
    {
        return Validator::make($data, ['trip_id' => 'required|exists:trips,id','user_id'=>'required|exists:users,id']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $tripUserFavorite
     *
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy( $tripUserFavorite)
    {
        try {
            return $this->liteResponse(config("code.request.SUCCESS"), $tripUserFavorite->delete());
        } catch (\Exception $exception) {
            return $this->liteResponse(config('code.request.FAILURE'), $exception->getMessage());
        }
    }
}
