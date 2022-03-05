<?php

namespace app\member\api;

use app\member\model\MemberLevel as MemberLevelModel;

class MemberLevel extends MemberInit
{
    public function initialize()
    {
        parent::initialize();
        $this->MemberLevelModel = new MemberLevelModel();
    }

    public function getList()
    {
        $map    = $data = [];
        $data = $this->params;
        $page   = isset($data['page']) ? $data['page'] : 1;
        $limit  = isset($data['limit']) ? $data['limit'] : 15;

        $map[] = ['status', 'eq', 1];
        $map[] = ['is_default', 'eq', 0];
        if (isset($data['keyword']) && !empty($data['keyword'])) {
            $map[] = ['level_name', 'like', "%{$data['keyword']}%"];
        }

        $data = $this->MemberLevelModel->pageList($map, true, 'sort asc, create_time desc', $page, $limit);
        return $this->_success('获取成功', $data);
    }
}