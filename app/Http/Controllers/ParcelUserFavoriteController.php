<?php

namespace App\Http\Controllers;

use App\Models\ParcelUserFavorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParcelUserFavoriteController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/user/parcel/favorite",
     *   tags={"Parcels"},
     *   summary="My favorite parcels",
     *   description="List user favorite parcels",
     *   operationId="myFavParcels",
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
        return $this->liteResponse(config("code.request.SUCCESS"), ParcelUserFavorite::with(["parcel"])->where("user_id", auth()->id())->paginate(20));
    }

    public function create(array $data)
    {
        return ParcelUserFavorite::create($data);
    }

    /**
     * @OA\Post(
     *     path="/api/user/parcel/favorite/update",
     *   tags={"Parcels"},
     *   summary="Favorite",
     *   description="Add or remove parcel_id in favorites",
     *   operationId="updateParcelfavList",
     *     @OA\Parameter(
     *     name="parcel_id",
     *     required=true,
     *     in="query",
     *     description="The parcel id",
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
        $data = $request->all((new ParcelUserFavorite())->getFillable());
        $data["user_id"] = auth()->id();
        $fav = ParcelUserFavorite::where("user_id", $data["user_id"])->where("parcel_id", $data["parcel_id"]);
        if (empty($fav->first())) {
            return $this->save($data);
        } else {
            return $this->destroy($fav);
        }
    }
    
      public function check(Request $request)
    {
     
        $fav = ParcelUserFavorite::where("user_id",auth()->id())->where("parcel_id", $request->parcel_id)->first();
       if (empty($fav)) {
            return "false";
        } else {
            return "true";
        }
    }

    protected function validator(&$data)
    {
        return Validator::make($data, ['parcel_id' => 'required|exists:parcels,id','user_id'=>'required|exists:users,id']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $parcelUserFavorite
     *
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy($parcelUserFavorite)
    {
        try {
            return $this->liteResponse(config("code.request.SUCCESS"), $parcelUserFavorite->delete());
        } catch (\Exception $exception) {
            return $this->liteResponse(config('code.request.FAILURE'), $exception->getMessage());
        }
    }
}
