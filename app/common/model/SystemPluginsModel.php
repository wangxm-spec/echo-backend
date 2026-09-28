<?php

namespace app\common\model;
class SystemPluginsModel extends BaseModel
{
    protected string $table = 'system_plugins';
    protected $json = ['env_checker'];
    protected $jsonAssoc = true;

    public const status = [
        1  => '正常',
        0  => '禁用',
        -1 => '关闭',
    ];
}