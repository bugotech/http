<?php namespace Bugotech\Http;

use Closure;
use Exception;

abstract class ControllerMongo extends Controller
{
    /**
     * Transacoes.
     *
     * @param callable $callback
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    protected function transaction(Closure $callback)
    {
        try {
            $return = $callback();

            return $return;
        } catch (Exception $e) {
            return $this->exceptions($e);
        }
    }
}
