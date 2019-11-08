<?php
return [
    // $_SERVER['REQUEST_ID']
    // nginx: fastcgi_param REQUEST_ID $request_id;
    'request_id' => 'REQUEST_ID',

    // 记录 sql 查询日志
    'sql_log' => [
        'enable' => true,
        'log' => env('LOG_CHANNEL', 'stack'),
    ],

    // 记录 redis 查询日志
    'redis_log' => [
        'enable' => true,
        'log' => env('LOG_CHANNEL', 'stack'),
    ],

    // 每页显示的记录数
    // see Illuminate\Database\Eloquent\Model::paginate
    // see Illuminate\Database\Eloquent\Model::$perPage
    'page_size' => env('PAGE_SIZE'),

    // 创建数据库迁移时，varchar 默认长度
    // 如果使用utf8mb4字符集时，varchar 长度超过 191 会报错：SQLSTATE[42000]: Syntax error or access violation: 1071 Specified key was too long; max key length is 767 bytes
    // 通过报错信息计算可得知：767/4 = 191 (utf8mb4 占用 4 个字节)
    'default_string_length' => 191,

    // 日志配置
    'log_to_db' => [
        'connection' => 'mysql',
        'table' => 'sys_log',
    ],
];