<?php

namespace app\admin\controller\content;

use app\admin\controller\Base;
use app\common\model\CommonMbtiTypeModel;

class Mbti extends Base
{
    /**
     * MBTI 人格列表
     */
    public function index()
    {
        if (!$this->request->isAjax()) {
            return view();
        }

        $limit  = $this->request->param('limit', 10);
        $offset = $this->request->param('offset', 0);
        $page   = floor($offset / $limit) + 1;

        $where[] = ['name|traits', 'like', $this->request->param('keywords', '')];
        $where[] = ['group', '=', $this->request->param('group', '')];
        $where[] = ['status', '=', $this->request->param('status', '')];

        $order    = $this->request->param('order', '');
        $sort     = $this->request->param('sort', '');
        $orderStr = ($sort && $order) ? "{$sort} {$order}" : 'code asc';

        $res = CommonMbtiTypeModel::where(formatWhere($where))
            ->order($orderStr)
            ->paginate(['list_rows' => $limit, 'page' => $page])
            ->toArray();

        foreach ($res['data'] as &$item) {
            $item['group_text'] = CommonMbtiTypeModel::group[$item['group']] ?? $item['group'];
        }
        unset($item);

        return json(['rows' => $res['data'], 'total' => $res['total']]);
    }

    /**
     * 新增/编辑人格
     */
    public function update()
    {
        $code = $this->request->param('code', '');
        if (!$this->request->isPost()) {
            $info = $code ? CommonMbtiTypeModel::find($code) : null;
            $this->assign('code', $code);
            $this->assign('info', $info ?: new CommonMbtiTypeModel());
            return view();
        }

        $data = $this->request->param([
            'code'        => '',
            'name'        => '',
            'group'       => '',
            'description' => '',
            'traits'      => '',
            'theme_color' => '',
            'status'      => 1,
        ]);

        $data['code'] = strtoupper(trim($data['code']));

        if (empty($data['code'])) {
            return $this->error('请输入人格代码');
        }
        if (empty($data['name'])) {
            return $this->error('请输入中文名');
        }
        if (empty($data['group'])) {
            return $this->error('请选择人格分组');
        }

        $now = date('Y-m-d H:i:s');
        $old = $code ? CommonMbtiTypeModel::find($code) : null;

        if ($old) {
            // 人格代码为主键，代码变更时先删后增
            if ($old->code !== $data['code']) {
                if (CommonMbtiTypeModel::where('code', $data['code'])->count() > 0) {
                    return $this->error('人格代码已存在');
                }
                CommonMbtiTypeModel::destroy($old->code);
                $data['create_time'] = $now;
                $data['update_time'] = $now;
                CommonMbtiTypeModel::create($data);
            } else {
                $data['update_time'] = $now;
                CommonMbtiTypeModel::where('code', $old->code)->update($data);
            }
        } else {
            if (CommonMbtiTypeModel::where('code', $data['code'])->count() > 0) {
                return $this->error('人格代码已存在');
            }
            $data['create_time'] = $now;
            $data['update_time'] = $now;
            CommonMbtiTypeModel::create($data);
        }

        return $this->success('提交成功');
    }

    /**
     * 修改状态
     */
    public function status()
    {
        $data = $this->request->param(['code', 'status']);
        if (!$data['code']) {
            return $this->error('参数错误');
        }
        CommonMbtiTypeModel::where('code', $data['code'])->update([
            'status'      => $data['status'],
            'update_time' => date('Y-m-d H:i:s'),
        ]);
        return $this->success('操作成功');
    }

    /**
     * 删除人格
     */
    public function delete()
    {
        $idx = $this->request->param('code');
        if (!$idx) {
            return $this->error('参数错误');
        }
        $codes = is_array($idx) ? $idx : explode(',', $idx);
        CommonMbtiTypeModel::destroy($codes);
        return $this->success('操作成功');
    }
}
