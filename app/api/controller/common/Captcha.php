<?php

namespace app\api\controller\common;

use app\api\controller\Base;

class Captcha extends Base
{
    function build(){
        $result = \Tinywan\Captcha\Captcha::base64();
        if(!env('DATA_DEBUG', 'false')){
            unset($result['value']);
        }
        return $this->success('SUCCESS', $result);
    }
}