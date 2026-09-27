<?php

namespace app\common\process;

use support\Url;

class Uri
{
    /**
     * 进程启动时执行
     */
    public static function start(): void
    {
        Url::init([
            'domain' => config('app.app_host', 'http://127.0.0.1:8787'),
            'root'   => '/',
            'suffix' => '',
        ]);
    }
}