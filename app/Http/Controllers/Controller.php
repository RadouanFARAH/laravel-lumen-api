<?php

namespace App\Http\Controllers;

use App\Http\ResponseParser\Builder;
use App\Http\ResponseParser\DefResponse;
use App\Models\Transaction;
use App\Models\WalletIdGen;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Lumen\Routing\Controller as BaseController;

/**
 *
 * * @OA\Tag(
 *     name="Response code",
 *     description="
 *     'TOKEN_EXPIRED' => 1, 'BLACK_LISTED_TOKEN' => 2, 'INVALID_TOKEN' => 3, 'NO_TOKEN' => 4,
 *     'USER_NOT_FOUND' => 5,
 *     'WRONG_JSON_FORMAT' => 6,
 *     'SUCCESS' => 1000, 'FAILURE' => 1001, 'VALIDATION_ERROR' => 1002, 'EXPIRED' => 1003, 'DATA_EXIST' => 1004,
 *     'NOT_AUTHORIZED' => 1005,
 *     'ACCOUNT_NOT_VERIFY' => 1100,'WRONG_USERNAME' => 1101,'WRONG_PASSWORD' => 1102,'WRONG_CREDENTIALS' => 1103,
 *     'ACCOUNT_VERIFIED' => 1104,'NOT_EXISTS' => 1105"
 * )
 * @OA\ExternalDocumentation(
 *     description="Find out more about Swagger",
 *     url="http://swagger.io"
 * )
 */

/**
 * @license Apache 2.0
 */

/**
 * @OA\Info(
 *     description="Luggin Documentation | Power By N-Y Corp",
 *     version="1.0.0",
 *     title="Luggin Documentation",
 *     termsOfService="http://swagger.io/terms/",
 *     @OA\Contact(
 *         email="yann.ngalle@ny-corp.io"
 *     ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 */


/**
 * @OA\SecurityScheme(
 *   securityScheme="Bearer",
 *   type="apiKey",
 *   description="JWT Bearer token",
 *   name="Authorization",
 *   in="header",
 * )
 */
class Controller extends BaseController
{


    const ROOT_DIRECTORY = "upload";
    const DOC_DIRECTORY = "docs";
    const PARCEL_DIRECTORY = "parcels";
    const PROFILE_DIRECTORY = "profiles";

    const NOTIFY_CONTEXT_LUGGAGE_REQUEST = "LRE";

    public const TRANSACTION = 'TRA';
    public const WALLET = 'WAL';

    /**
     * @param $context
     * @return string|null
     */
    public function genId($context)
    {
        switch ($context) {
            case self::WALLET:
                return $this->getNext($context, new WalletIdGen());
            case self::TRANSACTION:
                return $this->getNext($context, new Transaction());
            default:
                return null;
        }
    }

    /**
     * @param       $context
     * @param Model $model
     * @param bool $useSoftDelete
     * @return string|null
     */
    private function getNext($context, Model $model, bool $useSoftDelete = false)
    {
        $format = '0000000000';
        $id = array();
        $final_id = null;
        $step = 1;
        $id['context'] = $context;

        if ($useSoftDelete) {
            $last_model = $model->withTrashed()->where('id', 'like', '%' . $context . '%')->orderByDesc("created_at")->first();
        } else {
            $last_model = $model->where('id', 'like', '%' . $context . '%')->orderByDesc("created_at")->first();
        }

        if (empty($last_model))
            $last_id = '0';
        else
            $last_id = explode($context, $last_model->id)[1];

        do {
            $id['size'] = intval($last_id) + $step;
            $id['size'] = substr($format, 0, strlen($format) - strlen($id['size'])) . $id['size'];
            $final_id = join('', $id);

            if ($useSoftDelete) {
                $done = !empty($model->withTrashed()->find($final_id));
            } else {
                $done = !empty($model->find($final_id));
            }

            if ($done)
                $step += 1;
        } while ($done);
        return $final_id;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param array $data
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function save(array $data)
    {
        $validator = $this->validator($data);
        if ($validator->fails()) {
            \Log::info('Validation failed', ['errors' => $validator->errors()]);
            return $this->liteResponse(config('code.request.VALIDATION_ERROR'), $validator->errors());
        }

        try {
            $response = new DefResponse($this->liteResponse(config('code.request.SUCCESS'), $this->create($data)));
            $this->saved($response);
            return $response->getResponse();
        } catch (\Exception $exception) {
            \Log::error('Exception in save function', ['error' => $exception->getMessage()]);
            return $this->liteResponse(config('code.request.FAILURE'), null, $exception->getMessage());
        }
    }


    /**
     * Default validator in case of non specification
     *
     * @param $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(&$data)
    {
        return Validator::make($data, ['*' => 'required']);
    }

    /**
     * parsing api response according the specification
     *
     * @param      $code
     * @param null $data
     * @param null $message
     * @param null $token
     *
     * @return array|JsonResponse
     */
    public function liteResponse($code, $data = null, $message = null, $token = null)
    {

        try {
            $builder = new Builder($code, $message);
            $builder->setData($data);
            $builder->setToken($token);
            return  response()->json($builder->reply(), 200, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        } catch (Exception $e) {
            return $this->liteResponse(\config("code.request.EXCEPTION"), $e->getMessage());
        }
    }

    /**
     * Default create in case of non specification
     *
     * @param array $data
     *
     * @return Response
     */
    public function create(array $data)
    {

       

        return null;
    }
 
    public function saved($response) {}

    /**
     * Claim authorization to access remote service
     *
     * @param       $data
     * @param       $microservice
     * @param       $service
     * @param       $method
     *
     * @return array|JsonResponse|mixed
     */
    public function call($data, $microservice, $service, $method = "post")
    {
        $http = Http::baseUrl(env($microservice))
            ->withHeaders([
                'Access-Control-Allow-Origin' => encrypt(request()->url()),
            ]);

        foreach (request()->allFiles() as $key => $file) {
            $http->attach($key, fopen($file->getRealPath(), 'r'), $file->getClientOriginalName(), ['Accept' => $file->getClientMimeType()]);
        }

        $response = $http->{$method}($service, $data);

        if ($response->successful())
            return response()->json($response->json());
        return $this->liteResponse(config('code.request.SERVICE_NOT_AVAILABLE'), [
            "context" => $http,
            "remote" => $response,
        ]);
    }

    public function switchLang($locale)
    {
        if (in_array($locale, Config::get('app.locales'))) {
            app()->setLocale($locale);
            Session::put("locale", $locale);
        }
        return redirect(URL::previous());
    }

    /**
     * @param string $filesName
     * @param string $directory
     * @return false|string
     */
    public function saveMedias(string $filesName, $directory = "")
    {
        $allFiles = \request()->allFiles();
        if ($allFiles != null and array_key_exists($filesName, $allFiles)) {
            $otherFilesData = array();
            foreach ($allFiles[$filesName] ?? array() as $key => $image) {
                array_push($otherFilesData, $this->saveMedia($image, $directory));
            }
            return json_encode($otherFilesData);
        } else {
            return json_encode([]);
        }
    }

    public function saveMedia($file, $directory = "")
    {
        if (empty($file)) {
            return null;
        }

        $path = join('/', [self::ROOT_DIRECTORY, Str::replaceLast("/", '', $directory)]);
        $name = time() . "." . $file->getClientOriginalExtension();
        return $file->move($path, $name)->getPathname();
    }
}
