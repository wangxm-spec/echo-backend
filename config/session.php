<?php

use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [

    'type' => 'redis',

    'handler' => RedisSessionHandler::class,

    'config' => [
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        'redis' => [
            'host'     => env('REDIS_HOST', '127.0.0.1'),
            'port'     => (int) env('REDIS_PORT', 6379),
            'auth'     => env('REDIS_PASSWORD', ''),
            'timeout'  => 2,
            'database' => (int) env('REDIS_SELECT', 0),
            'prefix'   => 'redis_session_',
        ],
        'redis_cluster' => [
            'host'    => ['127.0.0.1:7000', '127.0.0.1:7001', '127.0.0.1:7002'],
            'timeout' => 2,
            'auth'    => '',
            'prefix'  => 'redis_session_',
        ]
    ],
    'session_name' => 'PHPSID',
    'auto_update_timestamp' => false,
    'lifetime' => 24*60*60,
    'cookie_lifetime' => 365*24*60*60,
    'cookie_path' => '/',
    'domain' => '',
    'http_only' => true,
    'secure' => false,
    'same_site' => '',
    'gc_probability' => [1, 1000],
];