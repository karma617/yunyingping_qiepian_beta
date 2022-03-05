<?php
namespace app\news\server;

use app\common\server\Service;
use app\news\model\News as NewsModel;

class News extends Service{

    public function initialize() {
        parent::initialize();
        $this->NewsModel = new NewsModel();
    }

    public function getNewsLists($data) {
        $map = [];
        $map[] = ['status', 'eq', 1];
        return $this->NewsModel->getList($map, 1, 8);
    }

    public function getNewsDetail($data)
    {
        $map = [];
        $map[] = ['id', 'eq', $data['id']];
        return $this->NewsModel->where($map)->find();
    }
}