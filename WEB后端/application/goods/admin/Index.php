<?php
namespace app\goods\admin;
use app\system\admin\Admin;

use app\goods\model\Goods as GoodsModel;

class Index extends Admin
{

    protected $oneModel = 'Goods';

    public function index()
    {
        if ($this->request->isAjax()) {
            if ($this->request->isAjax()) {
                $map    = $data = [];
                $data = $this->params;
                $page   = isset($data['page']) ? $data['page'] : 1;
                $limit  = isset($data['limit']) ? $data['limit'] : 15;
                $data = (new GoodsModel)->pageList($map, true, 'create_time desc', $page, $limit);
                return $this->success('获取成功', '', $data);
            }
            return $this->fetch();
        }
        return $this->fetch();
    }
}