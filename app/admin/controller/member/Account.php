<?php

namespace app\admin\controller\member;

use app\admin\controller\Base;
use app\common\model\MemberAccountModel;
use app\common\model\MemberCallModel;

class Account extends Base
{
    /**
     * 会员账号列表
     */
    public function index()
    {
        if (!$this->request->isAjax()) {
            return view();
        }

        $limit  = $this->request->param('limit', 10);
        $offset = $this->request->param('offset', 0);
        $page   = floor($offset / $limit) + 1;

        $where[] = ['a.account|a.nickname|a.email|a.qq_number', 'like', $this->request->param('keywords', '')];
        $where[] = ['a.status', '=', $this->request->param('status', '')];

        $order    = $this->request->param('order', '');
        $sort     = $this->request->param('sort', '');
        $orderStr = ($sort && $order) ? "a.{$sort} {$order}" : 'a.create_time desc';

        $res = MemberAccountModel::alias('a')
            ->join('member_call b', 'b.delete_time is null and b.id = a.call_id', 'left')
            ->field('a.*,b.title as call_title')
            ->where(formatWhere($where))
            ->order($orderStr)
            ->paginate(['list_rows' => $limit, 'page' => $page])
            ->toArray();

        return json(['rows' => $res['data'], 'total' => $res['total']]);
    }

    /**
     * 编辑账号（本模块不提供新增）
     */
    public function update()
    {
        $uuid = $this->request->param('uuid', '');

        if (!$this->request->isPost()) {
            $info = $uuid ? MemberAccountModel::find($uuid) : null;
            $this->assign('uuid', $uuid);
            $this->assign('info', $info ?: new MemberAccountModel());
            $this->assign('call_list', MemberCallModel::order('id desc')->select());
            return view();
        }

        if (!$uuid) {
            return $this->error('参数错误');
        }

        $account = MemberAccountModel::find($uuid);
        if (!$account) {
            return $this->error('账号不存在');
        }

        $data = $this->request->param([
            'nickname'  => '',
            'qq_number' => '',
            'avatar'    => '',
            'mbti_type' => '',
            'call_id'   => 0,
        ]);

        if (empty($data['nickname'])) {
            return $this->error('请输入昵称');
        }

        $data['update_time'] = date('Y-m-d H:i:s');
        MemberAccountModel::where('uuid', $uuid)->update($data);

        return $this->success('提交成功');
    }

    /**
     * 修改禁用
     */
    public function status()
    {
        $uuid   = $this->request->param('uuid', '');
        $status = $this->request->param('status', 0);

        if (!$uuid) {
            return $this->error('参数错误');
        }

        MemberAccountModel::where('uuid', $uuid)->update([
            'status'      => $status,
            'update_time' => date('Y-m-d H:i:s'),
        ]);

        return $this->success('操作成功');
    }

    /**
     * 修改密码
     */
    public function password()
    {
        $uuid     = $this->request->param('uuid', '');
        $password = $this->request->param('password', '');

        if (!$uuid) {
            return $this->error('参数错误');
        }
        if (empty($password)) {
            return $this->error('请输入新密码');
        }

        $account = MemberAccountModel::where('uuid', $uuid)->find();
        if (!$account) {
            return $this->error('账号不存在');
        }

        $account->password    = password_hash($password, PASSWORD_DEFAULT);
        $account->update_time = date('Y-m-d H:i:s');
        $account->save();

        return $this->success('密码重置成功');
    }

    /**
     * 删除
     */
    public function delete()
    {
        $idx = $this->request->param('uuid');
        if (!$idx) {
            return $this->error('参数错误');
        }
        $uuids = is_array($idx) ? $idx : explode(',', $idx);
        MemberAccountModel::destroy($uuids);
        return $this->success('操作成功');
    }
}
