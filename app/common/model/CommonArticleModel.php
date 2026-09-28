<?php

namespace app\common\model;
class CommonArticleModel extends BaseModel
{
    protected $table = 'common_article';

    public const position = [
        'start' => '启动页',
        'index_top' => '首页顶部',
        'index_tap' => '首页弹窗'
    ];
}