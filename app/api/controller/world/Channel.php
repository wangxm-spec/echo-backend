<?php

namespace app\api\controller\world;

use app\api\controller\Base;
use app\api\service\GatewayWorkService;
use app\api\service\WorldMessageService;
use app\common\model\WorldChannelModel;
use support\Redis;

class Channel extends Base
{
    private const REDIS_CHANNEL_TAG      = 'WORLD:CHANNEL:';       // channel_id => uuids
    private const REDIS_USER_CHANNEL_TAG = 'WORLD:USER:CHANNEL:';  // uuid => channel_ids
    /**
     * 全部频道列表（不涉及加入状态）
     */
    public function list()
    {
        $list = WorldChannelModel::where('status', 1)
            ->order('sort asc,id desc')
            ->column('id,name,desc');
        return $this->success('SUCCESS', $list);
    }

    /**
     * 我加入的频道列表（纯读，一次 Redis 调用）
     */
    public function mine()
    {
        $joinedIds = Redis::sMembers(self::REDIS_USER_CHANNEL_TAG . $this->uuid);
        if (empty($joinedIds)) {
            return $this->success('SUCCESS', []);
        }
        $list = WorldChannelModel::where('status', 1)
            ->whereIn('id', $joinedIds)
            ->order('sort asc,id desc')
            ->column('id,name,desc');
        return $this->success('SUCCESS', $list);
    }

    /**
     * 加入频道
     */
    public function bind()
    {
        $channelId = (int) $this->request->param('channel');
        if ($channelId <= 0) {
            return $this->error('参数错误');
        }
        $channel = WorldChannelModel::where('status', 1)->where('id', $channelId)->find();
        if(!$channel){
            return $this->error('频道不存在');
        }
        if($channel['status'] != 1){
            return $this->error('频道已关闭');
        }
        Redis::sAdd(self::REDIS_CHANNEL_TAG . $channelId, $this->uuid);
        Redis::sAdd(self::REDIS_USER_CHANNEL_TAG . $this->uuid, $channelId);
        return $this->success('加入成功');
    }

    /**
     * 退出频道
     */
    public function unbind()
    {
        $channelId = (int) $this->request->param('channel');
        if ($channelId <= 0) {
            return $this->error('参数错误');
        }
        $channel = WorldChannelModel::where('status', 1)->where('id', $channelId)->find();
        if(!$channel){
            return $this->error('频道不存在');
        }
        if($channel['status'] != 1){
            return $this->error('频道已关闭');
        }
        Redis::sRem(self::REDIS_CHANNEL_TAG . $channelId, $this->uuid);
        Redis::sRem(self::REDIS_USER_CHANNEL_TAG . $this->uuid, $channelId);
        return $this->success('退出成功');
    }

    /**
     * 发送频道消息
     */
    public function message()
    {
        $channelId = (int) $this->request->param('channel');
        $message_type = $this->request->param('message_type', 'text');
        $message_data = $this->request->param('message_data', '');
        if(!$message_data || !in_array($message_type, ['text', 'image', 'emoji'])){
            return $this->success('参数错误');
        }
        if ($channelId <= 0) {
            return $this->success('参数错误');
        }
        $channel = WorldChannelModel::where('status', 1)->where('id', $channelId)->find();
        if(!$channel){
            return $this->error('频道不存在');
        }
        if($channel['status'] != 1){
            return $this->error('频道已关闭');
        }
        // 取该频道所有成员 uid，用 sendToUid 广播
        $uids = Redis::sMembers(self::REDIS_CHANNEL_TAG . $channelId);
        if (empty($uids)) {
            return $this->success('当前无人在线');
        }
        WorldMessageService::channel($message_type, $message_data, $channelId, $uids);
        return $this->success('发送成功');
    }
}