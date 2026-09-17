<?php

namespace app\api\controller\init;

use app\api\controller\Base;
use app\common\model\CommonMbtiTypeModel;
use app\common\model\InitMbtiQuestionModel;
use app\common\model\InitMbtiQuestionOptionModel;
use app\common\model\InitMbtiTestRecordModel;
use app\common\service\MbtiService;

class Mbti extends Base
{
    function type(){
        $list = CommonMbtiTypeModel::where('status', 1)
            ->column('code,name,group,description,traits,theme_color');
        return $this->success('SUCCESS', $list);
    }

    public function question()
    {
        $question_list = InitMbtiQuestionModel::with('optionsList')
            ->where('status', 1)
            ->order('sort asc')
            ->select();

        $result = [];
        foreach ($question_list as $question) {
            $result[] = [
                'id'        => $question->id,
                'content'   => $question->content,
                'dimension' => $question->dimension,
                'option'    => $question->optionsList->toArray(), // 直接就是目标格式
            ];
        }
        return $this->success('SUCCESS', $result);
    }

    function calculator(){
        $answer = $this->request->param('answer', []);
        $result = MbtiService::calculate($answer);
        InitMbtiTestRecordModel::create([
            'uuid' => $this->uuid,
            'result_type' => $result['type'],
            'scores' => $result['scores'],
            'status' => 1,
            'create_time' => formatDate(),
            'update_time' => formatDate(),
        ]);
        return $this->success('SUCCESS', $result);
    }
}