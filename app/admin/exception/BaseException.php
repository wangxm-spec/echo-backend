<?php

namespace app\admin\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class BaseException extends \Exception
{
    protected $code    = 501;
    protected $message = '操作失败';
    protected string $tip = '';

    public function __construct(string $message = '', int $code = 0, string $tip = '')
    {
        if ($message !== '') {
            $this->message = $message;
        }
        if ($code !== 0) {
            $this->code = $code;
        }
        if ($tip !== '') {
            $this->tip = $tip;
        }
        parent::__construct($this->message, $this->code);
    }

    public function getTip(): string
    {
        return $this->tip;
    }

    /**
     * 默认渲染：统一 JSON
     * 子类（如 AuthException）可覆盖成 HTML/JSON 双模式
     */
    public function renderResponse(Request $request): Response
    {
        return json([
            'status' => $this->getCode(),
            'msg'    => $this->getMessage(),
            'data'   => '',
        ]);
    }
}