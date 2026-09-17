<?php

namespace app\common\service;

use app\common\model\SystemAiProvidersModel;
use Marwa\AI\Application;
use RuntimeException;

class AIProvidersService
{
    /**
     * 当前使用的供应商 key
     */
    protected string $providerKey;

    /**
     * 供应商数据库记录
     */
    protected object $provider;

    /**
     * 解析后的 config
     */
    protected array $config = [];

    /**
     * MarwaAI Application 实例
     */
    protected Application $app;

    /**
     * 默认模型（可被 withModel 覆盖）
     */
    protected ?string $model = null;

    /**
     * 历史消息
     * @var array<int, array{role: string, content: string}>
     */
    protected array $history = [];

    /**
     * 构造函数：传入供应商 key，自动加载配置并初始化
     *
     * @param string $providerKey 数据库 system_ai_providers.key 字段值
     * @throws RuntimeException
     */
    public function __construct(string $providerKey)
    {
        $this->providerKey = $providerKey;
        $this->loadProvider();
        $this->bootApplication();
    }

    /**
     * 从数据库加载供应商配置
     *
     * @throws RuntimeException
     */
    protected function loadProvider(): void
    {
        $provider = SystemAiProvidersModel::where('key', $this->providerKey)
            ->where('status', 1)
            ->find();

        if (!$provider) {
            throw new RuntimeException("AI 供应商 [{$this->providerKey}] 不存在或已禁用");
        }

        $config = $provider->config;
        if (!is_array($config)) {
            throw new RuntimeException("AI 供应商 [{$this->providerKey}] 的 config 配置格式错误");
        }

        $this->provider = $provider;
        $this->config   = $config;
        $this->model    = $provider->default_model ?: null;
    }

    /**
     * 初始化 MarwaAI Application
     */
    protected function bootApplication(): void
    {
        $marwaProvider = $this->mapProviderType($this->provider->type);

        $aiConfig = [
            'default' => $marwaProvider,
            $marwaProvider => [
                'api_key'  => $this->config['api_key'] ?? '',
                'base_url' => $this->config['base_url'] ?? null,
                'model'    => $this->model,
                'timeout'  => $this->config['timeout'] ?? 30,
                'retries'  => $this->config['retries'] ?? 3,
            ],
        ];

        $this->app = new Application($aiConfig);
    }

    /**
     * 设置本次调用使用的模型
     *
     * @param string $model
     * @return $this
     */
    public function withModel(string $model): static
    {
        $this->model = $model;
        return $this;
    }

    /**
     * 设置历史消息
     *
     * @param array $history 形如 [['role'=>'user','content'=>'...'], ...]
     * @return $this
     */
    public function withHistory(array $history): static
    {
        $this->history = $history;
        return $this;
    }

    /**
     * 发送对话，获取回复文本
     *
     * @param string $prompt 用户输入
     * @param array  $options 额外参数（temperature、max_tokens 等）
     * @return string
     */
    public function chat(string $prompt, array $options = []): string
    {
        $conversation = $this->buildConversation($prompt);

        $response = $conversation->send($options);

        $this->updateTokenUsage($response);

        return $response->getContent();
    }

    /**
     * 发送对话，强制返回 JSON 数组
     *
     * @param string $prompt 用户输入
     * @param array  $options 额外参数
     * @return array
     * @throws RuntimeException
     */
    public function chatJson(string $prompt, array $options = []): array
    {
        // 沿用 chat() 的调用链，保证走的是当前供应商配置
        $reply = $this->chat($prompt, $options);

        // 清理可能包裹的 markdown 代码块
        $reply = trim($reply);
        $reply = preg_replace('/^```(?:json)?\s*/i', '', $reply);
        $reply = preg_replace('/\s*```$/', '', $reply);
        $data = json_decode($reply, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('AI 返回内容不是合法 JSON：' . $reply);
        }

        return $data;
    }

    /**
     * 获取原始响应对象（需要更多字段时使用）
     *
     * @param string $prompt
     * @param array  $options
     * @return mixed
     */
    public function raw(string $prompt, array $options = [])
    {
        $conversation = $this->buildConversation($prompt);

        $response = $conversation->send($options);

        $this->updateTokenUsage($response);

        return $response;
    }

    /**
     * 构建 Conversation 对象（内部统一处理历史记录和模型）
     *
     * @param string $prompt
     * @return mixed
     */
    protected function buildConversation(string $prompt)
    {
        // 有历史时拼成完整 messages 数组传入
        if (!empty($this->history)) {
            $messages   = $this->history;
            $messages[] = ['role' => 'user', 'content' => $prompt];
            $conversation = $this->app->conversation($messages);
        } else {
            $conversation = $this->app->conversation($prompt);
        }

        if ($this->model !== null && method_exists($conversation, 'withModel')) {
            $conversation = $conversation->withModel($this->model);
        }

        return $conversation;
    }

    /**
     * 获取当前供应商的默认模型
     */
    public function getDefaultModel(): string
    {
        return $this->provider->default_model;
    }

    /**
     * 获取当前供应商的所有可用模型列表
     */
    public function getModels(): array
    {
        $models = $this->provider->models;

        if (is_string($models)) {
            $models = json_decode($models, true) ?: [];
        }

        return is_array($models) ? $models : [];
    }

    /**
     * 获取当前供应商的原始记录
     */
    public function getProvider(): object
    {
        return $this->provider;
    }

    /**
     * 类型映射为 MarwaAI 支持的 provider key
     */
    protected function mapProviderType(string $type): string
    {
        $map = [
            'openai'    => 'openai',
            'deepseek'  => 'deepseek',
            'ollama'    => 'ollama',
            'huoshan'   => 'openai',
            'anthropic' => 'anthropic',
            'google'    => 'google',
            'xai'       => 'xai',
            'mistral'   => 'mistral',
        ];

        return $map[$type] ?? $type;
    }

    /**
     * 累加 token 用量
     */
    protected function updateTokenUsage($response): void
    {
        if (!is_object($response) || !method_exists($response, 'getUsage')) {
            return;
        }

        $usage = $response->getUsage();
        if (!is_object($usage)) {
            return;
        }

        // 依次尝试常见的方法名和属性名
        $total = null;

        if (method_exists($usage, 'getTotalTokens')) {
            $total = $usage->getTotalTokens();
        } elseif (method_exists($usage, 'totalTokens')) {
            $total = $usage->totalTokens();
        } elseif (property_exists($usage, 'totalTokens')) {
            $total = $usage->totalTokens;
        } elseif (property_exists($usage, 'total_tokens')) {
            $total = $usage->total_tokens;
        }

        if ($total === null || $total <= 0) {
            return;
        }

        SystemAiProvidersModel::where('key', $this->providerKey)
            ->inc('total_token_used', (int) $total)
            ->update();
    }
}