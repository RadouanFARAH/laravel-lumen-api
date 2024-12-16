<?php

namespace App\Http\Controllers;

use App\FreshDesk\Api;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    protected $api;

    public function __construct()
    {
        $this->api = new Api("n5f4yxsMdOJGQbFqB1JM", "newaccount1627999686728");
    }

    public function fields()
    {
        try {
            return $this->liteResponse(config("code.request.SUCCESS"), $this->api->tickets->fields());
        } catch (\Exception $exception) {
            return $this->liteResponse(config("code.request.EXCEPTION"), $exception->getMessage());
        }
    }

    /**
     * @OA\Post(
     *    path="/api/user/support/create",
     *   tags={"Support"},
     *   summary="Create ticket",
     *   description="",
     *   operationId="supportCreate",
     *   @OA\Parameter(
     *         name="description",
     *         in="query",
     *         description="the description",
     *         required=true,
     *         @OA\Schema(
     *         type="string"
     *         ),
     *         style="form"
     *     ),
     *   @OA\Parameter(
     *         name="subject",
     *         in="query",
     *         description="the subject",
     *         required=true,
     *         @OA\Schema(
     *         type="string"
     *         ),
     *         style="form"
     *     ),
     *   @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="the status should be 2,3,4,5,6",
     *         required=true,
     *         @OA\Schema(
     *         type="string"
     *         ),
     *         style="form"
     *     ),
     *     @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\Schema(type="json"),
     *
     *   ),
     * )
     *
     * @param Request $request
     *
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function send(Request $request)
    {
        try {
            $data = $request->all();
            $data['priority'] = 1;
            $data['email'] = auth()->user()->email;
            $data['name'] = auth()->user()->first_name . " " . auth()->user()->last_name;
            $all = $this->api->tickets->create($data);
            return $this->liteResponse(config("code.request.SUCCESS"), $all);
        } catch (\Exception $exception) {
            return $this->liteResponse(config("code.request.EXCEPTION"), $exception->getMessage());
        }

    }


    public function tickets()
    {
        try {
            $all = $this->api->tickets->all();
            return $this->liteResponse(config("code.request.SUCCESS"), $all);
        } catch (\Exception $exception) {
            return $this->liteResponse(config("code.request.EXCEPTION"), $exception->getMessage());
        }
    }

    /**
     * @OA\Post(
     *    path="/api/user/support/detail",
     *   tags={"Support"},
     *   summary="get ticket",
     *   description="",
     *   operationId="tikectDetail",
     *   @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="the ticket id",
     *         required=true,
     *         @OA\Schema(
     *         type="string"
     *         ),
     *         style="form"
     *     ),
     *     @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\Schema(type="json"),
     *
     *   ),
     * )
     * @param Request $request
     *
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function ticketDetail(Request $request)
    {
        try {
            return $this->liteResponse(config("code.request.SUCCESS"), $this->api->tickets->view($request->id));
        } catch (\Exception $exception) {
            return $this->liteResponse(config("code.request.EXCEPTION"), $exception->getMessage());
        }
    }

    /**
     * @OA\Post(
     *    path="/api/user/support/mine",
     *   tags={"Support"},
     *   summary="get my tickets",
     *   description="",
     *   operationId="tikectMine",
     *     @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\Schema(type="json"),
     *
     *   ),
     * )
     * @param Request $request
     *
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function mine(Request $request)
    {
        try {
            return $this->liteResponse(config("code.request.SUCCESS"), $this->api->tickets->all(["email"=>auth()->user()->email]));
        } catch (\Exception $exception) {
            return $this->liteResponse(config("code.request.EXCEPTION"), $exception->getMessage());
        }
    }

    /**
     * @OA\Post(
     *    path="/api/user/support/conversation",
     *   tags={"Support"},
     *   summary="get ticket conversation",
     *   description="",
     *   operationId="tikectCoversation",
     *     @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\Schema(type="json"),
     *
     *   ),
     * )
     * @param Request $request
     *
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function conversations(Request $request)
    {
        try {
            return $this->liteResponse(config("code.request.SUCCESS"), $this->api->tickets->conversations($request->id));
        } catch (\Exception $exception) {
            return $this->liteResponse(config("code.request.EXCEPTION"), $exception->getMessage());
        }
    }
}
