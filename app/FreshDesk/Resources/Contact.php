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
use App\FreshDesk\Resources\Traits\DeleteTrait;
use App\FreshDesk\Resources\Traits\UpdateTrait;
use App\FreshDesk\Resources\Traits\ViewTrait;

/**
 *
 * Contact resource
 *
 * Provides access to the contact resources
 *
 * @package Api\Resources
 */
class Contact extends AbstractResource
{
    use AllTrait, CreateTrait, ViewTrait, UpdateTrait, DeleteTrait;

    /**
     * The resource endpoint
     *
     * @var string
     * @internal
     */
    protected $endpoint = '/contacts';

    /**
     * List contact fields
     *
     * The agent whose credentials (API key or username/password) are being used to make this API call should be
     * authorised to view the contact fields
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
    public function fields(array $query = null)
    {
        return $this->api()->request('GET', '/contact_fields', null, $query);
    }

    /**
     * Convert a contact into an agent
     *
     * Note:
     * 1. The contact must have an email address in order to be converted into an agent.
     * 2. If your account has already reached the maximum number of agents, the API request will fail with HTTP error code 403
     * 3. The agent whose credentials (API key, username and password) were used to make this API call should be authorised to convert a contact into an agent
     *
     * @param int $id The agent id
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
     */
    public function makeAgent($id, array $query = null)
    {
        $end = $id . '/make_agent';

        return $this->api()->request('GET', $this->endpoint($end), null, $query);
    }

    public function hardDelete($id)
    {
        $end = $id . '/hard_delete';

        return $this->api()->request('DELETE', $this->endpoint($end), null, null);
    }
}
