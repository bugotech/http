<?php namespace Bugotech\Http;

use Illuminate\Routing\Exceptions\UrlGenerationException;

class UrlGenerator extends \Illuminate\Routing\UrlGenerator
{
    /**
     * Lista de parametros do contexto compartilhado.
     * @var array
     */
    protected $parametersContext = [];

    /**
     * Get the URL for a given route instance.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @param  mixed  $parameters
     * @param  bool   $absolute
     * @return string
     *
     * @throws \Illuminate\Routing\Exceptions\UrlGenerationException
     */
    protected function toRoute($route, $parameters, $absolute)
    {
        $parameters = $this->formatParameters($parameters);

        $domain = $this->getRouteDomain($route, $parameters);

        // Verificar se ainda tem parametros par atraduzir
        $params_orign = $parameters;
        $parameters = [];
        preg_match_all('%\\{(.*?)\\}%', $route->uri(), $params, PREG_PATTERN_ORDER);
        for ($i = 0; $i < count($params[0]); $i++) {
            $key = str_replace('?', '', $params[1][$i]);
            $val = $this->getParameterContext($key);
            if (array_key_exists($key, $params_orign)) {
                $parameters[$key] = $params_orign[$key];
            } elseif (! is_null($val)) {
                $parameters[$key] = $val;
            }
        }

        $uri = $this->addQueryString($this->trimUrl(
            $root = $this->replaceRoot($route, $domain, $parameters),
            $this->replaceRouteParameters($route->uri(), $parameters)
        ), $parameters);

        // Se ainda tiver parametros gerar erro
        if (preg_match('/\{.*?\}/', $uri)) {
            throw UrlGenerationException::forMissingParameters($route);
        }

        $uri = strtr(rawurlencode($uri), $this->dontEncode);

        return $absolute ? $uri : '/' . ltrim(str_replace($root, '', $uri), '/');
    }

    /**
     * Compartilhar valor de parametro.
     * @param $name
     * @param $value
     */
    public function share($name, $value)
    {
        $this->parametersContext[$name] = $value;
    }

    /**
     * @param $name
     * @param null $default
     */
    public function getParameterContext($name, $default = null)
    {
        if (! array_key_exists($name, $this->parametersContext)) {
            return $default;
        }

        return $this->parametersContext[$name];
    }
}