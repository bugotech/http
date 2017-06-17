<?php namespace Bugotech\Http;

use Illuminate\Routing\Router;
use Illuminate\Routing\RoutingServiceProvider;

class HttpServiceProvider extends RoutingServiceProvider
{
    public function register()
    {
        parent::register();

        if (config('app.env') != 'production') {
            $this->commands('\Bugotech\Http\Console\InstallCommand');
        }

        $this->app->alias('router', '\Illuminate\Routing\Router');
    }

    protected function registerRouter()
    {
        $this->app->singleton('router', function ($app) {
            return new Router($app['events'], $app);
        });
    }
}
