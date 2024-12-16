<?php
/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 20/04/2016
 * Time: 2:32 PM
 */

namespace App\FreshDesk\Resources;
use App\FreshDesk\Resources\Traits\DeleteTrait;
use App\FreshDesk\Resources\Traits\MonitorTrait;
use App\FreshDesk\Resources\Traits\UpdateTrait;
use App\FreshDesk\Resources\Traits\ViewTrait;

/**
 *
 * Topic Resource
 *
 * Provides access to topic resources
 *
 * @package Api\Resources
 */
class Topic extends AbstractResource
{

    use ViewTrait, UpdateTrait, DeleteTrait, MonitorTrait;

    /**
     * The topic resource endpoint
     *
     * @var string
     * @internal
     */
    protected $endpoint = '/discussions/topics';

    /**
     * The forums resource endpoint
     *
     * @var string
     * @internal
     */
    private $forumsEndpoint = '/discussions/forums';

    /**
     * Creates the forums endpoint
     * @param string $id
     * @return string
     * @internal
     */
    protected function forumsEndpoint($id = null)
    {
        return $id === null ? $this->forumsEndpoint : $this->forumsEndpoint . '/' . $id;
    }

    /**
     *
     * Create a topic for a forum
     *
     * @param int $id
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
        return $this->api()->request('POST', $this->forumsEndpoint($id . '/topics'), $data);
    }

    /**
     *
     * List topics in a forum
     *
     * @param int $id
     * @param array|null $query
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
    public function all($id, array $query = null)
    {
        return $this->api()->request('GET', $this->forumsEndpoint($id . '/topics'), null, $query);
    }

}
