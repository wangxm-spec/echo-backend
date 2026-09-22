<?php
namespace app;

use app\common\model\SystemConfigModel;
use support\think\Cache;
use Webman\Config;

class BaseController
{
    protected $request;
    protected $appName;

    public function __construct()
    {
        $this->request = request();
        $this->appName = !empty($this->request->app) ? $this->request->app : 'index';
    }
}