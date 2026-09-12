<?php
return [
    'default' => [
        'host' => 'redis://' . env('REDIS_HOST') . ':' . env('REDIS_PORT'),
        'options' => [
            'auth' => env('REDIS_PASSWORD'),
            'db' => env('REDIS_SELECT'),
            'prefix' => '',
            'max_attempts'  => 5,
            'retry_seconds' => 5,
        ],
        // Connection pool, supports only Swoole or Swow drivers.
        'pool' => [
            'max_connections' => 5,
            'min_connections' => 1,
            'wait_timeout' => 3,
            'idle_timeout' => 60,
            'heartbeat_interval' => 50,
        ]
    ],
];
