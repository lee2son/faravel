<?php

namespace Faravel\Database\Eloquent;

use Illuminate\Support\Facades\Cache;

class Builder extends \Illuminate\Database\Eloquent\Builder
{
    /**
     * 从缓存获取数据
     * @param int $expiry 缓存过期时间
     * @return mixed
     */
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