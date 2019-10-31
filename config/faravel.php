<?php
return [
    'listen_sql' => [
        'enable' => false,
        'log' => env('LOG_CHANNEL', 'stack')
    ],

    'listen_redis' => [
        'enable' => false,
        'log' => env('LOG_CHANNEL', 'stack'),
    ]
];