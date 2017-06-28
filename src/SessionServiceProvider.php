<?php namespace Bugotech\Http;

use Illuminate\Session\Middleware\StartSession;

class SessionServiceProvider extends \Illuminate\Session\SessionServiceProvider
{
    /**
     * Register.
     */
    public function register()
    {
        $this->registerSessionManager();

        $this->registerSessionDriver();

        $this->app->singleton('Illuminate\Session\Middleware\StartSession', function ($app) {
            return new StartSession($app['session']);
        });
    }
}
