<?php

namespace app\common\exception;

class SystemException extends BaseException
{
    /**
     * @var int
     */
    protected $code = 500;

    /**
     * @var string
     */
    protected $message = '系统已暂停服务，请注意查看公告';

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