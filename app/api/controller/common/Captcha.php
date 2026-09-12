<?php

namespace app\api\controller\common;

use app\api\controller\Base;

class Captcha extends Base
{
    function build(){
        $result = \Tinywan\Captcha\Captcha::base64();
        if(strtolower(env('DATA_DEBUG', 'false')) != 'true'){
            unset($result['value']);
        }
        return $this->success('SUCCESS', $result);
    }
}