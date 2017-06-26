<?php namespace Bugotech\Http;

class Cookie extends \Illuminate\Cookie\CookieJar
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Determine if a cookie exists on the request.
     *
     * @param  string  $key
     * @return bool
     */
    public function has($key)
    {
        return ! is_null($this->app['request']->cookie($key, null));
    }

    /**
     * Retrieve a cookie from the request.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return string
     */
    public function get($key = null, $default = null)
    {
        return $this->app['request']->cookie($key, $default);
    }
}
