<?php
/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 20/04/2016
 * Time: 2:32 PM
 */

namespace App\FreshDesk\Resources;

use App\FreshDesk\Resources\Traits\AllTrait;
use App\FreshDesk\Resources\Traits\CreateTrait;
use App\FreshDesk\Resources\Traits\ViewTrait;

/**
 * Agent resource
 *
 * This provides access to the agent resources
 *
 * @package Api\Resources
 */
class Agent extends AbstractResource
{

    use AllTrait, CreateTrait, ViewTrait;

    /**
     * The resource endpoint
     * @internal
     * @var string
     */
    protected $endpoint = '/agents';

    /**
     *
     * Get the currently authenticated agent
     *
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
    public function current(array $query = null)
    {
        return $this->api()->request('GET', $this->endpoint('me'), null, $query);
    }
}
