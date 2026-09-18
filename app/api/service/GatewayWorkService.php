<?php

namespace app\api\service;

use GatewayWorker\Lib\Gateway;

class GatewayWorkService
{
    public static function sendToUid(array|object|string $data, string|array $uid = null): void
    {
        if($uid){
            Gateway::sendToUid($uid, is_string($data) ? $data : json_encode($data));
        }elseif($uid === null){
            self::sendToAll($data);
        }
    }

    public static function sendToAll(array|object|string $data): void
    {
        try {
            Gateway::sendToAll(is_string($data) ? $data : json_encode($data));
        } catch (\Exception $e) {

        }
    }
}