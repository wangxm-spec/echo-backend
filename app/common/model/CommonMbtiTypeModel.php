<?php

namespace app\common\model;
class CommonMbtiTypeModel extends BaseModel
{
    protected $table = 'common_mbti_type';
    protected $pk = 'code';

    public const group = [
        'NT' => '理性主义者',
        'NF' => '理想主义者',
        'SJ' => '守护者',
        'SP' => '探索者',
    ];

    public const status = [
        1 => '正常',
        0 => '隐藏',
    ];
}