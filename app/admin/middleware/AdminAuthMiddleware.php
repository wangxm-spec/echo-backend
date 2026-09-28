<?php

namespace app\admin\middleware;

use app\admin\exception\AuthException;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class AdminAuthMiddleware implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        // 白名单 IP
        if (!in_array($request->ip(), explode(',', sys('admin_white_ip')))) {
            return redirect('/');
        }

        // 当前控制器/方法
        $controller = strtolower($request->short_controller) ?? '';   // 若框架注入了
        $adminId    = $request->session()->get('admin_id');

        // 非登录接口且未登录 → 跳登录
        if ($controller !== 'login' && !$adminId) {
            return redirect('/admin/login/index');
        }

        if($controller != 'login' ){
            $permissions = $request->session()->get('admin_role');
            if(!$permissions){
                throw new AuthException();
            }
            if($permissions != '__ALL__'){
                $url = 'admin/' . $controller . '/' . $request->action;
                if(!in_array($controller, ['upload', 'common'])){
                    if(!in_array($url, ['admin/index/index', 'admin/index/main', 'admin/index/password'])){
                        if(!in_array($url ,explode(',', $permissions))){
                            throw new AuthException();
                        }
                    }
                }
            }
        }
        $request->admin_id = $adminId;
        return $handler($request);
    }
}