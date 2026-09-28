<?php

namespace app\admin\controller\system;

use app\admin\controller\Base;
use app\common\model\SystemPluginsModel;

class Plugins extends Base
{
    /**
     * 插件列表
     */
    public function index()
    {
        if (!$this->request->isAjax()) {
            return view();
        }

        $limit  = $this->request->param('limit', 10);
        $offset = $this->request->param('offset', 0);
        $page   = floor($offset / $limit) + 1;

        $where[] = ['title|desc', 'like', $this->request->param('keywords', '')];
        $where[] = ['status', '=', $this->request->param('status', '')];

        $order    = $this->request->param('order', '');
        $sort     = $this->request->param('sort', '');
        $orderStr = ($sort && $order) ? "{$sort} {$order}" : 'id desc';

        $res = SystemPluginsModel::where(formatWhere($where))
            ->order($orderStr)
            ->paginate(['list_rows' => $limit, 'page' => $page])
            ->toArray();

        foreach ($res['data'] as &$item) {
            $item['status_text'] = SystemPluginsModel::status[$item['status']] ?? $item['status'];
        }
        unset($item);

        return json(['rows' => $res['data'], 'total' => $res['total']]);
    }

    /**
     * 新增/编辑插件
     */
    public function update()
    {
        $id = $this->request->param('id', 0);
        if (!$this->request->isPost()) {
            $info = SystemPluginsModel::find($id);
            $this->assign('id', $id);
            $this->assign('info', $info ?: new SystemPluginsModel());
            return view();
        }

        $data = $this->request->param([
            'cate'        => 0,
            'title'       => '',
            'desc'        => '',
            'version'     => '',
            'path'        => '',
            'status'      => 1,
            'env_checker' => '',
        ]);

        if (empty($data['title'])) {
            return $this->error('请输入插件标题');
        }

        $envChecker = [];
        if (!empty($data['env_checker'])) {
            $envChecker = json_decode($data['env_checker'], true);
            if (!is_array($envChecker)) {
                return $this->error('环境检查必须是合法的 JSON');
            }
        }
        $data['env_checker'] = $envChecker;

        $now = date('Y-m-d H:i:s');

        if ($id > 0) {
            $data['update_time'] = $now;
            SystemPluginsModel::where('id', $id)->update($data);
        } else {
            $data['create_time'] = $now;
            $data['update_time'] = $now;
            SystemPluginsModel::create($data);
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
        SystemPluginsModel::where('id', $data['id'])->update([
            'status'      => $data['status'],
            'update_time' => date('Y-m-d H:i:s'),
        ]);
        return $this->success('操作成功');
    }

    /**
     * 删除插件
     */
    public function delete()
    {
        $idx = $this->request->param('id');
        if (!$idx) {
            return $this->error('参数错误');
        }
        $ids = is_array($idx) ? $idx : explode(',', $idx);
        SystemPluginsModel::destroy($ids);
        return $this->success('操作成功');
    }
}
