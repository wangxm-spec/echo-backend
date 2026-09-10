<?php

namespace app\common\exception;

class ParamException extends BaseException
{
    /**
     * @var int
     */
    protected $code = 400;

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