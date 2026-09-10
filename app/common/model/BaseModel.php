<?php

namespace app\common\model;
use support\think\Model;
use think\model\concern\SoftDelete;
class BaseModel extends Model
{
    use SoftDelete;
    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $deleteTime = 'delete_time';
}