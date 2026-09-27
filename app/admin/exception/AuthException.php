<?php

namespace app\admin\exception;

use Webman\Http\Request;
use Webman\Http\Response;

class AuthException extends BaseException
{
    /**
     * @var int 403 无权操作
     */
    protected $code = 403;

    /**
     * @var string
     */
    protected $message = '您没有权限执行此操作';

    /**
     * 附加提示（页面上第二行小字，可选）
     */
    protected string $tip = '';

    public function __construct(string $message = '', int $code = 0, string $tip = '')
    {
        parent::__construct($message, $code);
        if ($tip !== '') {
            $this->tip = $tip;
        }
    }

    public function getTip(): string
    {
        return $this->tip;
    }

    /**
     * 渲染响应：Ajax 走 JSON，浏览器走 HTML 403 页面
     * 由全局 ExceptionHandler 调用
     */
    public function renderResponse(Request $request): Response
    {
        if ($this->wantsJson($request)) {
            return json([
                'status' => $this->getCode(),
                'msg'    => $this->getMessage(),
                'data'   => '',
            ]);
        }

        return response($this->buildHtml(), 403)
            ->withHeader('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     * 是否期望 JSON
     */
    protected function wantsJson(Request $request): bool
    {
        $accept = $request->header('accept', '');
        if (stripos($accept, 'application/json') !== false) {
            return true;
        }
        if ($request->header('x-requested-with') === 'XMLHttpRequest') {
            return true;
        }
        return str_starts_with($request->path(), '/api');
    }

    /**
     * 生成 403 HTML
     */
    protected function buildHtml(): string
    {
        $message = htmlspecialchars($this->getMessage(), ENT_QUOTES, 'UTF-8');
        $tip     = htmlspecialchars($this->tip, ENT_QUOTES, 'UTF-8');
        $tipHtml = $tip ? '<p class="tip">' . $tip . '</p>' : '';
        $homeUrl = '/';

        return <<<HTML
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>无权操作</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "PingFang SC",
                     "Hiragino Sans GB", "Microsoft YaHei", sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        display: flex; align-items: center; justify-content: center;
        padding: 24px; color: #333;
    }
    .card {
        background: #fff; border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, .25);
        max-width: 520px; width: 100%;
        padding: 48px 40px; text-align: center;
        animation: fadeIn .4s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .icon {
        width: 96px; height: 96px; margin: 0 auto 24px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ff6b6b, #ee5a24);
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 8px 24px rgba(238, 90, 36, .35);
    }
    .icon svg { width: 52px; height: 52px; fill: #fff; }
    .code {
        font-size: 64px; font-weight: 800; line-height: 1;
        background: linear-gradient(135deg, #ff6b6b, #ee5a24);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 8px; letter-spacing: 2px;
    }
    .title { font-size: 22px; font-weight: 600; color: #222; margin-bottom: 12px; }
    .message { font-size: 15px; color: #666; line-height: 1.7; margin-bottom: 8px; }
    .tip { font-size: 13px; color: #999; line-height: 1.6; margin-bottom: 24px; }
    .actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; margin-top: 24px; }
    .btn {
        display: inline-block; padding: 11px 28px; font-size: 14px;
        border-radius: 8px; text-decoration: none; cursor: pointer;
        border: none; transition: all .2s; font-weight: 500;
    }
    .btn-primary {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: #fff; box-shadow: 0 4px 14px rgba(102, 126, 234, .4);
    }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(102, 126, 234, .55); }
    .btn-default { background: #f1f3f5; color: #555; }
    .btn-default:hover { background: #e9ecef; transform: translateY(-2px); }
    @media (max-width: 480px) {
        .card { padding: 36px 24px; }
        .code { font-size: 52px; }
        .icon { width: 80px; height: 80px; }
        .icon svg { width: 42px; height: 42px; }
    }
</style>
</head>
<body>
    <div class="card">
        <div class="icon">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 1a5 5 0 0 0-5 5v3H6a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2h-1V6a5 5 0 0 0-5-5zm-3 8V6a3 3 0 1 1 6 0v3H9zm3 5a1.5 1.5 0 0 1 1 2.618V19a1 1 0 1 1-2 0v-2.382A1.5 1.5 0 0 1 12 14z"/>
            </svg>
        </div>
        <div class="code">403</div>
        <div class="title">无权操作</div>
        <p class="message">{$message}</p>
        {$tipHtml}
        <div class="actions">
            <a href="{$homeUrl}" class="btn btn-primary">返回首页</a>
            <a href="javascript:history.back()" class="btn btn-default">返回上一页</a>
        </div>
    </div>
</body>
</html>
HTML;
    }
}