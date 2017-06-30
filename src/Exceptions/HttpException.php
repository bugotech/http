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
     * @param Request $request
     * @param string $message
     * @param int $code
     * @param Exception $previous
     * @param array $attrs
     */
    public function __construct(Request $request, $message = '', $code = 0, Exception $previous = null, array $attrs = [])
    {
        parent::__construct($message, $code, $previous);

        $this->request = $request;
        $this->attrs = $attrs;
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
     * URL de volta.
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        return url()->previous();
    }
}