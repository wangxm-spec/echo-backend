<?php

namespace app\common\model;
class SystemAiProvidersModel extends BaseModel
{
    protected $table = 'system_ai_providers';
    protected $json = ['config'];
    protected $jsonAssoc = true;
}