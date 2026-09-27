<?php

namespace app\api\controller;

use app\BaseController;
use app\common\exception\ParamException;
use app\common\exception\SystemException;
use app\common\model\SystemAppVersionModel;

class Base extends BaseController
{
    protected string $platform = 'mp';

    protected string $uuid = '';

    protected string $app_site = 'ad';
    protected int $app_version = 1;
    public function __construct()
    {
        parent::__construct();
        $this->platform = $this->request->header('platform');
        if(!in_array($this->platform, ['mp','mb'])){
            throw new ParamException('设备异常');
        }
        if(!empty($this->request->uuid)){
            $this->uuid = $this->request->uuid;
        }
        $this->initialize();
    }

    protected function initialize()
    {
        if(!sys('site_status')){
            throw new SystemException();
        }
        $this->app_site = $this->request->header('app_site', '');
        $this->app_version = $this->request->header('app_version', 1);
        if(!in_array($this->app_site, ['ad','ios','mac','win'])){
            throw new ParamException('设备异常');
        }
        //这里检查版本是否有强制更新
        $check = SystemAppVersionModel::where('site', $this->app_site)
            ->where('status', 1)
            ->where('version_id', '>', $this->app_version)
            ->where('is_must', 1)
            ->where('push_time', '<=', formatDate())
            ->order('version_id', 'desc')
            ->count();
        if($check > 0){
            throw new ParamException('有新版本更新', 886);
        }
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
}