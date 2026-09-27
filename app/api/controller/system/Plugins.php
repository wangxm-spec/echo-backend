<?php

namespace app\api\controller\system;

use app\api\controller\Base;
use app\common\model\SystemPluginsModel;

class Plugins extends Base
{
    function list(){
        $page_limit = $this->request->param('page_limit', 10);
        $now_page = $this->request->param('now_page', 1);
        $list = SystemPluginsModel::where('status', 1)
            ->field('id,cate,title,desc,version,path,env_check,create_time,update_time')
            ->paginate(['list_rows' => $page_limit, 'page' => $now_page])
            ->toArray();
        return json(['rows' => $list['data'], 'total' => $list['total']]);
    }
}