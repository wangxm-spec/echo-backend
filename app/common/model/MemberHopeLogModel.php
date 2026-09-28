<?php

namespace app\common\model;
class MemberHopeLogModel extends BaseModel
{
    protected $table = 'member_hope_log';

    public const type = [
        'recharge' => '充值',
        'consume'  => '消费',
        'admin'    => '管理员调整',
        'reward'   => '奖励',
        'refund'   => '退还',
    ];

    public const from = [
        'admin'  => '管理员',
        'member' => '会员',
        'system' => '系统',
    ];
}