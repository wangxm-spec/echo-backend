<?php

namespace app\common\model;
class MemberCallModel extends BaseModel
{
    protected $table = 'member_call';

    public const role = [
        'common' => '普通',
        'admin'  => '管理员',
        'viewer' => '观察者',
    ];

    public const status = [
        1 => '开启',
        0 => '关闭',
    ];
}