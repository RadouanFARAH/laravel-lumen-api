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
 * Provides access to the Email Config resources
 *
 * @package Api\Resources
 */
class EmailConfig extends AbstractResource
{

    use AllTrait, ViewTrait;

    /**
     * The resource endpoint
     *
     * @var string
     * @internal
     */
    protected $endpoint = '/email_configs';
}
