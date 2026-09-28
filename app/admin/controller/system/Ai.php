<?php

namespace app\admin\controller\system;

use app\admin\controller\Base;
use app\common\model\SystemAiProvidersModel;

class Ai extends Base
{
    /**
     * AI服务商列表
     */
    public function index()
    {
        if (!$this->request->isAjax()) {
            $this->assign('type_list', SystemAiProvidersModel::type);
            $this->assign('status_list', SystemAiProvidersModel::status);
            return view();
        }

        $limit  = $this->request->param('limit', 10);
        $offset = $this->request->param('offset', 0);
        $page   = floor($offset / $limit) + 1;

        $where[] = ['key|title', 'like', $this->request->param('keywords', '')];
        $where[] = ['type', '=', $this->request->param('type', '')];
        $where[] = ['status', '=', $this->request->param('status', '')];

        $order    = $this->request->param('order', '');
        $sort     = $this->request->param('sort', '');
        $orderStr = ($sort && $order) ? "{$sort} {$order}" : 'id desc';

        $res = SystemAiProvidersModel::where(formatWhere($where))
            ->order($orderStr)
            ->paginate(['list_rows' => $limit, 'page' => $page])
            ->toArray();

        foreach ($res['data'] as &$item) {
            $item['type_text']  = SystemAiProvidersModel::type[$item['type']] ?? $item['type'];
            $item['model_list'] = is_array($item['models']) ? implode(', ', $item['models']) : $item['models'];
        }
        unset($item);

        return json(['rows' => $res['data'], 'total' => $res['total']]);
    }

    /**
     * 新增/编辑服务商
     */
    public function update()
    {
        $id = $this->request->param('id', 0);
        if (!$this->request->isPost()) {
            $info = SystemAiProvidersModel::find($id);
            $this->assign('id', $id);
            $this->assign('info', $info ?: new SystemAiProvidersModel());
            return view();
        }

        $data = $this->request->param([
            'key'           => '',
            'title'         => '',
            'desc'          => '',
            'type'          => '',
            'config'        => '',
            'models'        => '',
            'default_model' => '',
            'status'        => 1,
        ]);

        if (empty($data['key'])) {
            return $this->error('请输入调用键名');
        }
        if (empty($data['title'])) {
            return $this->error('请输入标题');
        }
        if (empty($data['type'])) {
            return $this->error('请选择服务商类型');
        }

        // 调用键名唯一
        $where = [['key', '=', $data['key']]];
        if ($id > 0) {
            $where[] = ['id', '<>', $id];
        }
        if (SystemAiProvidersModel::where($where)->count() > 0) {
            return $this->error('调用键名已存在');
        }

        // 配置参数
        $config = [];
        if (!empty($data['config'])) {
            $config = json_decode($data['config'], true);
            if (!is_array($config)) {
                return $this->error('配置参数必须是合法的 JSON');
            }
        }
        $data['config'] = $config;

        // 模型列表
        $models = [];
        if (!empty($data['models'])) {
            $models = array_values(array_filter(array_map('trim', explode(',', $data['models']))));
        }
        $data['models'] = $models;

        if (empty($data['default_model'])) {
            $data['default_model'] = $models[0] ?? '';
        }

        $now = date('Y-m-d H:i:s');

        if ($id > 0) {
            $data['udpate_time'] = $now;
            SystemAiProvidersModel::where('id', $id)->update($data);
        } else {
            $data['create_time'] = $now;
            $data['udpate_time'] = $now;
            SystemAiProvidersModel::create($data);
        }

        return $this->success('提交成功');
    }

    /**
     * 修改状态
     */
    public function status()
    {
        $data = $this->request->param(['id', 'status']);
        if (!$data['id']) {
            return $this->error('参数错误');
        }
        SystemAiProvidersModel::where('id', $data['id'])->update([
            'status'      => $data['status'],
            'udpate_time' => date('Y-m-d H:i:s'),
        ]);
        return $this->success('操作成功');
    }

    /**
     * 删除服务商
     */
    public function delete()
    {
        $idx = $this->request->param('id');
        if (!$idx) {
            return $this->error('参数错误');
        }
        $ids = is_array($idx) ? $idx : explode(',', $idx);
        SystemAiProvidersModel::destroy($ids);
        return $this->success('操作成功');
    }
}
