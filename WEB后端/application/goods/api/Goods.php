<?php

namespace app\goods\api;

use app\member\api\MemberInit;
use app\goods\model\Goods as GoodsModel;

class Goods extends MemberInit
{
    public function initialize()
    {
        parent::initialize();
        $this->GoodsModel = new GoodsModel();
    }

    public function getGoodsList()
    {
        $map    = $data = [];
        $data = $this->params;
        $page   = isset($data['page']) ? $data['page'] : 1;
        $limit  = isset($data['limit']) ? $data['limit'] : 15;

        $map[] = ['status', 'eq', 0];
        if (isset($data['keyword']) && !empty($data['keyword'])) {
            $map[] = ['goods_name', 'like', "%{$data['keyword']}%"];
        }

        $data = $this->GoodsModel->pageList($map, true, 'create_time desc', $page, $limit);
        return $this->_success('获取成功', '', $data);
    }
}
