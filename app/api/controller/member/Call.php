<?php

namespace app\api\controller\member;

use app\api\controller\Base;
use app\common\model\MemberAccountModel;
use app\common\model\MemberCallModel;

class Call extends Base
{
    function list(){
        $list = MemberCallModel::where('uuid', $this->uuid)
            ->where('status', 1)
            ->column('id,title,color');
        return $this->success('SUCCESS', $list);
    }

    function set(){
        $id = $this->request->param('id');
        $call = MemberCallModel::where('id', $id)
            ->where('status', 1)
            ->where('uuid', $this->uuid)
            ->field('title,color,role')
            ->find();
        if(!$call){
            return $this->error('称号不存在');
        }
        MemberAccountModel::where('uuid', $this->uuid)
            ->update([
                'call_id' => $call['id']
            ]);
        return $this->success('设置成功');
    }
}