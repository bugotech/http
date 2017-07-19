<?php namespace Bugotech\Http\Events;

use Bugotech\Http\Router;
use Bugotech\Events\Event;

abstract class RegisterRoutes extends Event
{
    /**
     * @var Router
     */
    public $router;

    /**
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }
}
