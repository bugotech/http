<?php namespace Bugotech\Http;

use Closure;

class Router extends \Illuminate\Routing\Router
{
    /**
     * Registar rota de API.
     * @param array $attributes
     * @param callable $callback
     */
    public function api(array $attributes, Closure $callback)
    {
        $attributes['prefix'] = array_key_exists('prefix', $attributes) ? sprintf('%s/%s', 'api', $attributes['prefix']) : 'api';
        parent::group($attributes, $callback);
    }

    /**
     * Registrar rotas publicas.
     * @param callable $callback
     */
    public function onPublic(Closure $callback)
    {
        event()->listen('Bugotech\Http\Events\PublicRegisterRoutes', $callback);
    }

    /**
     * Registrar rotas publicas com inquilino definido.
     * @param callable $callback
     */
    public function onPublicTenant(Closure $callback)
    {
        event()->listen('Bugotech\Http\Events\PublicTenantRegisterRoutes', $callback);
    }

    /**
     * Registrar rotas privadas com inquilino definido.
     * @param callable $callback
     */
    public function onPrivate(Closure $callback)
    {
        event()->listen('Bugotech\Http\Events\PrivateRegisterRoutes', $callback);
    }

    /**
     * Registrar rotas API.
     * @param callable $callback
     */
    public function onApi(Closure $callback)
    {
        event()->listen('Bugotech\Http\Events\ApiRegisterRoutes', $callback);
    }
}