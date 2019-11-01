<?php
return [
    // 记录 sql 查询日志
    'listen_sql' => [
        'enable' => false,
        'log' => env('LOG_CHANNEL', 'stack')
    ],

    // 记录 redis 查询日志
    'listen_redis' => [
        'enable' => false,
        'log' => env('LOG_CHANNEL', 'stack'),
    ]
];