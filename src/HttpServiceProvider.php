<?php namespace Bugotech\Http;

use Bugotech\Http\Events\ApiRegisterRoutes;
use Illuminate\Routing\Events\RouteMatched;
use Bugotech\Http\Events\PublicRegisterRoutes;
use Illuminate\Routing\RoutingServiceProvider;
use Bugotech\Http\Events\PrivateRegisterRoutes;
use Bugotech\Http\Events\PublicTenantRegisterRoutes;

class HttpServiceProvider extends RoutingServiceProvider
{
    public function register()
    {
        parent::register();

        // Carregar config
        $this->app->configure('session', __DIR__ . '/../config/session.php');
        $this->app->configure('routing', __DIR__ . '/../config/routing.php');

        // Cookie
        $this->registerCookie();

        // Alias
        $this->app->alias('request', 'Illuminate\Http\Request');
        $this->app->alias('url', 'Illuminate\Contracts\Routing\UrlGenerator');
        $this->app->alias('router', 'Bugotech\Http\Router');
        $this->app->alias('cookie', 'Illuminate\Contracts\Cookie\QueueingFactory');
        $this->app->alias('cookie', 'Illuminate\Contracts\Cookie\Factory');

        // Alias para ajsutar problema ao carregar SessionManager
        $this->app->addContextualBinding('Illuminate\Session\SessionManager', '$app', $this->app);

        // Registrar comando para preparar o ambiente
        if (config('app.env') != 'production') {
            $this->commands('\Bugotech\Http\Console\InstallCommand');
        }

        // Mapear rotas
        $this->mapRoutes();

        // Share request in routes
        $this->shareRequestInUrl();
    }

    protected function registerRouter()
    {
        $this->app->singleton('router', function ($app) {
            return new Router($app['events'], $app);
        });
    }

    /**
     * Register the URL generator service.
     *
     * @return void
     */
    protected function registerUrlGenerator()
    {
        $this->app['url'] = $this->app->share(function ($app) {
            $routes = $app['router']->getRoutes();

            // The URL generator needs the route collection that exists on the router.
            // Keep in mind this is an object, so we're passing by references here
            // and all the registered routes will be available to the generator.
            $app->instance('routes', $routes);

            $url = new UrlGenerator(
                $routes, $app->rebinding(
                    'request', $this->requestRebinder()
                )
            );

            $url->setSessionResolver(function () {
                return $this->app['session'];
            });

            // If the route collection is "rebound", for example, when the routes stay
            // cached for the application, we will need to rebind the routes on the
            // URL generator instance so it has the latest version of the routes.
            $app->rebinding('routes', function ($app, $routes) {
                $app['url']->setRoutes($routes);
            });

            return $url;
        });
    }

    protected function registerCookie()
    {
        $this->app->singleton('cookie', function ($app) {
            $config = $app['config']['session'];

            return (new Cookie($app))->setDefaultPathAndDomain($config['path'], $config['domain'], $config['secure']);
        });
    }

    /**
     * Mapear as rotas do sistema.
     */
    protected function mapRoutes()
    {
        $this->app['events']->listen('kernel.handling', function () {
            // Gatilho para arquivo routes.php
            $file_route = app_path('routes.php');
            if (files()->exists($file_route)) {
                require $file_route;
            }

            // Web
            router()->group(['middleware' => ['web']], function (Router $router) {
                event()->fire(new PublicRegisterRoutes($router));
                event()->fire(new PublicTenantRegisterRoutes($router));
                event()->fire(new PrivateRegisterRoutes($router));
            });

            // Api
            router()->group(['middleware' => ['api'], 'prefix' => 'api'], function (Router $router) {
                event()->fire(new ApiRegisterRoutes($router));
            });
        });
    }

    /**
     * Register request params in url.
     */
    protected function shareRequestInUrl()
    {
        $this->app['events']->listen('Illuminate\Routing\Events\RouteMatched', function (RouteMatched $event) {
            foreach ($event->route->parameters() as $k => $v) {
                $this->app['url']->share($k, $v);
            }
        });
    }
}
