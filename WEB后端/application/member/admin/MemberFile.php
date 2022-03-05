<?php
namespace app\member\admin;
use app\system\admin\Admin;

class MemberFile extends Admin
{

    protected $oneModel = 'MemberFile';

    protected function initialize()
    {
        parent::initialize();
        $this->member_file_model = model('MemberFile');
    }

    public function index()
    {
        if ($this->request->isAjax()) {
            if ($this->request->isAjax()) {
                $map    = $data = [];
                $data = $this->params;
                $page   = isset($data['page']) ? $data['page'] : 1;
                $limit  = isset($data['limit']) ? $data['limit'] : 15;

                $keyword = input('keyword/s', '');
                if ($keyword) {
                    $map[] = ['fileSortName|fileName', 'like', "%" . $keyword . "%"];
                }

                $data = $this->member_file_model->pageList($map, true, 'create_time desc', $page, $limit);
                return $this->success('获取成功', '', $data);
            }
            return $this->fetch();
        }
        return $this->fetch();
    }

}