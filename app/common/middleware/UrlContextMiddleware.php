<?php

namespace app\common\middleware;

use support\Url;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class UrlContextMiddleware implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        // 多应用：$request->app 是应用名，如 'admin'
        if (!empty($request->app)) {
            Url::setApp($request->app);
        }

        // 控制器名
        if (!empty($request->controller)) {
            $short = preg_replace('/Controller$/i', '', substr(strrchr('\\' . $request->controller, '\\'), 1));
            Url::setCurrentController(strtolower($short));

            $request->short_controller = strtolower($short);
        }

        return $handler($request);
    }
}