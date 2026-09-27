<?php

namespace app\api\controller\system;

use app\api\controller\Base;
use app\common\model\SystemAppVersionModel;

class Version extends Base
{
    function info(){
        $info = SystemAppVersionModel::where('site', $this->app_site)
            ->where('status', 1)
            ->where('push_time', '<=', formatDate())
            ->field('id,title,desc,push_time')
            ->order('version_id', 'desc')
            ->find();
        return $this->success('SUCCESS', $info);
    }
}