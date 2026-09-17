<?php

namespace plugin\webman\gateway;
use Tinywan\Jwt\JwtToken;
use GatewayWorker\Lib\Gateway;

class Events
{
    public static function onWorkerStart($worker)
    {
        //服务启动
    }

    public static function onConnect($client_id)
    {
        //客户端连接
    }



    public static function onWebSocketConnect($client_id, $data)
    {
        $authorization = $data['server']['HTTP_AUTHORIZATION'] ?? '';
        if (empty($authorization) || $authorization === 'undefined') {
            $config = config('plugin.tinywan.jwt.app'); // 读取插件配置
            if (!empty($config['jwt']['is_support_get_token'])) {
                $key = $config['jwt']['is_support_get_token_key'] ?? 'token';
                $authorization = trim($data['get'][$key] ?? '');
            }
        }
        if (empty($authorization)) {
            Gateway::closeClient($client_id);
            return;
        }
        if (stripos($authorization, 'Bearer ') === 0) {
            $authorization = substr($authorization, 7);
        }
        $ref = new \ReflectionClass(\Tinywan\Jwt\JwtToken::class);
        $accessToken = $ref->getConstant('ACCESS_TOKEN'); // 拿到 1
        try {
            $payload = \Tinywan\Jwt\JwtToken::verify($accessToken, $authorization);
        } catch (\Exception $e) {
            Gateway::closeClient($client_id);
            return;
        }
        // 3. 校验通过，绑定用户
        Gateway::bindUid($client_id, $payload['id']);
        Gateway::updateSession($client_id, ['uid' => $payload['id']]);
        Gateway::sendToClient($client_id, "绑定成功");
    }

    public static function onMessage($client_id, $message)
    {
        //客户端发消息
        Gateway::sendToClient($client_id, "receive message $message");
    }

    public static function onClose($client_id)
    {
        //客户端关闭
        $uid = $_SESSION['uid'] ?? null;
        if ($uid) {
            // 更新下线状态
        }
    }

}
