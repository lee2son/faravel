<?php

/**
 * 获取当前时间的毫秒数
 * @return int
 */
function millitime()
{
    return intval(microtime(true) * 1000);
}

/**
 * @param string $sql
 * @param array $bindings
 * @return string
 */
function sql($sql, $bindings) : string
{
    foreach ($bindings as $i => $binding) {
        if ($binding instanceof \DateTime) {
            $bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
        } else if (is_string($binding)) {
            $bindings[$i] = "'{$binding}'";
        }
    }

    $query = str_replace(array('%', '?'), array('%%', '%s'), $sql);

    return vsprintf($query, $bindings);
}