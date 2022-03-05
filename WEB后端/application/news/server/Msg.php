<?php
namespace app\news\server;

use app\common\server\Service;
use app\news\model\Msg as MsgModel;

class Msg extends Service{

    public function initialize() {
        parent::initialize();
        $this->MsgModel = new MsgModel();
    }

    public function getMsgLists($data) {
        $map = [];
        $map[] = ['status', 'eq', 1];
        return $this->MsgModel->getList($map, 1, 10);
    }
}