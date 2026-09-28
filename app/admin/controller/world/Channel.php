<?php

namespace app\admin\controller\world;

use app\admin\controller\Base;
use app\common\model\WorldChannelModel;

class Channel extends Base
{
    /**
     * 频道列表
     */
    public function index()
    {
        if (!$this->request->isAjax()) {
            return view();
        }

        $limit  = $this->request->param('limit', 10);
        $offset = $this->request->param('offset', 0);
        $page   = floor($offset / $limit) + 1;

        $where[] = ['name|desc', 'like', $this->request->param('keywords', '')];
        $where[] = ['status', '=', $this->request->param('status', '')];

        $order    = $this->request->param('order', '');
        $sort     = $this->request->param('sort', '');
        $orderStr = ($sort && $order) ? "{$sort} {$order}" : 'sort asc, id asc';

        $res = WorldChannelModel::where(formatWhere($where))
            ->order($orderStr)
            ->paginate(['list_rows' => $limit, 'page' => $page])
            ->toArray();

        return json(['rows' => $res['data'], 'total' => $res['total']]);
    }

    /**
     * 新增/编辑频道
     */
    public function update()
    {
        $id = $this->request->param('id', 0);
        if (!$this->request->isPost()) {
            $info = WorldChannelModel::find($id);
            $this->assign('id', $id);
            $this->assign('info', $info ?: new WorldChannelModel());
            return view();
        }

        $data = $this->request->param([
            'name'   => '',
            'desc'   => '',
            'sort'   => 50,
            'status' => 1,
        ]);

        if (empty($data['name'])) {
            return $this->error('请输入频道名称');
        }

        $now = date('Y-m-d H:i:s');

        if ($id > 0) {
            $data['update_time'] = $now;
            WorldChannelModel::where('id', $id)->update($data);
        } else {
            $data['create_time'] = $now;
            $data['update_time'] = $now;
            WorldChannelModel::create($data);
        }

        return $this->success('提交成功');
    }

    /**
     * 修改状态
     */
    public function status()
    {
        $data = $this->request->param(['id', 'status']);
        if (!$data['id']) {
            return $this->error('参数错误');
        }
        WorldChannelModel::where('id', $data['id'])->update([
            'status'      => $data['status'],
            'update_time' => date('Y-m-d H:i:s'),
        ]);
        return $this->success('操作成功');
    }

    /**
     * 删除频道
     */
    public function delete()
    {
        $idx = $this->request->param('id');
        if (!$idx) {
            return $this->error('参数错误');
        }
        $ids = is_array($idx) ? $idx : explode(',', $idx);
        WorldChannelModel::destroy($ids);
        return $this->success('操作成功');
    }
}
