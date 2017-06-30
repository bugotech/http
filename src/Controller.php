<?php namespace Bugotech\Http;

use Closure;
use Exception;
use Illuminate\Http\JsonResponse;
use Bugotech\Http\Exceptions\HttpException;
use Bugotech\Foundation\Support\Validator;
use Bugotech\Foundation\Support\ExceptionAttrs;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

abstract class Controller extends BaseController
{
    /**
     * Transacoes.
     *
     * @param callable $callback
     * @return JsonResponse|\Illuminate\Http\RedirectResponse|mixed
     */
    protected function transaction(Closure $callback)
    {
        db()->beginTransaction();
        try {
            $return = $callback();

            db()->commit();

            return $return;
        } catch (Exception $e) {
            db()->rollBack();

            return $this->exceptions($e);
        }
    }

    /**
     * @param Exception $e
     * @return \Illuminate\Http\RedirectResponse|JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    protected function exceptions(Exception $e)
    {
        if ((request()->ajax() && ! request()->pjax()) || request()->wantsJson()) {
            $error = new \stdClass();
            $error->code = $e->getCode();
            $error->message = $e->getMessage();

            return new JsonResponse($error, 422);
        }

        // Verificar se ja eh um Response
        if ($e instanceof BaseResponse) {
            return $e;
        }

        // Verificar se já eh um HttpException
        if (! ($e instanceof HttpException)) {
            $attrs = [];
            if ($e instanceof ExceptionAttrs) {
                $attrs = $e->getAttrs();
            }
            $e = new HttpException(request(), $e->getMessage(), $e->getCode(), $e->getPrevious(), $attrs);
        }

        return $e->getResponse();
    }

    /**
     * Validar valores pela regra.
     *
     * @param array $rules
     * @param array|null $values
     * @return bool
     */
    protected function validate(array $rules, $values = null)
    {
        // Se os valores forem nulos, buscar no request
        if (is_null($values)) {
            $values = request()->all();
        }

        // Validar valores pela regra
        return Validator::validate($values, $rules);
    }
}
