<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 21/04/2016
 * Time: 9:10 AM
 */

namespace App\FreshDesk\Resources\Traits;


/**
 * Create Trait
 *
 * @package Freshdesk\Resources\Traits
 */
trait CreateTrait
{

    /**
     * @param null $end string
     * @return string
     * @internal
     */
    abstract protected function endpoint($end = null);

    /**
     * @return \App\FreshDesk\Api
     * @internal
     */
    abstract protected function api();

    /**
     * Create a resource
     *
     * Create a resource with the supplied data
     *
     * @param array $data The data
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
    public function create(array $data)
    {
        return $this->api()->request('POST', $this->endpoint(), $data);
    }
}
