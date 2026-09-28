<?php

namespace app\common\model;
class SystemAppVersionModel extends BaseModel
{
    protected string $table = 'system_app_version';

    public const site = [
        'ad'  => '安卓',
        'ios' => '苹果手机',
        'mac' => '苹果电脑',
        'win' => '微软',
    ];

    public const status = [
        1 => '正常',
        0 => '禁用',
    ];

    public const is_must = [
        1 => '强制更新',
        0 => '非强制',
    ];
}