<?php

namespace app\common\model;
class InitMbtiQuestionModel extends BaseModel
{
    protected $table = 'init_mbti_question';

    public function optionsList()
    {
        return $this->hasMany(InitMbtiQuestionOptionModel::class, 'question_id', 'id')
            ->field('id,question_id,content,score_value as score') // 直接别名
            ->where('status', 1)
            ->order('sort asc');
    }
}