<?php

namespace app\api\controller\member;

use app\api\controller\Base;
use app\api\exception\AuthException;
use app\common\model\MemberAccountModel;
use app\common\model\MemberCharacterModel;
use app\common\model\MemberDeviceModel;

class Info extends Base
{
    function info(){
        $user = MemberAccountModel::where('status', 1)
            ->where('uuid', $this->uuid)
            ->field('uuid,account,avatar,email,nickname,qq_number,mbti_type,call_id,hope_amount,create_time')
            ->find();
        if(!$user){
            throw new AuthException();
        }
        $user['avatar'] = formatUrl($user['avatar']);
        $user['phone'] = substr($user['account'], 0, 3) . '****' . substr($user['account'], -4);
        return $this->success('SUCCESS', $user);
    }

    function device(){
        $device_list = MemberDeviceModel::where('uuid', $this->uuid)
            ->order('update_time desc')
            ->column('code,name,status,online_status,create_time,update_time');
        return $this->success('SUCCESS', $device_list);
    }

    function character(){
        $character_list = MemberCharacterModel::with('files')
            ->where('uuid', $this->uuid)
            ->field('cuid,name,birth,icon,description,personality,scenario,system_prompt,tone_tags,soul_card,rigidity,other_prompt,loader_call,current_coordinates,status,create_time')
            ->order('id desc')
            ->select();
        return $this->success('SUCCESS', $character_list);
    }
}