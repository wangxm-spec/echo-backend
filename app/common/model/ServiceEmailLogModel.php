<?php

namespace app\common\model;
class ServiceEmailLogModel extends BaseModel
{
    protected $table = 'service_email_log';

    public const type = [
        'bind'     => '邮箱绑定',
        'password' => '密码找回',
        'order'    => '订单通知',
    ];

    public const status = [
        1  => '已使用',
        0  => '未使用',
        -1 => '已失效',
    ];
}