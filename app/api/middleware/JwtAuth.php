<?php

namespace app\api\middleware;
use app\api\exception\AuthException;
use ReflectionClass;
use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;
class JwtAuth implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $uuid = \Tinywan\Jwt\JwtToken::getCurrentId();
        if(!$uuid){
            throw new AuthException('请先登录');
        }

        $exp_time = \Tinywan\Jwt\JwtToken::getTokenExp();
        if($exp_time <= 0){
            throw new AuthException('请先登录');
        }

        $user = \Tinywan\Jwt\JwtToken::getUser();
        if(!$user || $user['status'] != 1){
            throw new AuthException('账号不存在或已禁用');
        }

        $client = \Tinywan\Jwt\JwtToken::getExtendVal('client');
        if(!in_array($client, ['mb','mp'])){
            throw new AuthException('请先登录');
        }
        if($client != $request->header('platform')){
            throw new AuthException('登录设备异常');
        }

        $request->uuid = $uuid;
        $request->user = $user;
        return $handler($request);
    }
}