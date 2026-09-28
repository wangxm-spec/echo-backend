<?php

namespace app\admin\controller\system;

use app\admin\controller\Base;
use app\common\model\SystemAppVersionModel;

class Version extends Base
{
    /**
     * APP版本列表
     */
    public function index()
    {
        if (!$this->request->isAjax()) {
            $this->assign('site_list', SystemAppVersionModel::site);
            $this->assign('status_list', SystemAppVersionModel::status);
            $this->assign('is_must_list', SystemAppVersionModel::is_must);
            return view();
        }

        $limit  = $this->request->param('limit', 10);
        $offset = $this->request->param('offset', 0);
        $page   = floor($offset / $limit) + 1;

        $where[] = ['title|version_code', 'like', $this->request->param('keywords', '')];
        $where[] = ['site', '=', $this->request->param('site', '')];
        $where[] = ['status', '=', $this->request->param('status', '')];

        $order    = $this->request->param('order', '');
        $sort     = $this->request->param('sort', '');
        $orderStr = ($sort && $order) ? "{$sort} {$order}" : 'id desc';

        $res = SystemAppVersionModel::where(formatWhere($where))
            ->order($orderStr)
            ->paginate(['list_rows' => $limit, 'page' => $page])
            ->toArray();

        foreach ($res['data'] as &$item) {
            $item['site_text']    = SystemAppVersionModel::site[$item['site']] ?? $item['site'];
            $item['is_must_text'] = SystemAppVersionModel::is_must[$item['is_must']] ?? $item['is_must'];
        }
        unset($item);

        return json(['rows' => $res['data'], 'total' => $res['total']]);
    }

    /**
     * 新增/编辑版本
     */
    public function update()
    {
        $id = $this->request->param('id', 0);
        if (!$this->request->isPost()) {
            $info = SystemAppVersionModel::find($id);
            $this->assign('id', $id);
            $this->assign('info', $info ?: new SystemAppVersionModel());
            return view();
        }

        $data = $this->request->param([
            'version_id'   => 0,
            'version_code' => '',
            'site'         => 'win',
            'title'        => '',
            'desc'         => '',
            'status'       => 1,
            'push_time'    => '',
            'is_must'      => 0,
        ]);

        if (empty($data['version_code'])) {
            return $this->error('请输入版本号');
        }
        if (empty($data['title'])) {
            return $this->error('请输入标题');
        }
        if (empty($data['push_time'])) {
            return $this->error('请选择发布时间');
        }

        $now = date('Y-m-d H:i:s');

        if ($id > 0) {
            $data['update_time'] = $now;
            SystemAppVersionModel::where('id', $id)->update($data);
        } else {
            $data['create_time'] = $now;
            $data['update_time'] = $now;
            SystemAppVersionModel::create($data);
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
        SystemAppVersionModel::where('id', $data['id'])->update([
            'status'      => $data['status'],
            'update_time' => date('Y-m-d H:i:s'),
        ]);
        return $this->success('操作成功');
    }

    /**
     * 删除版本
     */
    public function delete()
    {
        $idx = $this->request->param('id');
        if (!$idx) {
            return $this->error('参数错误');
        }
        $ids = is_array($idx) ? $idx : explode(',', $idx);
        SystemAppVersionModel::destroy($ids);
        return $this->success('操作成功');
    }
}
