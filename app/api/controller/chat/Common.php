<?php

namespace app\api\controller\chat;

use app\api\controller\Base;
use app\common\model\MemberCharacterModel;
use app\common\service\PromptService;

/**
 * 日常交流
 */
class Common extends Base
{
    function chat(){
        $cuid = $this->request->param('cuid');
        $message_type = $this->request->param('message_type');
        $message_data = $this->request->param('message_data');
        if(!in_array($message_type, ['text', 'image', 'emoji'])){
            return $this->error('消息类型异常');
        }
        if($message_type == 'text' && mb_strlen($message_data) > 2000){
            return $this->error('输入文字最多2000字');
        }
        $character = MemberCharacterModel::where('status', 1)
            ->where('cuid', $cuid)
            ->where('uuid', $this->uuid)
            ->find();
        if(!$character){
            return $this->error('角色卡不存在');
        }
        //组装提示词
        $prompt = (new PromptService())->build('chat_common.txt', [
            
        ]);
        //组装聊天记录
        //执行交互
        //推送其他设备
    }
}