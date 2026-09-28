<?php

namespace app\admin\controller;

use app\admin\exception\AuthException;
use app\admin\model\AdminMenuModel;
use app\BaseController;
use HttpResponseException;
use support\View;

class Base extends BaseController
{
    protected $admin_id = 0;
    protected $admin_role = '';

    protected $is_super_admin = false;

    public function __construct(){
        parent::__construct();
        $this->admin_id = $this->request->session()->get('admin_id');
        $this->admin_role = $this->request->session()->get('admin_role');
        $this->assign('admin_rule', explode(',', $this->admin_role));
        $this->is_super_admin = $this->admin_role == '__ALL__';
        $this->assign('is_super_admin', $this->admin_role == '__ALL__');
    }

    protected function ajaxReturn($status, $msg, $data = [])
    {
        $res = [
            'status' => $status,
            'msg'    => $msg,
            'data'   => $data,
        ];
        return json($res);
    }

    /**
     * 成功响应
     */
    protected function success($msg = '操作成功', $data = [])
    {
        return $this->ajaxReturn(200, $msg, $data);
    }

    /**
     * 错误响应
     */
    protected function error($msg = '参数错误', $data = [])
    {
        return $this->ajaxReturn(201, $msg, $data);
    }

    /**
     * 分配变量到视图
     */
    protected function assign(...$vars)
    {
        View::assign(...$vars);
    }

    /**
     * 重定向
     */
    protected function redirect(...$args)
    {
        throw new HttpResponseException(redirect(...$args));
    }
}