<?php

namespace Faravel\Illuminate\Foundation\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Log
{
    const QUERY = 1;
    const REQUEST = 2;
    const HEADERS = 4;
    const COOKIES = 8;
    const SERVER = 16;
    const FILES = 32;
    const CONTENT = 64;

    /**
     * @param Request $request
     * @param \Closure $next
     * @param $log
     * @param $options
     */
    public function handle($request, \Closure $next, $log, $enable = 0B11111)
    {
        /**
         * @var Response $response
         */
        $response = $next($request);

        $data = [
            'requestId' => $request->header('X-Request-Id'),
            'traceId' => $request->header('X-Trace-Id'),
            'secure' => $request->isSecure(),
            'scheme' => $request->getScheme(),
            'response' => [
                'headers' => $response->headers->all(),
            ],
        ];

        $enable = intval($enable);

        if($enable & static::QUERY) {
            $data['query'] = $request->query->all();
        }

        if($enable & static::REQUEST) {
            $data['query'] = $request->request->all();
        }

        if($enable & static::HEADERS) {
            $data['query'] = $request->headers->all();
        }

        if($enable & static::COOKIES) {
            $data['query'] = $request->cookies->all();
        }

        if($enable & static::SERVER) {
            $data['query'] = $request->server->all();
        }

        if($enable & static::FILES) {
            $data['query'] = $request->files->all();
        }

        if($enable & static::CONTENT) {
            $data['response']['content'] = $response->getContent();
        }

        $message = sprintf("%s %s %s", $request->method(), $request->fullUrl(), $response->status());
        \Illuminate\Support\Facades\Log::channel($log)->info($message, $data);

        return $response;
    }
}