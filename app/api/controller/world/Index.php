<?php

namespace app\api\controller\world;

use app\api\controller\Base;
use app\api\service\WorldMessageService;
use app\common\model\MemberCallModel;
use app\common\model\WorldMessageModel;

class Index extends Base
{
    function message(){
        $message_type = $this->request->param('message_type', 'text');
        $message_data = $this->request->param('message_data', '');
        if(!$message_data || !in_array($message_type, ['text', 'image', 'emoji'])){
            return $this->success('参数错误');
        }
        WorldMessageService::world($message_type, $message_data);
        return $this->success('发送成功');
    }

    function cancel(){
        $message_id = $this->request->param('message_id');
        $user = $this->request->user;
        $message_data = WorldMessageModel::where('id', $message_id)
            ->find();
        if(!$message_data){
            return $this->success('消息不存在');
        }
        //检查权限
        if($user['call_id']){
            $call = MemberCallModel::where('id', $user['call_id'])
                ->where('status', 1)
                ->where('uuid', $user['uuid'])
                ->field('title,color,role')
                ->find();
            if($call && in_array($call['role'], ['admin', 'viewer'])){
                WorldMessageService::cancel($message_id, $message_data['channel_id']);
                return $this->success('SUCCESS');
            }
        }
        if($message_data['uuid'] != $this->uuid){
            return $this->success('仅能撤回自己的消息');
        }
        if(time() - strtotime($message_data['create_time']) > 3000){
            return $this->success('仅能撤回5分钟内的消息');
        }
        WorldMessageService::cancel($message_id, $message_data['channel_id']);
        return $this->success('SUCCESS');
    }
}