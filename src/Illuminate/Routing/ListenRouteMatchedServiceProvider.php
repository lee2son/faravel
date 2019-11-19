<?php

namespace Faravel\Illuminate\Routing;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class ListenRouteMatchedServiceProvider extends ServiceProvider
{
    /**
     * @var string log channel
     */
    public $log = "";

    /**
     * @var array 记录哪些数据
     * @see format
     */
    public $what = ['query', 'reqiest', 'headers'];

    /**
     * @var string 日志前缀
     */
    public $prefix = '';

    /**
     * Bootstrap any application services.
     * @return void
     */
    public function boot()
    {
        Route::matched(function (RouteMatched $event) {
            dd($event->route);
            $log = explode('|', $event->route->getAction('log', null));
            dd($log);
            foreach($log as $v) {
                if(Str::startsWith($v, 'request:')) {
                    $this->listen($event);
                }
            }
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
    protected function listen(RouteMatched $event)
    {
        list('message' => $message, 'data' => $data) = $this->format($event);
        Log::channel($this->log)->info($message, $data);
    }

    /**
     * 日志格式
     * @param QueryExecuted $query
     * @return string
     */
    protected function format(RouteMatched $event)
    {
        $request = $event->request;

        $data = [
            'secure' => $request->isSecure(),
            'scheme' => $request->getScheme(),
            'requestId' => $request->headers->get('X-Request-Id'),
            'traceId' => $request->headers->get('X-Trace-Id'),
        ];

        $dataKeys = array_intersect(['query', 'request', 'files', 'server', 'headers', 'cookies'], $this->what);
        foreach($dataKeys as $key)
        {
            $data[$key] = $request->{$key}->all();
        }

        return [
            'message' => sprintf('%s%s %s', $this->prefix, $request->method(), $request->url()),
            'data' => $data
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

    /**
     * 设置要记录哪些数据
     * @param array $what
     * @return $this
     */
    public function setWhat(array $what)
    {
        $this->what = $what;
        return $this;
    }

    /**
     * 设置要记录哪些数据
     * @param array $what
     * @return $this
     */
    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }
}