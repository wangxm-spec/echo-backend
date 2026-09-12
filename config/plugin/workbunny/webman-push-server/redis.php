<?php declare(strict_types=1);

return [
    // push server 储存器
    'server-storage' => [
        'host'     => env('REDIS_HOST'),
        'password' => env('REDIS_PASSWORD'),
        'port'     => env('REDIS_PORT'),
        'database' => env('REDIS_SELECT'),
    ],
    // 服务通讯频道
    'server-channel' => [
        'host'     => env('REDIS_HOST'),
        'password' => env('REDIS_PASSWORD'),
        'port'     => env('REDIS_PORT'),
        'database' => env('REDIS_SELECT'),
        'options'  => []
    ],
    // redis注册器配置
    'server-registrar' => [
        'host'     => env('REDIS_HOST'),
        'password' => env('REDIS_PASSWORD'),
        'port'     => env('REDIS_PORT'),
        'database' => env('REDIS_SELECT'),
        'options'  => []
    ]
];
