<?php namespace Bugotech\Http\Events;

use Bugotech\Http\Router;
use Bugotech\Events\Event;

abstract class RegisterRoutes extends Event
{
    /**
     * @var Router
     */
    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }
}
