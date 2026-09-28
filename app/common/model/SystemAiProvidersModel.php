<?php

namespace app\common\model;
class SystemAiProvidersModel extends BaseModel
{
    protected $table = 'system_ai_providers';
    protected $json = ['config', 'models'];
    protected $jsonAssoc = true;

    public const type = [
        'ollama'   => 'Ollama',
        'openai'   => 'OpenAI兼容',
        'huoshan'  => '火山引擎',
        'deepseek' => 'DeepSeek',
    ];

    public const status = [
        1 => '正常',
        0 => '禁用',
    ];
}