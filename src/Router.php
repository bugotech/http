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
     * @param int $priority
     */
    public function onPublic(Closure $callback, $priority = 0)
    {
        event()->listen('Bugotech\Http\Events\PublicRegisterRoutes', $callback, $priority);
    }

    /**
     * Registrar rotas publicas com inquilino definido.
     * @param callable $callback
     * @param int $priority
     */
    public function onPublicTenant(Closure $callback, $priority = 0)
    {
        event()->listen('Bugotech\Http\Events\PublicTenantRegisterRoutes', $callback, $priority);
    }

    /**
     * Registrar rotas privadas com inquilino definido.
     * @param callable $callback
     * @param int $priority
     */
    public function onPrivate(Closure $callback, $priority = 0)
    {
        event()->listen('Bugotech\Http\Events\PrivateRegisterRoutes', $callback, $priority);
    }

    /**
     * Registrar rotas API.
     * @param callable $callback
     * @param int $priority
     */
    public function onApi(Closure $callback, $priority = 0)
    {
        event()->listen('Bugotech\Http\Events\ApiRegisterRoutes', $callback, $priority);
    }
}