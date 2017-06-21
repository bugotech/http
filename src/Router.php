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
}