<?php

namespace app\common\model;
class MemberAccountModel extends BaseModel
{
    protected $table = 'member_account';
    protected $pk = 'uuid';

    public const status = [
        1 => '正常',
        0 => '禁用',
    ];
}