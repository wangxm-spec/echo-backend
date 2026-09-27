<?php

namespace app\admin\controller;

use app\admin\model\AdminMenuModel;
use app\admin\model\AdminUserModel;
use app\common\model\MemberAccountModel;

class Index extends Base
{
    public function __construct(){
        parent::__construct();
    }

    function index(){
        $menus = $this->getMenus();
        $this->assign('menus', $menus);
        return view();
    }

    /**
     * 获取菜单（含权限过滤）
     */
    protected function getMenus()
    {
        $menuList = AdminMenuModel::where('type', 1)
            ->order('sort asc, id asc')
            ->select()
            ->toArray();

        // 字段映射：center_menu -> url/title/pid
        $list = [];
        foreach ($menuList as $m) {
            $list[] = [
                'id'    => $m['id'],
                'pid'   => $m['pid'],
                'title' => $m['title'],
                'url'   => $m['path'],
                'icon'  => $m['icon'],
                'sort'  => $m['sort'],
            ];
        }

        $menus = list_to_tree($list, 'id', 'pid', 'children', 0);

        // 超级管理员显示全部菜单
        if ($this->admin_role === '__ALL__') {
            return $menus;
        }

        $rules = explode(',', $this->admin_role);

        $filterMenu = function ($menus) use (&$filterMenu, $rules) {
            $result = [];
            foreach ($menus as $menu) {
                if (!empty($menu['children'])) {
                    $menu['children'] = $filterMenu($menu['children']);
                }
                if (!empty($menu['url'])) {
                    if (in_array($menu['url'], $rules)) {
                        $result[] = $menu;
                    }
                } elseif (!empty($menu['children'])) {
                    $result[] = $menu;
                }
            }
            return $result;
        };

        return $filterMenu($menus);
    }

    function main(){
        $this->assign([
            'member_count' => MemberAccountModel::count()
        ]);
        return view();
    }

    function password(){
        if (!$this->request->isPost()) {
            return view();
        }

        $old_password     = $this->request->post('old_password', '');
        $password         = $this->request->post('password', '');
        $password_confirm = $this->request->post('password_confirm', '');

        if (empty($password)) {
            return $this->error('请输入新密码');
        }
        if ($password != $password_confirm) {
            return $this->error('两次密码输入不一致');
        }

        $admin      = AdminUserModel::find($this->admin_id);
        $dbPassword = $admin->password ?? '';

        if(!password_verify($old_password, $dbPassword)){
            return $this->error('原密码错误');
        }

        $admin->password    = password_hash($password, PASSWORD_DEFAULT);
        $admin->update_time = date('Y-m-d H:i:s');
        $admin->save();

        $session = $this->request->session();
        $session->delete('admin_id');
        $session->delete('admin_role');

        return $this->success('密码修改成功，请重新登录');
    }
}