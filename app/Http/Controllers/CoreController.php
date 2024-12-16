<?php


namespace App\Http\Controllers;


use App\Exceptions\LiteResponseException;
use App\Http\ResponseParser\DefResponse;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

abstract class CoreController extends Controller
{
    protected $excludedUpdateAttributes = [];
    protected $pagination = 50;


    /**
     * Record the new model
     *
     * @param array $data all required fields that should be record
     *
     * @return Model
     */
    public function create(array $data): Model
    {
        if (array_key_exists("password", $data)) {
            $data["password"] = Hash::make($data["password"]);
        }
        return $this->getModel()::create($data);
    }

    /**
     * @return BaseModel the model to manipulate
     */
    abstract function getModel(): BaseModel;

    public function saved($model)
    {
        $model->refresh();
        $this->onAfterAdd($model);
    }

    /**
     * Fire on model record with success
     *
     * @param Model $model the new recorded model
     */
    public function onAfterAdd(Model $model)
    {

    }

    /**
     * Record a model according to his fillable and assets attributes
     *
     * @param Request $request the request query containing all parameter
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function add(Request $request)
    {
        $data = $request->all($this->getModel()->getFillable());

        if (array_key_exists("phone", $data)) {
            $data["phone"] = $this->cleanPhone($data["phone"]);
        }

        $password = null;
        if (array_key_exists("password", $data)) {
            $password = Str::random(8);
            $data["password"] = $password;
        }

        //make specific data transformation or custom validation
        try {
            $this->onBeforeAdd($data);
        } catch (\Exception $exception) {
            if ($exception instanceof LiteResponseException) {
                return $this->liteResponse($exception->getCode(), $exception->getData(), $exception->getMessage());
            } else {
                return $this->liteResponse(config("code.request.EXCEPTION"), null, $exception->getMessage());
            }
        }

        $model = $this->getModel();
        //save all file
        foreach ($model->getAssets() as $asset) {
            if (!is_string($data[$asset])) {
                $data[$asset] = $this->storeFile($asset, $this->getFileDirectory());
            }
        }

        //save model
        $response = new DefResponse($this->save($data));
        if ($response->isSuccess()) {
            try {
                if (!empty($password)) {
                    //Send mail notification to newly created account if user has password
                    //Notification::send($this->getModel()->where("id", $response->getData()["id"])->first(), $this->getNotification($data, $password));
                }
            } catch (\Exception $exception) {
                if ($exception instanceof LiteResponseException) {
                    return $this->liteResponse($exception->getCode(), $exception->getData(), $exception->getMessage());
                } else {
                    return $this->liteResponse(config("code.request.EXCEPTION"), null, $exception->getMessage());
                }
            }
        }
        return $response->getResponse();
    }

    public function save(array $data)
    {
        $validator = $this->validator($data);
        if ($validator->fails())
            return $this->liteResponse(config('code.request.VALIDATION_ERROR'), $validator->errors());
        try {
            $model =  $this->create($data);
            $this->saved( $model);
            return $this->liteResponse(config('code.request.SUCCESS'), $model);
        } catch (\Exception $exception) {
            return $this->liteResponse(config('code.request.FAILURE'),null, $exception->getMessage());
        }
    }

    /**
     * Fire before inserting the new record
     *
     * @param array $data the get from request with out any change on field except for email and password
     */
    public function onBeforeAdd(array &$data): void
    {

    }

    /**
     * get the upload directory for file of the current model
     *
     * @return string the directory where files will be saved
     */
    public function getFileDirectory(): string
    {
        return "";
    }

    /**
     * @param array  $data     contain any data stored
     * @param string $password generated for the user
     *
     * @return  the mailable notification to send to the new created model
     */
    public function getNotification($data, $password)
    {
        return null;
    }

    /**
     * Search account by given available input
     *
     * @param Request $request
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function search(Request $request)
    {
        $query = $this->getModel()::query();

        $inclusive = $request->boolean("inclusive", true);
        $this->injectDefaultSearchCriteria($query, $request, $inclusive);

        $this->specificSearchCriteria($query, $request);

        if($request->has("perPage") and is_integer($request->perPage)){
            $this->pagination = $request->perPage;
        }

        return $this->liteResponse(config("code.request.SUCCESS"), $query->orderByDesc("created_at")->paginate($this->pagination));
    }

    /**
     * Add default field attribute search criteria
     *
     * @param      $query
     * @param      $request
     * @param bool $inclusive
     */
    public function injectDefaultSearchCriteria($query, $request, $inclusive = true)
    {
        $keywords = [];
        foreach ($this->getModel()->getFillable() as $input) {
            $value = $request->{$input};
            if (!empty($value)) {
                //Build query
                array_push($keywords, $value);
            }
        }
        $query->search(join(" ", $keywords), null, $inclusive);
    }

    /**
     * Add some specific search criteria
     *
     * @param $query
     * @param $request
     */
    public function specificSearchCriteria($query, $request)
    {
    }

