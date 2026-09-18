<?php

namespace app\api\controller\common;

use app\api\controller\Base;
use app\common\model\CommonArticleModel;

class Article extends Base
{
    function list(){
        $position = $this->request->param('position');
        $list = CommonArticleModel::where('position', $position)
            ->where('status', 1)
            ->where('publish_time', '<=', formatDate())
            ->column('id,title,keyword,create_time,update_time');
        return $this->success('SUCCESS', $list);
    }

    function detail(){
        $id = $this->request->param('id');
        $info = CommonArticleModel::where('id', $id)
            ->where('status', 1)
            ->where('publish_time', '<=', formatDate())
            ->field('id,title,keyword,detail,create_time,update_time')
            ->find();
        return $this->success('SUCCESS', $info);
    }
}