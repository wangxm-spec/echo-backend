<?php

namespace app\admin\controller\member;

use app\admin\controller\Base;
use app\common\model\MemberAccountModel;
use app\common\model\MemberCallModel;

class Call extends Base
{
    /**
     * 称号列表
     */
    public function index()
    {
        if (!$this->request->isAjax()) {
            return view();
        }

        $limit  = $this->request->param('limit', 10);
        $offset = $this->request->param('offset', 0);
        $page   = floor($offset / $limit) + 1;

        $where[] = ['a.title', 'like', $this->request->param('keywords', '')];
        $where[] = ['a.uuid', '=', $this->request->param('uuid', '')];
        $where[] = ['a.role', '=', $this->request->param('role', '')];
        $where[] = ['a.status', '=', $this->request->param('status', '')];

        $order    = $this->request->param('order', '');
        $sort     = $this->request->param('sort', '');
        $orderStr = ($sort && $order) ? "a.{$sort} {$order}" : 'a.id desc';

        $res = MemberCallModel::alias('a')
            ->join('member_account b', 'b.delete_time is null and b.uuid = a.uuid', 'left')
            ->field('a.*,b.nickname,b.account')
            ->where(formatWhere($where))
            ->order($orderStr)
            ->paginate(['list_rows' => $limit, 'page' => $page])
            ->toArray();

        foreach ($res['data'] as &$item) {
            $item['role_text'] = MemberCallModel::role[$item['role']] ?? $item['role'];
        }
        unset($item);

        return json(['rows' => $res['data'], 'total' => $res['total']]);
    }

    /**
     * 新增/编辑称号
     */
    public function update()
    {
        $id = $this->request->param('id', 0);
        if (!$this->request->isPost()) {
            $info = MemberCallModel::find($id);
            $this->assign('id', $id);
            $this->assign('info', $info ?: new MemberCallModel());
            $this->assign('account_list', MemberAccountModel::field('uuid,account,nickname')->order('create_time desc')->select());
            return view();
        }

        $data = $this->request->param([
            'uuid'   => '',
            'title'  => '',
            'color'  => '',
            'role'   => '',
            'status' => 1,
        ]);

        if (empty($data['uuid'])) {
            return $this->error('请选择归属会员');
        }
        if (empty($data['title'])) {
            return $this->error('请输入称号名称');
        }
        if (empty($data['role'])) {
            return $this->error('请选择权限');
        }

        $now = date('Y-m-d H:i:s');

        if ($id > 0) {
            $data['update_time'] = $now;
            MemberCallModel::where('id', $id)->update($data);
        } else {
            $data['create_time'] = $now;
            $data['update_time'] = $now;
            MemberCallModel::create($data);
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
        MemberCallModel::where('id', $data['id'])->update([
            'status'      => $data['status'],
            'update_time' => date('Y-m-d H:i:s'),
        ]);
        return $this->success('操作成功');
    }

    /**
     * 删除称号
     */
    public function delete()
    {
        $idx = $this->request->param('id');
        if (!$idx) {
            return $this->error('参数错误');
        }
        $ids = is_array($idx) ? $idx : explode(',', $idx);
        MemberCallModel::destroy($ids);
        return $this->success('操作成功');
    }
}
