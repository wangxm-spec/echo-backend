<?php

namespace app\api\service;

use app\api\exception\AuthException;
use app\common\exception\ParamException;
use app\common\model\MemberAccountModel;
use app\common\model\MemberCallModel;
use app\common\model\WorldMessageModel;

class WorldMessageService
{
    private const WORlD_MESSAGE = 'world.message';
    private const WORlD_NOTICE = 'world.notice';
    private const WORlD_MESSAGE_CANCEL = 'world.message.cancel';
    public static function world($message_type = 'text', $message_data = '', $channel = 0): void
    {
        $message = self::formatMessage($message_type, $message_data);
        GatewayWorkService::sendToAll([
            'type' => self::WORlD_MESSAGE,
            'data' => $message
        ]);
    }

    public static function channel($message_type = 'text', $message_data = '', $channel = 0, $uids = []): void
    {
        $message = self::formatMessage($message_type, $message_data);
        GatewayWorkService::sendToUid([
            'type' => self::WORlD_MESSAGE,
            'data' => $message
        ], $uids);
    }

    public static function cancel($message_id, $channel_id = 0){
        GatewayWorkService::sendToAll([
            'type' => self::WORlD_MESSAGE_CANCEL,
            'data' => [
                'message_id' => $message_id,
                'channel_id' => $channel_id
            ]
        ]);
    }

    private static function formatMessage($message_type = 'text', $message_data = '', $channel = 0): array
    {
        //这里要做信息敏感字过滤的
        $user = request()->user;
        if(!$user){
            throw new AuthException();
        }
        $user_role = $callname = '';
        $call_color = $nickname_color = $message_color = $time_color = '#000000';
        if($user['call_id']){
            $call = MemberCallModel::where('id', $user['call_id'])
                ->where('status', 1)
                ->where('uuid', $user['uuid'])
                ->field('title,color,role')
                ->find();
            if($call){
                $callname = $call['title']; $call_color = $call['color']; $user_role = $call['role'];
                if($call['role']){
                    $message_color = $nickname_color = $time_color = $call['color'];
                }
            }
        }
        //管理员权限
        if($user_role == 'admin'){

        }

        $now = formatDate();
        $msg = WorldMessageModel::create([
            'channel_id' => $channel,
            'uuid' => $user['uuid'],
            'uname' => $user['nickname'],
            'message_type' => $message_type,
            'message_data' => $message_data,
            'create_time' => $now,
            'update_time' => $now,
        ]);
        $message = [
            'msg_id' => $msg->id,
            'user' => [
                'id' => $user['uuid'],
                'nickname' => $user['nickname'],
                'callname' => $callname,
                'avatar' => formatUrl($user['avatar']),
            ],
            'msg' => [
                'type' => $message_type,
                'data' => $message_data
            ],
            'theme' => [
                'nickname' => $nickname_color,
                'callname' => $call_color,
                'message' => $message_color,
                'time' => $time_color,
            ],
            'time' => $now,
            'notice' => false
        ];
        return $message;
    }
}