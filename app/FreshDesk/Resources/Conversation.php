<?php

/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 20/04/2016
 * Time: 2:32 PM
 */

namespace App\FreshDesk\Resources;

use App\FreshDesk\Resources\Traits\DeleteTrait;
use App\FreshDesk\Resources\Traits\UpdateTrait;

/**
 * Conversation resource
 *
 * This provides access to the agent resources
 *
 * @package Api\Resources
 */
class Conversation extends AbstractResource
{

    use UpdateTrait, DeleteTrait;

    /**
     * The resource endpoint
     * @internal
     * @var string
     */
    protected $endpoint = '/conversations';

    /**
     * The ticket resource endpoint
     *
     * @var string
     * @internal
     */
    private $ticketsEndpoint = '/tickets';

    /**
     * Creates the ticket endpoint
     * @param string $id
     * @return string
     * @internal
     */
    protected function ticketsEndpoint($id = null)
    {
        return $id === null ? $this->ticketsEndpoint : $this->ticketsEndpoint . '/' . $id;
    }

    /**
     *
     * Reply to a ticket
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
    public function reply($id, array $data)
    {
        return $this->api()->request('POST', $this->ticketsEndpoint($id . '/reply'), $data);
    }

    /**
     *
     * Reply to a ticket with attachment
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
    public function replyWithAttachment($id, array $data)
    {
        return $this->api()->requestMultipart('POST', $this->ticketsEndpoint($id . '/reply'), $data);
    }

    /**
     *
     * Create a note for a ticket
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
    public function note($id, array $data)
    {
        return $this->api()->request('POST', $this->ticketsEndpoint($id . '/notes'), $data);
    }
}
