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
 * SLA Policy resource
 *
 * Provides access to the SLA policy resources
 *
 * @package Api\Resources
 */
class SLAPolicy extends AbstractResource
{

    use AllTrait, ViewTrait;

    /**
     * The resource endpoint
     *
     * @var string
     * @internal
     */
    protected $endpoint = '/sla_policy';
}
