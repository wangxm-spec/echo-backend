<?php

namespace app\common\model;
class WorldMessageModel extends BaseModel
{
    protected $table = 'world_message';

    public const message_type = [
        'text'  => '文本',
        'image' => '图片',
        'emoji' => '表情',
    ];

    public const role = [
        'admin'  => '管理员',
        'viewer' => '观察者',
    ];
}