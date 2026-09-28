<?php

namespace app\admin\controller;

use app\admin\model\AdminMenuModel;
use app\admin\model\AdminRoleModel;
use app\admin\model\AdminUserModel;

class Role extends Base
{
    /**
     * 角色列表
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

        $order    = $this->request->param('order', '');
        $sort     = $this->request->param('sort', '');
        $orderStr = ($sort && $order) ? "{$sort} {$order}" : 'id desc';

        $res = AdminRoleModel::where(formatWhere($where))
            ->order($orderStr)
            ->paginate(['list_rows' => $limit, 'page' => $page])
            ->toArray();
        return json(['rows' => $res['data'], 'total' => $res['total']]);
    }

    /**
     * 添加/编辑角色
     */
    public function update()
    {
        $id = $this->request->param('id', 0);
        if (!$this->request->isPost()) {
            $this->assign('id', $id);
            $info = AdminRoleModel::find($id);
            $this->assign('info', $info ?: new AdminRoleModel());
            return view();
        }

        $data = $this->request->param([
            'name'   => '',
            'desc' => '',
            'permissions' => '',
        ]);

        if (empty($data['name'])) {
            return $this->error('请输入角色名称');
        }

        // 名称唯一
        $where = [['name', '=', $data['name']]];
        if ($id > 0) {
            $where[] = ['id', '<>', $id];
        }
        if (AdminRoleModel::where($where)->count() > 0) {
            return $this->error('角色名称已存在');
        }
        $now = date('Y-m-d H:i:s');

        if ($id > 0) {
            $data['update_time'] = $now;
            AdminRoleModel::where('id', $id)->update($data);
        } else {
            $data['create_time'] = $now;
            $data['update_time'] = $now;
            AdminRoleModel::create($data);
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
        AdminRoleModel::where('id', $data['id'])->update([
            'status'      => $data['status'],
            'update_time' => date('Y-m-d H:i:s'),
        ]);
        return $this->success('操作成功');
    }

    /**
     * 删除角色
     */
    public function delete()
    {
        $idx = $this->request->param('id');
        if (!$idx) {
            return $this->error('参数错误');
        }
        $ids = is_array($idx) ? $idx : explode(',', $idx);

        // 检查是否被管理员绑定
        $bindCount = AdminUserModel::whereIn('role_id', $ids)->count();
        if ($bindCount > 0) {
            return $this->error('该角色已被管理员绑定，请先解除');
        }

        AdminRoleModel::destroy($ids);
        return $this->success('操作成功');
    }

    /**
     * 权限分配
     */
    public function auth()
    {
        if ($this->request->isAjax()) {
            $id  = $this->request->param('id', 0);
            $idx = $this->request->param('idx', []);

            if (!$id) {
                return $this->error('参数错误');
            }

            // 清理
            $list = [];
            foreach ($idx as $v) {
                if (is_string($v)) {
                    $s = trim($v);
                    if ($s !== '') $list[] = $s;
                } elseif (is_numeric($v)) {
                    $list[] = (string)$v;
                }
            }
            $idx = array_values(array_unique($list));

            $role = AdminRoleModel::find($id);
            if (!$role) {
                return $this->error('角色不存在');
            }
            //$menus = AdminMenuModel::where('id', 'in', $idx)->column('path');
            $role->permissions = implode(',', $idx);
            $role->save();

            return $this->success('权限保存成功');
        }

        $id = $this->request->param('id', 0);
        $this->assign('id', $id);

        $info = AdminRoleModel::find($id);
        $this->assign('info', $info);

        $nodes = $this->getJsTreeNodes(0, $info ? explode(',', $info->permissions) : []);
        $this->assign('nodes', json_encode($nodes, JSON_UNESCAPED_UNICODE));

        return view();
    }

    /**
     * 生成 jsTree 节点（含菜单和按钮）
     */
    private function getJsTreeNodes($pid, $permissions)
    {
        $list = AdminMenuModel::where('pid', $pid)
            ->order('type asc, sort asc, id asc')
            ->select()
            ->toArray();

        $permArr = is_array($permissions) ? $permissions : [];

        $result = [];
        foreach ($list as $key => $val) {
            $selectStatus = in_array((string)$val['path'], $permArr, true);

            $text = $val['title'] . ' (' . $val['path'] . ')';

            $result[$key]['text']   = $text;
            $result[$key]['state']  = ['opened' => true, 'selected' => $selectStatus];
            $result[$key]['a_attr'] = ['data-id' => (string)$val['path']];

            $children = $this->getJsTreeNodes($val['id'], $permissions);
            if ($children) {
                $result[$key]['children'] = $children;
            }
        }
        return array_values($result);
    }
}