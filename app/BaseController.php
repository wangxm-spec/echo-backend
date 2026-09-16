<?php
namespace app;

use support\Request;
use think\model\View;
use think\Template;

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