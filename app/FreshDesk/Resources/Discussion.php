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
 * Forum category resource
 *
 * This provides access to the forum category resources
 *
 * @package Api\Resources
 */
class Discussion extends AbstractResource
{

    use AllTrait, CreateTrait, ViewTrait, UpdateTrait, DeleteTrait;

    /**
     * The resource endpoint
     *
     * @internal
     * @var string
     *
     */
    protected $endpoint = '/discussions/categories';

}
