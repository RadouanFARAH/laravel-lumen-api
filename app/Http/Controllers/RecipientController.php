<?php

namespace App\Http\Controllers;

use App\Models\Recipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RecipientController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/user/parcel/recipients/mine",
     *   tags={"Parcels"},
     *   summary="My recipients",
     *   description="List user recipients",
     *   operationId="myReceipients",
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
        return $this->liteResponse(config("code.request.SUCCESS"), Recipient::where("owner_id", auth()->id())->orderByDesc("created_at")->get());
    }

   public function create(array $data)
   {
       return Recipient::updateOrCreate(
           [
               'phone' => $data['phone'],
           ],
           $data);
   }

   protected function validator(&$data)
   {
       return Validator::make($data,[
           "name"=>"required",
           "phone"=>"required|string",
           "owner_id" => ["required", "exists:users,id"],
       ]);
   }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $data = $request->all((new Recipient())->getFillable());
        $data["owner_id"]=auth()->id();
       return $this->save($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Recipient  $recipient
     * @return \Illuminate\Http\Response
     */
    public function show(Recipient $recipient)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Recipient  $recipient
     * @return \Illuminate\Http\Response
     */
    public function edit(Recipient $recipient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Recipient  $recipient
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Recipient $recipient)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Recipient  $recipient
     * @return \Illuminate\Http\Response
     */
    public function destroy(Recipient $recipient)
    {
        //
    }
}
