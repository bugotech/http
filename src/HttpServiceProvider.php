<?php namespace Bugotech\Http;

use Illuminate\Routing\RoutingServiceProvider;

class HttpServiceProvider extends RoutingServiceProvider
{
    public function register()
    {
        parent::register();

        // Carregar config
        $this->app->configure('session', __DIR__ . '/../config/session.php');
        $this->app->configure('routing', __DIR__ . '/../config/routing.php');

        // Alias
        $this->app->alias('url', '\Illuminate\Contracts\Routing\UrlGenerator');
        $this->app->alias('router', '\Bugotech\Http\Router');
        $this->app->alias('cookie', '\Illuminate\Contracts\Cookie\Factory');

        // Registrar comando para preparar o ambiente
        if (config('app.env') != 'production') {
            $this->commands('\Bugotech\Http\Console\InstallCommand');
        }

        // Mapear rotas
        $this->mapRoutes();
    }

    protected function registerRouter()
    {
        $this->app->singleton('router', function ($app) {
            return new Router($app['events'], $app);
        });
    }

    /**
     * Mapear as rotas do sistema.
     */
    protected function mapRoutes()
    {
        // Web
        router()->group(['middleware' => ['web']], function (Router $router) {
            $file_route = app_path('routes.php');
            if (files()->exists($file_route)) {
                require $file_route;
            }
        });

        // Api
        router()->group(['middleware' => ['api'], 'prefix' => 'api'], function (Router $router) {
            $file_route = app_path('routes_api.php');
            if (files()->exists($file_route)) {
                require $file_route;
            }
        });
    }
}
