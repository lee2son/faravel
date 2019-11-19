<?php

namespace Faravel\Illuminate\Foundation\Http;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

/**
 * Class ListenRequestHandledServiceProvider
 * @package Faravel\Illuminate\Foundation\Http
 * @see \Illuminate\Foundation\Http\Kernel::handle
 */
class ListenResponseHandledServiceProvider extends ServiceProvider
{
    /**
     * @var string log channel
     */
    public $log = "";

    /**
     * Bootstrap any application services.
     * @return void
     */
    public function boot()
    {
        Event::listen(RequestHandled::class, function(RequestHandled $event) {
            $this->listen($event);
        });
    }

    /**
     * Register any application services.
     * @return void
     */
    public function register()
    {

    }

    /**
     * 监听函数
     * @param QueryExecuted $query
     */
    protected function listen(RequestHandled $event)
    {
        list('message' => $message, 'data' => $data) = $this->format($event);
        Log::channel($this->log)->info($message, $data);
    }

    /**
     * 日志格式
     * @param QueryExecuted $query
     * @return string
     */
    protected function format(RequestHandled $event)
    {
        return [
            'message' => sprintf('%s %s %s', $event->request->method(), $event->request->url(), $event->response->getStatusCode()),
            'data' => [
                'content' => $event->response->getContent(),
                'headers' => $event->response->headers->all(),
                'requestId' => $event->request->header('X-Request-Id'),
                'traceId' => $event->request->header('X-Trace-Id'),
            ]
        ];
    }

    /**
     * 设置记录日志的channel
     * @param $channel
     */
    public function setLog($log)
    {
        $this->log = $log;
        return $this;
    }
}