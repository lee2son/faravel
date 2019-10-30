<?php
return [
    'listen_sql' => [
        'enable' => false,
        'log' => env('LOG_CHANNEL', 'stack')
    ],

    'listen_redis' => [
        'default' => env('LOG_CHANNEL', 'stack'),
        'cache' => env('LOG_CHANNEL', 'stack'),
    ],
];