<?php

namespace app\common\model;
class ServiceSmsLogModel extends BaseModel
{
    protected $table = 'service_sms_log';

    public const type = [
        'register' => '注册',
        'login'    => '登录',
        'reset'    => '重置密码',
        'bind'     => '绑定手机',
    ];

    public const status = [
        1  => '已使用',
        0  => '未使用',
        -1 => '已失效',
    ];
}