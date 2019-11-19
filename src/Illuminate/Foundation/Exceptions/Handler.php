<?php

namespace Faravel\Illuminate\Foundation\Exceptions;

use Exception;

class Handler extends \Illuminate\Foundation\Exceptions\Handler
{
    protected function context()
    {
        $context = parent::context();

        if($requestId = request()->header('X-Request-Id')) {
            $context['requestId'] = $requestId;
        }

        if($traceId = request()->header('X-Trace-Id')) {
            $context['traceId'] = $traceId;
        }

        return $context;
    }
}