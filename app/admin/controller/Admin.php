<?php

namespace app\admin\controller;

use app\admin\model\AdminRoleModel;
use app\admin\model\AdminUserModel;

class Admin extends Base
{
    /**
     * 管理员列表
     */
    public function index()
    {
        if (!$this->request->isAjax()) {
            $this->assign('role_list', AdminRoleModel::select());
            return view();
        }

        $limit  = $this->request->param('limit', 10);
        $offset = $this->request->param('offset', 0);
        $page   = floor($offset / $limit) + 1;

        $where[] = ['a.account', 'like', $this->request->param('keywords', '')];
        $where[] = ['a.status', '=', $this->request->param('status', '')];

        $order    = $this->request->param('order', '');
        $sort     = $this->request->param('sort', '');
        $orderStr = ($sort && $order) ? "a.{$sort} {$order}" : 'a.id desc';

        $res = AdminUserModel::alias('a')->where(formatWhere($where))
            ->join('admin_role b', 'b.delete_time is null and b.id = a.role_id', 'left')
            ->field('a.*,b.name role_name')
            ->order($orderStr)
            ->paginate(['list_rows' => $limit, 'page' => $page])
            ->toArray();

        foreach ($res['data'] as &$item) {
            $item['role_name'] = ((int)($item['is_super_admin'] ?? 0) === 1) ? '超级管理员' : '普通管理员';
        }
        return json(['rows' => $res['data'], 'total' => $res['total']]);
    }

    /**
     * 添加/编辑管理员
     */
    public function update()
    {
        $id = $this->request->param('id', 0);
        if (!$this->request->isPost()) {
            $info = AdminUserModel::find($id);
            $this->assign('id', $id);
            $this->assign('info', $info ?: new AdminUserModel());
            $this->assign('role_list', AdminRoleModel::select());
            return view();
        }
        $data = $this->request->param([
            'account'   => '',
            'password'  => '',
            'status'    => 1,
            'role_id'   => 0,
            'is_super_admin' => 0
        ]);
        if (empty($data['account'])) {
            return $this->error('请输入用户名');
        }

        if (AdminUserModel::where('account', $data['account'])->where('id', '<>', $id)->count() > 0) {
            return $this->error('用户名已存在');
        }
        if(!$this->is_super_admin){
            unset($data['is_super_admin']);
        }
        $now = date('Y-m-d H:i:s');
        if ($id > 0) {
            $data['update_time'] = $now;
            if(!empty($data['password'])){
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }else{
                unset($data['password']);
            }
            AdminUserModel::where('id', $id)->update($data);
        } else {
            if (empty($data['password'])) {
                return $this->error('请输入密码');
            }
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            $data['create_time'] = formatDate();
            AdminUserModel::create($data);
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
        AdminUserModel::where('id', $data['id'])->update([
            'status'      => $data['status'],
            'update_time' => date('Y-m-d H:i:s'),
        ]);
        return $this->success('操作成功');
    }

    /**
     * 删除管理员
     */
    public function delete()
    {
        $idx = $this->request->param('id');
        if (!$idx) {
            return $this->error('参数错误');
        }
        $ids = is_array($idx) ? $idx : explode(',', $idx);
        // 不能删除超级管理员
        $hasSuper = AdminUserModel::whereIn('id', $ids)->where('is_super_admin', 1)->count();
        if ($hasSuper > 0) {
            return $this->error('不能删除超级管理员');
        }
        AdminUserModel::destroy($ids);
        return $this->success('操作成功');
    }

    /**
     * 重置密码
     */
    public function password()
    {
        $id       = $this->request->param('id', 0);
        $password = $this->request->param('password', '');

        if (!$id) {
            return $this->error('参数错误');
        }
        if (empty($password)) {
            return $this->error('请输入新密码');
        }

        $admin = AdminUserModel::find($id);
        if (!$admin) {
            return $this->error('管理员不存在');
        }

        $admin->password    = password_hash($password, PASSWORD_DEFAULT);
        $admin->update_time = date('Y-m-d H:i:s');
        $admin->save();

        return $this->success('密码重置成功');
    }
}