<?php

namespace App\Http\Controllers;

use App\Models\LuggageRequestWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LuggageRequestWalletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function create(array $data)
    {
        return LuggageRequestWallet::create($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all((new LuggageRequestWallet())->getFillable());
        return $this->save($data);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\LuggageRequestWallet $luggageRequestWallet
     * @return \Illuminate\Http\Response
     */
    public function show(LuggageRequestWallet $luggageRequestWallet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\LuggageRequestWallet $luggageRequestWallet
     * @return \Illuminate\Http\Response
     */
    public function edit(LuggageRequestWallet $luggageRequestWallet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\LuggageRequestWallet $luggageRequestWallet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LuggageRequestWallet $luggageRequestWallet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\LuggageRequestWallet $luggageRequestWallet
     * @return \Illuminate\Http\Response
     */
    public function destroy(LuggageRequestWallet $luggageRequestWallet)
    {
        //
    }

    protected function validator(&$data)
    {
        return Validator::make($data, [
            "wallet_id" => ["required", "exists:wallets,id"],
            "luggage_request_id" => ["required", "exists:luggage_requests,id"],
        ]);
    }
}
