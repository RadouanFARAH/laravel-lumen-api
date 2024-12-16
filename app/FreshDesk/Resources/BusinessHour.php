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
 * Business Hour resource
 *
 * This provides access to the business hour resources
 *
 * @package Api\Resources
 */
class BusinessHour extends AbstractResource
{

    use AllTrait, ViewTrait;

    /**
     * The resource endpoint
     *
     * @var string
     * @internal
     */
    protected $endpoint = '/business_hours';
}
