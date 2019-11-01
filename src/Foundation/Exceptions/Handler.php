<?php

namespace Faravel\Foundation\Exceptions;

use Exception;

class Handler extends \Illuminate\Foundation\Exceptions\Handler
{
    const REQUEST_ID = 'REQUEST_ID';

    protected function context()
    {
        $context = parent::context();

        if($requestId = request()->server(static::REQUEST_ID)) {
            $context['requestId'] = $requestId;
        }

        return $context;
    }
}