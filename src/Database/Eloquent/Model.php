<?php

namespace Faravel\Database\Eloquent;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Model extends Eloquent
{
    const CONNECTION = null;

    public function __construct(array $attributes = [])
    {
        if(static::CONNECTION) {
            $this->connection = static::CONNECTION;
        }

        $this->perPage = env('PAGE_SIZE', $this->perPage);

        parent::__construct($attributes);
    }
}