<?php

namespace app\common\exception;

use Exception;

class BaseException extends Exception
{
    /**
     * @var int
     */
    protected $code = 501;

    /**
     * @var string
     */
    protected $message = '操作失败';

    /**
     * 构造方法
     * @param string $message 错误信息
     * @param int $code 错误码
     */
    public function __construct(string $message = '', int $code = 0)
    {
        if ($message) {
            $this->message = $message;
        }
        if ($code) {
            $this->code = $code;
        }
        parent::__construct($this->message, $this->code);
    }
}