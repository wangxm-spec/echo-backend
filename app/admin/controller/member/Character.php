<?php

namespace app\admin\controller\member;

use app\admin\controller\Base;
use app\common\model\MemberCharacterFileModel;
use app\common\model\MemberCharacterModel;

class Character extends Base
{
    /**
     * 角色卡列表
     */
    public function index()
    {
        if (!$this->request->isAjax()) {
            return view();
        }

        $limit  = $this->request->param('limit', 10);
        $offset = $this->request->param('offset', 0);
        $page   = floor($offset / $limit) + 1;

        $where[] = ['a.name|a.remark', 'like', $this->request->param('keywords', '')];
        $where[] = ['a.status', '=', $this->request->param('status', '')];

        $order    = $this->request->param('order', '');
        $sort     = $this->request->param('sort', '');
        $orderStr = ($sort && $order) ? "a.{$sort} {$order}" : 'a.create_time desc';

        $res = MemberCharacterModel::alias('a')
            ->join('member_account b', 'b.delete_time is null and b.uuid = a.uuid', 'left')
            ->field('a.*,b.nickname,b.account')
            ->where(formatWhere($where))
            ->order($orderStr)
            ->paginate(['list_rows' => $limit, 'page' => $page])
            ->toArray();

        return json(['rows' => $res['data'], 'total' => $res['total']]);
    }

    /**
     * 角色卡详情（只读）
     */
    public function detail()
    {
        $cuid = $this->request->param('cuid', '');

        $info = $cuid ? MemberCharacterModel::with(['files'])->where('cuid', $cuid)->find() : null;

        $this->assign('cuid', $cuid);
        $this->assign('info', $info ?: new MemberCharacterModel());

        return view();
    }

    /**
     * 角色卡文件信息（只读）
     */
    public function file()
    {
        $cuid = $this->request->param('cuid', '');

        $list = $cuid
            ? MemberCharacterFileModel::where('cuid', $cuid)->order('id desc')->select()->toArray()
            : [];

        foreach ($list as &$item) {
            $item['file_type_text'] = MemberCharacterModel::file_type[$item['file_type']] ?? $item['file_type'];
            $item['file_url']       = formatUrl($item['file_url']);
        }
        unset($item);

        $this->assign('cuid', $cuid);
        $this->assign('list', $list);

        return view();
    }

    /**
     * 删除角色卡
     */
    public function delete()
    {
        $idx = $this->request->param('cuid');
        if (!$idx) {
            return $this->error('参数错误');
        }
        $cuids = is_array($idx) ? $idx : explode(',', $idx);

        MemberCharacterFileModel::whereIn('cuid', $cuids)->update(['delete_time' => date('Y-m-d H:i:s')]);
        MemberCharacterModel::destroy($cuids);

        return $this->success('操作成功');
    }
}
