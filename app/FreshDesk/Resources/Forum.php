<?php
/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 20/04/2016
 * Time: 2:32 PM
 */

namespace App\FreshDesk\Resources;

use App\FreshDesk\Resources\Traits\AllTrait;
use App\FreshDesk\Resources\Traits\DeleteTrait;
use App\FreshDesk\Resources\Traits\MonitorTrait;
use App\FreshDesk\Resources\Traits\UpdateTrait;
use App\FreshDesk\Resources\Traits\ViewTrait;

/**
 *
 * Forum resource
 *
 * Provides access to the forum resources
 *
 * @package Api\Resources
 */
class Forum extends AbstractResource
{

    use AllTrait, ViewTrait, UpdateTrait, DeleteTrait, MonitorTrait;

    /**
     * The forums resource endpoint
     *
     * @var string
     * @internal
     */
    protected $endpoint = '/discussions/forums';

    /**
     * The resource endpoint
     *
     * @var string
     * @internal
     */
    protected $categoryEndpoint = '/discussions/categories';

    /**
     * Creates the category endpoint (for creating forums)
     *
     * @param integer $id
     * @return string
     * @internal
     */
    private function categoryEndpoint($id = null)
    {
        return $id === null ? $this->categoryEndpoint : $this->categoryEndpoint . '/' . $id;
    }

    /**
     *
     * Create a forum for a category.
     *
     * @param int $id The category Id
     * @param array $data
     *
     * @return array|null
     * @throws \App\FreshDesk\Exceptions\AccessDeniedException
     * @throws \App\FreshDesk\Exceptions\ApiException
     * @throws \App\FreshDesk\Exceptions\AuthenticationException
     * @throws \App\FreshDesk\Exceptions\ConflictingStateException
     * @throws \App\FreshDesk\Exceptions\NotFoundException
     * @throws \App\FreshDesk\Exceptions\RateLimitExceededException
     * @throws \App\FreshDesk\Exceptions\UnsupportedContentTypeException
     * @throws \App\FreshDesk\Exceptions\MethodNotAllowedException
     * @throws \App\FreshDesk\Exceptions\UnsupportedAcceptHeaderException
     * @throws \App\FreshDesk\Exceptions\ValidationException
     *@api
     */
    public function create($id, array $data)
    {
        return $this->api()->request('POST', $this->categoryEndpoint($id), $data);
    }
}
