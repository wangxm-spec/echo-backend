<?php

namespace app\common\service;

use RuntimeException;

class PromptService
{
    /**
     * 提示词模板目录
     */
    protected string $templateDir;

    /**
     * 默认占位值
     */
    protected string $defaultValue;

    /**
     * @param string|null $templateDir 模板目录，不传则使用默认目录
     * @param string $defaultValue 未传变量的默认值
     */
    public function __construct(?string $templateDir = null, string $defaultValue = '暂无')
    {
        $this->templateDir = rtrim(
            $templateDir ?: $this->getDefaultTemplateDir(),
            DIRECTORY_SEPARATOR
        );
        $this->defaultValue = $defaultValue;
    }

    /**
     * 默认模板目录
     * 你可以按项目实际结构调整
     */
    protected function getDefaultTemplateDir(): string
    {
        // 例如：项目根目录 / runtime / prompts
        return app_path() . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'prompts';
    }

    /**
     * 组装提示词
     *
     * @param string $fileName 模板文件名，例如 roleplay.txt
     * @param array $vars 变量键值对
     * @return string
     */
    public function build(string $fileName, array $vars = []): string
    {
        $filePath = $this->templateDir . DIRECTORY_SEPARATOR . $fileName;

        if (!is_file($filePath)) {
            throw new RuntimeException("提示词模板不存在: {$filePath}");
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new RuntimeException("读取提示词模板失败: {$filePath}");
        }

        return $this->render($content, $vars);
    }

    /**
     * 直接渲染字符串模板
     *
     * @param string $template
     * @param array $vars
     * @return string
     */
    public function render(string $template, array $vars = []): string
    {
        $result = preg_replace_callback(
            '/\{\{\s*([a-zA-Z0-9_\.\-]+)\s*\}\}/',
            function ($matches) use ($vars) {
                $key = $matches[1];

                // 先尝试完整 key 直接命中，支持 key 本身是数组/对象
                if (array_key_exists($key, $vars)) {
                    return $this->stringify($vars[$key]);
                }

                // 再尝试点号嵌套取值
                $value = $this->getNestedValue($vars, $key);
                if ($value !== null) {
                    return $this->stringify($value);
                }

                return $this->defaultValue;
            },
            $template
        );

        return $result ?? '';
    }

    /**
     * 支持点号嵌套取值，例如 character.name
     *
     * @param array $vars
     * @param string $path
     * @return mixed|null
     */
    protected function getNestedValue(array $vars, string $path)
    {
        if (strpos($path, '.') === false) {
            return null;
        }

        $segments = explode('.', $path);
        $current = $vars;

        foreach ($segments as $segment) {
            if (is_array($current) && array_key_exists($segment, $current)) {
                $current = $current[$segment];
            } elseif (is_object($current) && isset($current->$segment)) {
                $current = $current->$segment;
            } else {
                return null;
            }
        }

        return $current;
    }

    /**
     * 把值转成字符串
     * - 数组 / 对象：单行 JSON
     * - null：默认值
     * - bool：true / false
     * - 其他：字符串
     *
     * @param mixed $value
     * @return string
     */
    protected function stringify($value): string
    {
        if (is_null($value)) {
            return $this->defaultValue;
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_array($value) || is_object($value)) {
            $json = json_encode(
                $value,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );

            return $json === false ? $this->defaultValue : $json;
        }

        return (string) $value;
    }
}