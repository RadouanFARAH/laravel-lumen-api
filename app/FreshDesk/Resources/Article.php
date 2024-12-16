<?php

namespace App\FreshDesk\Resources;

use App\FreshDesk\Resources\Traits\DeleteTrait;
use App\FreshDesk\Resources\Traits\UpdateTrait;
use App\FreshDesk\Resources\Traits\ViewTrait;

/**
 * Forum category resource
 *
 * This provides access to the knowledge base category resources
 *
 * @package Api\Resources
 */
class Article extends AbstractResource
{

    use ViewTrait, UpdateTrait, DeleteTrait;

    /**
     * The resource endpoint
     *
     * @internal
     * @var string
     *
     */
    protected $endpoint = '/solutions/articles';

    /**
     * The resource endpoint
     *
     * @var string
     * @internal
     */
    protected $folderEndpoint = '/solutions/folders';

    /**
     * Creates the folder endpoint (for creating articles)
     *
     * @param integer $id
     * @return string
     * @internal
     */
    private function folderEndpoint($id = null)
    {
        return $id === null ? $this->folderEndpoint : $this->folderEndpoint . '/' . $id;
    }

    /**
     *
     * Create a folders for a category.
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
        return $this->api()->request('POST', $this->folderEndpoint($id), $data);
    }

    public function forFolder($id, array $query = null)
    {
        return $this->api()->request('GET', $this->folderEndpoint($id) . $this->base(), null, $query);
    }

    private function base()
    {
        $arr = explode('/', $this->endpoint);
        return '/' . $arr[2];
    }

}
