<?php

namespace app\common\model;
class WorldChannelModel extends BaseModel
{
    protected $table = 'world_channel';

    public const status = [
        1 => '正常',
        0 => '禁用',
    ];
}