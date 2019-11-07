<?php

namespace Faravel\Foundation\Exceptions;

use Exception;

class Handler extends \Illuminate\Foundation\Exceptions\Handler
{
    /**
     * 记录错误日志时也记录 requestId，需要 nginx 开启 $request_id 功能：
     * add_header X-Request-Id $request_id;
     * fastcgi_param REQUEST_ID $request_id;
     * @return array
     */
    protected function context()
    {
        $context = parent::context();

        if($requestId = request()->server(config('faravel.request_id'))) {
            $context['requestId'] = $requestId;
        }

        return $context;
    }
}