<?php

namespace app\admin\controller;
use app\common\model\SystemConfigModel;
use support\think\Cache;

class Config extends Base
{
    /** 配置分组 */
    protected $groupList = [
        1  => '基础配置',
        2  => '用户配置',
    ];

    /** 输入类型 */
    protected $typeList = [
        1 => '文本',
        2 => '数字',
        3 => '文本域',
        4 => '开关',
        5 => '下拉选择',
        6 => '图片上传',
        7 => '编辑器',
        8 => '标签'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->assign('group_list', $this->groupList);
        $this->assign('type_list',  $this->typeList);
    }

    function clear(){
        Cache::delete('config_data');
        return $this->success('清理完成');
    }

    /**
     * 分组配置面板
     */
    public function config()
    {
        if(!$this->request->isPost()){
            $group = intval($this->request->param('group', 1));

            $list = SystemConfigModel::where('group', $group)
                ->where('status', 1)
                ->order('sort asc, id asc')
                ->select()
                ->toArray();

            $this->assign('group', $group);
            $this->assign('group_text', $this->groupList[$group] ?? '');
            $this->assign('list', $list);

            return view();
        }
        $data = $this->request->post();
        $now  = date('Y-m-d H:i:s');

        foreach ($data as $key => $value) {
            if (in_array($key, ['group', 'page'])) continue;

            $config = SystemConfigModel::where('name', $key)
                ->where('status', 1)
                ->find();
            if ($config) {
                if (is_array($value)) {
                    $value = implode(',', $value);
                }
                $config->value       = $value;
                $config->update_time = $now;
                $config->save();
            }
        }
        $this->clear();
        return $this->success('保存成功');
    }
}