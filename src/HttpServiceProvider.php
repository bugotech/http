<?php namespace Bugotech\Http;

use Illuminate\Routing\RoutingServiceProvider;

class HttpServiceProvider extends RoutingServiceProvider
{
    public function register()
    {
        parent::register();

        $this->app->alias('router', '\Illuminate\Routing\Router');
    }

}
