<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 21/04/2016
 * Time: 9:10 AM
 */

namespace App\FreshDesk\Resources\Traits;

/**
 * View Trait
 *
 * @package Freshdesk\Resources\Traits
 *
 */
trait ViewTrait
{
    /**
     * @param integer $end string
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
     * View a resource
     *
     * Use 'include' to embed additional details in the response. Each include will consume an additional credit.
     * For example if you embed the requester and company information you will be charged a total of 3 API credits for the call.
     * See Freshdesk's documentation for details.
     *
     * @param int $id The resource id
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
    public function view($id, array $query = null)
    {
        return $this->api()->request('GET', $this->endpoint($id), null, $query);
    }
}
