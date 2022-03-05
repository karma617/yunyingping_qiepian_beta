<?php
namespace app\goods\admin;
use app\system\admin\Admin;

use app\goods\model\GoodsOrder as GoodsOrderModel;

class Order extends Admin
{

    protected $oneModel = 'GoodsOrder';

    public function index()
    {
        if ($this->request->isAjax()) {
            if ($this->request->isAjax()) {
                $page   = isset($data['page']) ? $data['page'] : 1;
                $limit  = isset($data['limit']) ? $data['limit'] : 15;
                $map = [];
                $type = input('type/d', -1);
                $keyword = input('keyword/s', '');
                if ($keyword) {
                    $map[] = ['order_sn|trade_no', 'like', "%" . $keyword . "%"];
                }
                //会员等级
                if ($type >= 0) {
                    $map[] = ['type', 'eq', $type];
                }
                
                $data = (new GoodsOrderModel)->pageList($map, true, 'create_time desc', $page, $limit);
                return $this->success('获取成功', '', $data);
            }
            return $this->fetch();
        }
        return $this->fetch();
    }
}