    /**
     * Update any model according to his available field in the fillable and assets attributes
     *
     * @param Request $request
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(Request $request)
    {
        //get model fillable attributes
        $data = $request->all($this->getModel()->getFillable());

        //Clean phone
        if (array_key_exists("phone", $data)) {
            $data["phone"] = $this->cleanPhone($data["phone"]);
        }

        //save all files
        foreach ($this->getModel()->getAssets() as $asset) {
            $data[$asset] = $this->storeFile($asset, $this->getFileDirectory());
        }

        //remove empty value
        $data = array_filter($data);
        if (empty($data)) {
            return $this->liteResponse(config("code.request.VALIDATION_ERROR"), null, "Empty data set, no value to update");
        }

        $this->onBeforeUpdate($data);

        //verify that the specified id exists
        $model = $this->getModel()->where("id", $request->id)->first();
        if (empty($model)) {
            return $this->liteResponse(config("code.request.NOT_FOUND"));
        }

        //This part remove attribute that should not be updated
        if (is_array($this->excludedUpdateAttributes) and !empty($this->excludedUpdateAttributes)) {
            foreach ($this->excludedUpdateAttributes as $excludedUpdateAttribute) {
                if (array_key_exists($excludedUpdateAttribute, $data)) {
                    unset($data[$excludedUpdateAttribute]);
                }
            }
        }

        //validation using the updateRule
        $validator = Validator::make($data, $this->updateRule($model->id));
        if ($validator->fails()) {
            return $this->liteResponse(config("code.request.VALIDATION_ERROR"), $validator->errors());
        }

        try {
            //Updating model
            $this->getModel()->where("id", $request->id)->update($data);
            //fetch model and return it we new values
            $updatedModel = $this->getModel()->where("id", $request->id)->first();

            $this->onAfterUpdate($updatedModel);

            return $this->liteResponse(config("code.request.SUCCESS"), $updatedModel);
        } catch (\Exception $exception) {
            if ($exception instanceof LiteResponseException) {
                return $this->liteResponse($exception->getCode(), $exception->getData(), $exception->getMessage());
            } else {
                return $this->liteResponse(config("code.request.EXCEPTION"), null, $exception->getMessage());
            }
        }
    }

    /**
     * Fire before updating an existing record
     *
     * @param array $data the get from request with out any change on field
     */
    public function onBeforeUpdate(array &$data): void
    {

    }

    /**
     * Validation for model update
     *
     * @param mixed $modelId of the model about to be updated
     *
     * @return array and array of validation
     */
    abstract function updateRule($modelId): array;

    /**
     * Fire on model update with success
     *
     * @param Model $model the new updated model
     */
    public function onAfterUpdate(Model $model)
    {

    }

    public function delete(Request $request)
    {
        try {
            $model = $this->getModel()->where("id", $request->id)->first();
            if (empty($model)) {
                return $this->liteResponse(config("code.request.NOT_FOUND"));
            }

            $this->onBeforeDelete($model);

            $model->delete();

            $this->onAfterDelete($model);

            return $this->liteResponse(config("code.request.SUCCESS"));
        } catch (\Exception $exception) {
            if ($exception instanceof LiteResponseException) {
                return $this->liteResponse($exception->getCode(), $exception->getData(), $exception->getMessage());
            } else {
                return $this->liteResponse(config("code.request.EXCEPTION"), null, $exception->getMessage());
            }
        }
    }

    /**
     * Fire before deleting the model
     *
     * @param BaseModel $model the record that will be deleted
     */
    public function onBeforeDelete(BaseModel $model)
    {
    }

    /**
     * Fire after the model has been deleted
     *
     * @param BaseModel $model
     */
    public function onAfterDelete(BaseModel $model)
    {
    }

    /**
     * Restore a soft deleted model
     *
     * @param Request $request
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function restore(Request $request)
    {
        try {
            $model = $this->getModel()->onlyTrashed()->where("id", $request->id)->first();
            if (empty($model)) {
                return $this->liteResponse(config("code.request.NOT_FOUND"));
            }

            $model->restore();

            $this->onRestoreCompleted($model);

            return $this->liteResponse(config("code.request.SUCCESS"));
        } catch (\Exception $exception) {
            if ($exception instanceof LiteResponseException) {
                return $this->liteResponse($exception->getCode(), $exception->getData(), $exception->getMessage());
            } else {
                return $this->liteResponse(config("code.request.EXCEPTION"), null, $exception->getMessage());
            }
        }
    }

    /**
     * Fire when the model has been restore in case to allow other action such as restoring his children
     *
     * @param BaseModel $model the model restored
     */
    public function onRestoreCompleted(BaseModel $model)
    {
    }

    protected function validator(&$data)
    {
        return Validator::make($data, $this->addRule());
    }

    /**
     * Validation for model creation
     *
     *
     * @return array and array of validation
     */
    abstract function addRule(): array;
}
