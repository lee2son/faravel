<?php

namespace Faravel\Database\Eloquent;

use Illuminate\Support\Facades\Cache;

class Builder extends \Illuminate\Database\Eloquent\Builder
{
    public function firstFromCache($expiry = 3600)
    {
        $connection = $this->getModel()->getConnectionName();
        $table = $this->getModel()->getTable();
        $sql = sql($this->toSql(), $this->getBindings());

        $key = sprintf("%s:%s:%s", $connection, $table, md5($sql));

        return Cache::remember($key, $expiry, function() {
            return $this->first();
        });
    }
}