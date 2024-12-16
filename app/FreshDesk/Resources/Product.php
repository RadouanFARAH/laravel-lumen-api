<?php
/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 20/04/2016
 * Time: 2:32 PM
 */

namespace App\FreshDesk\Resources;
use App\FreshDesk\Resources\Traits\AllTrait;
use App\FreshDesk\Resources\Traits\ViewTrait;

/**
 * Email Config resource
 *
 * @package Api\Resources
 */
class Product extends AbstractResource
{

    use AllTrait, ViewTrait;

    /**
     * The resource endpoint
     *
     * @var string
     * @internal
     *
     */
    protected $endpoint = '/products';
}
