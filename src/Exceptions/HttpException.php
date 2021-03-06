<?php namespace Bugotech\Http\Exceptions;

use Exception;
use Illuminate\Http\Request;

class HttpException extends Exception
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var array
     */
    protected $attrs = [];

    /**
     * @var array
     */
    protected $attrsCustom = [];

    /**
     * @param Request $request
     * @param string $message
     * @param int $code
     * @param Exception $previous
     * @param array $attrs
     */
    public function __construct(Request $request, $message = '', $code = 0, Exception $previous = null, array $attrs = [], array $attrsCustom = [])
    {
        parent::__construct($message, $code, $previous);

        $this->request = $request;
        $this->attrs = $attrs;
        $this->attrsCustom = (count($attrsCustom) == 0) ? $attrs : $attrsCustom;
    }

    /**
     * Get the underlying response instance.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        $response = redirect()->to($this->getRedirectUrl());
        $response->withInput($this->request->input());
        $response->withErrors($this->getErrors(), 'default');
        $response->setCharset(config('app.charset', 'UTF-8'));

        return $response;
    }

    /**
     * Array de erros.
     *
     * @return array
     */
    protected function getErrors()
    {
        $arr = ['error' => $this->getMessage()];

        if (count($this->attrs) > 0) {
            $arr['attrs'] = $this->getAttrs();
        }

        if (count($this->attrsCustom) > 0) {
            $arr['attrscustom'] = $this->getAttrsCustom();
        }

        return $arr;
    }

    /**
     * Lista de erros por atributo.
     * @return array
     */
    public function getAttrs()
    {
        return $this->attrs;
    }

    /**
     * Lista de erros por atributo customizados.
     * @return array
     */
    public function getAttrsCustom()
    {
        return $this->attrsCustom;
    }

    /**
     * URL de volta.
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        return url()->previous();
    }
}