<?php namespace Bugotech\Http;

trait JsonHttp
{
    public function json($return)
    {
        $headers = [];
        $headers['Content-Type'] = 'application/json; charset=utf-8';
        $headers['Access-Control-Allow-Origin'] = '*';

        return response()->json($return, 200, $headers, JSON_PRETTY_PRINT);
    }
}