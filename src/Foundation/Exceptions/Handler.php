<?php

namespace Faravel\Foundation\Exceptions;


use Exception;

class Handler extends \Illuminate\Foundation\Exceptions\Handler
{
    public function report(\Exception $e)
    {
        // TODO 记录日志、增加 trace_id
        return parent::report($e);
    }

    public function render($request, Exception $e)
    {
        return parent::render($request, $e);
    }
}