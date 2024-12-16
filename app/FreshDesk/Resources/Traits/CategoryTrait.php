<?php

namespace App\FreshDesk\Resources\Traits;

/**
 * View Trait
 *
 * @package Freshdesk\Resources\Traits
 *
 */
trait CategoryTrait
{

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
    public function forCategory($id, array $query = null)
    {
        return $this->api()->request('GET', $this->categoryEndpoint($id) . $this->base(), null, $query);
    }

    private function base()
    {
        $arr = explode('/', $this->endpoint);
        return '/' . $arr[2];
    }
}
