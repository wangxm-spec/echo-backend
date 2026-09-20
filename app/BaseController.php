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
        $this->buildConfig();
    }

    /**
     * 构建系统配置
     * 从缓存或数据库加载配置并写入到 Config
     */
    protected function buildConfig(): void
    {
        // 从缓存获取配置数据
        $config_data = Cache::get('config_data');
        if (!$config_data) {
            $list = SystemConfigModel::column('data', 'name');
            Cache::set('config_data', $list);
            $config_data = $list;
        }
        // 写入到 config 命名空间 sys
        foreach ($config_data as $key => $value) {
            Config::set($value, "sys.{$key}");
        }
    }
}