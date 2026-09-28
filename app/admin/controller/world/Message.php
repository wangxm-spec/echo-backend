<?php

namespace app\admin\controller\world;

use app\admin\controller\Base;
use app\common\model\WorldChannelModel;
use app\common\model\WorldMessageModel;

class Message extends Base
{
    /**
     * 聊天信息
     */
    public function index()
    {
        if (!$this->request->isAjax()) {
            $this->assign('channel_list', WorldChannelModel::order('sort asc, id asc')->select());
            $this->assign('role_list', WorldMessageModel::role);
            return view();
        }

        $limit  = $this->request->param('limit', 10);
        $offset = $this->request->param('offset', 0);
        $page   = floor($offset / $limit) + 1;

        $where[] = ['a.channel_id', '=', $this->request->param('channel_id', '')];
        $where[] = ['a.uname|a.message_data', 'like', $this->request->param('keywords', '')];
        $where[] = ['a.message_type', '=', $this->request->param('message_type', '')];
        $where[] = ['a.role', '=', $this->request->param('role', '')];

        $order    = $this->request->param('order', '');
        $sort     = $this->request->param('sort', '');
        $orderStr = ($sort && $order) ? "a.{$sort} {$order}" : 'a.id desc';

        $res = WorldMessageModel::alias('a')
            ->join('world_channel b', 'b.delete_time is null and b.id = a.channel_id', 'left')
            ->field('a.*,b.name as channel_name')
            ->where(formatWhere($where))
            ->order($orderStr)
            ->paginate(['list_rows' => $limit, 'page' => $page])
            ->toArray();

        foreach ($res['data'] as &$item) {
            $item['channel_name']     = $item['channel_name'] ?: '世界频道';
            $item['message_type_text'] = WorldMessageModel::message_type[$item['message_type']] ?? $item['message_type'];
            $item['role_text']        = WorldMessageModel::role[$item['role']] ?? ($item['role'] ?: '-');
        }
        unset($item);

        return json(['rows' => $res['data'], 'total' => $res['total']]);
    }

    /**
     * 删除聊天信息
     */
    public function delete()
    {
        $idx = $this->request->param('id');
        if (!$idx) {
            return $this->error('参数错误');
        }
        $ids = is_array($idx) ? $idx : explode(',', $idx);
        WorldMessageModel::destroy($ids);
        return $this->success('操作成功');
    }
}
