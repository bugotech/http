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
     * @param Request $request
     * @param string $message
     * @param int $code
     * @param Exception $previous
     */
    public function __construct(Request $request, $message = '', $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->request = $request;
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
        return ['error' => $this->getMessage()];
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