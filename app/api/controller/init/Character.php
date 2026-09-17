<?php

namespace app\api\controller\init;

use app\api\controller\Base;
use app\common\service\AIProvidersService;

/**
 * 角色卡生成（通过 AI 从材料中提取）
 * 材料类型：小说片段 / 聊天记录 / 生平描述
 */
class Character extends Base
{
    /**
     * 生成角色卡
     */
    public function build()
    {
        // 1. 参数校验
        $type = $this->request->param('type', 'common');
        if (!in_array($type, ['novel', 'chat', 'common'], true)) {
            return $this->error('参数错误：type 必须为 novel / chat / common');
        }

        $content = $this->request->param('content', '');
        if (empty($content)) {
            return $this->error('请提供 content');
        }

        // 2. 长度检查
        if (mb_strlen($content, 'UTF-8') > 8000) {
            return $this->error('内容过长（超过 8000 字符），请分段提交');
        }

        // 3. 加载提示词模板
        $templatePath = app_path() . '/common/template/prompt/build_character.txt';
        if (!is_file($templatePath)) {
            return $this->error('模板文件不存在');
        }

        $template = (string) file_get_contents($templatePath);

        // 4. 替换占位符
        $prompt = str_replace('{{MATERIAL_TYPE}}', $type, $template);
        $prompt = str_replace('{{MATERIAL_CONTENT}}', $content, $prompt);
        $prompt = str_replace('{{TIME}}', date('Y-m-d'), $prompt);

        // 5. 调用 AI
        try {
            $ai = new AIProvidersService('local');
            $rawResult = $ai->chat($prompt);
        } catch (\Exception $e) {
            return $this->error('AI 调用失败：' . $e->getMessage());
        }

        // 6. 解析 JSON
        $rawResult = trim($rawResult);
        $rawResult = preg_replace('/^```(?:json)?\s*/i', '', $rawResult);
        $rawResult = preg_replace('/\s*```$/', '', $rawResult);

        $character = json_decode($rawResult, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->error('AI 返回格式异常，请重试', $rawResult);
        }

        return $this->success('SUCCESS', $character);
    }
}