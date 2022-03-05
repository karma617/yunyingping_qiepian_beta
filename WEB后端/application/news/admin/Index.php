<?php
namespace app\news\admin;
use app\system\admin\Admin;

use app\news\model\News as NewsModel;

class Index extends Admin
{
    protected $oneModel = 'News';//模型名称[通用添加、修改专用]

    public function initialize()
    {
        parent::initialize();
        $this->NewsModel = new NewsModel();
    }
    public function index()
    {
        if ($this->request->isAjax()) {
            $map    = $data = [];
            $data = $this->params;
            $page   = isset($data['page']) ? $data['page'] : 1;
            $limit  = isset($data['limit']) ? $data['limit'] : 15;
            $data = $this->NewsModel->getList($map, $page, $limit);
            return $this->success('获取成功', '', $data);
        }
        return $this->fetch();
    }
}