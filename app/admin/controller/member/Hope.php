<?php

namespace app\admin\controller\member;

use app\admin\controller\Base;
use app\common\model\MemberHopeLogModel;

class Hope extends Base
{
    /**
     * 琥珀变更日志
     */
    public function log()
    {
        if (!$this->request->isAjax()) {
            $this->assign('type_list', MemberHopeLogModel::type);
            $this->assign('from_list', MemberHopeLogModel::from);
            return view();
        }

        $limit  = $this->request->param('limit', 10);
        $offset = $this->request->param('offset', 0);
        $page   = floor($offset / $limit) + 1;

        $where[] = ['a.uuid', '=', $this->request->param('uuid', '')];
        $where[] = ['a.type', '=', $this->request->param('type', '')];
        $where[] = ['a.from', '=', $this->request->param('from', '')];
        $where[] = ['a.create_time', 'between time', $this->request->param('create_time', '')];

        $order    = $this->request->param('order', '');
        $sort     = $this->request->param('sort', '');
        $orderStr = ($sort && $order) ? "a.{$sort} {$order}" : 'a.id desc';

        $res = MemberHopeLogModel::alias('a')
            ->join('member_account b', 'b.delete_time is null and b.uuid = a.uuid', 'left')
            ->field('a.*,b.nickname,b.account')
            ->where(formatWhere($where))
            ->order($orderStr)
            ->paginate(['list_rows' => $limit, 'page' => $page])
            ->toArray();

        foreach ($res['data'] as &$item) {
            $item['type_text'] = MemberHopeLogModel::type[$item['type']] ?? $item['type'];
            $item['from_text'] = MemberHopeLogModel::from[$item['from']] ?? $item['from'];
        }
        return json(['rows' => $res['data'], 'total' => $res['total']]);
    }
}
