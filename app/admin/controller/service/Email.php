<?php

namespace app\admin\controller\service;

use app\admin\controller\Base;
use app\common\model\ServiceEmailLogModel;

class Email extends Base
{
    /**
     * 邮件发送日志
     */
    public function log()
    {
        if (!$this->request->isAjax()) {
            $this->assign('type_list', ServiceEmailLogModel::type);
            $this->assign('status_list', ServiceEmailLogModel::status);
            return view();
        }

        $limit  = $this->request->param('limit', 10);
        $offset = $this->request->param('offset', 0);
        $page   = floor($offset / $limit) + 1;

        $where[] = ['email|subject', 'like', $this->request->param('keywords', '')];
        $where[] = ['type', '=', $this->request->param('type', '')];
        $where[] = ['status', '=', $this->request->param('status', '')];
        $where[] = ['create_time', 'between time', $this->request->param('create_time', '')];

        $order    = $this->request->param('order', '');
        $sort     = $this->request->param('sort', '');
        $orderStr = ($sort && $order) ? "{$sort} {$order}" : 'id desc';

        $res = ServiceEmailLogModel::field('id,email,code,type,subject,status,error_count,use_time,create_time')
            ->where(formatWhere($where))
            ->order($orderStr)
            ->paginate(['list_rows' => $limit, 'page' => $page])
            ->toArray();

        foreach ($res['data'] as &$item) {
            $item['type_text']   = ServiceEmailLogModel::type[$item['type']] ?? $item['type'];
            $item['status_text'] = ServiceEmailLogModel::status[$item['status']] ?? $item['status'];
        }
        unset($item);

        return json(['rows' => $res['data'], 'total' => $res['total']]);
    }
}
