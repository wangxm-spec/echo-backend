<?php

namespace app\api\controller;

use app\common\exception\ParamException;

class Base
{
    protected $request;
    protected $platform = 'mp';

    protected $uuid = '';
    public function __construct()
    {
        $this->request = request();
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
    protected function error($msg, $data = [])
    {
        return $this->ajaxReturn(201, $msg, $data);
    }
}