<?php

namespace Faravel\Illuminate\Database\Eloquent;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Model
 * @package Faravel\Database\Eloquent
 * @mixin Builder
 */
class Model extends Eloquent
{
    const CONNECTION = null;

    public function __construct(array $attributes = [])
    {
        if(static::CONNECTION) {
            $this->connection = static::CONNECTION;
        }

        $this->perPage = config('faravel.page_size', $this->perPage);

        parent::__construct($attributes);
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }
}