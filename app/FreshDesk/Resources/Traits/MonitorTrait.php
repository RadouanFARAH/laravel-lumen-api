<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 21/04/2016
 * Time: 10:23 AM
 */

namespace App\FreshDesk\Resources\Traits;

/**
 * Monitor Trait
 *
 * @package Freshdesk\Resources\Traits
 */
trait MonitorTrait
{

    /**
     * @param string $end string
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
     * Monitor a resource
     *
     * Monitor a resource for the given user
     *
     * @param $id The id of the resource
     * @param $userId The id of the user
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
    public function monitor($id, $userId)
    {
        $data = [
            'user_id' => $userId
        ];

        return $this->api()->request('POST', $this->endpoint($id . '/follow'), $data);
    }

    /**
     * Unmonitor a resource
     *
     * Unmonitor a resource for the given user
     *
     * @param $id The id of the resource
     * @param $userId The id of the user
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
    public function unmonitor($id, $userId)
    {
        $query = [
            'user_id' => $userId
        ];

        return $this->api()->request('POST', $this->endpoint($id . '/follow'), null, $query);
    }

    /**
     * Monitor status
     *
     * Get the monitoring status of the topic for the user
     *
     * @param $id The id of the resource
     * @param $userId The id of the user
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
    public function monitorStatus($id, $userId)
    {
        $query = [
            'user_id' => $userId
        ];

        return $this->api()->request('GET', $this->endpoint($id . '/follow'), null, $query);
    }
}
