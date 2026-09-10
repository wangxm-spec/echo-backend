<?php

namespace app\api\exception;

use app\common\exception\BaseException;

class AuthException extends BaseException
{
    /**
     * @var int
     */
    protected $code = 888;

    /**
     * @var string
     */
    protected $message = '参数错误';

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