<?php

namespace Faravel\Database\Eloquent;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Model extends Eloquent
{
    const CONNECTION = null;
    const TABLE = null;
    const PRIMARY_KEY = null;

    public function __construct(array $attributes = [])
    {
        $this->connection = static::CONNECTION;
        $this->table = static::TABLE;
        $this->primaryKey = static::PRIMARY_KEY;

        parent::__construct($attributes);
    }
}