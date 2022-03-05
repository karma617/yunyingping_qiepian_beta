<?php

namespace app\news\api;

use app\member\api\MemberInit;
use app\news\server\News as NewsService;
use app\news\server\Msg as MsgService;

class Index extends MemberInit
{
    public function initialize()
    {
        parent::initialize();
        $this->NewsService = new NewsService();
        $this->MsgService = new MsgService();
    }

    public function lists()
    {
        $data = $this->params;
        $result = $this->NewsService->getNewsLists($data);
        if (false === $result) {
            return $this->_error($this->BannerService->getError());
        }
        return $this->_success('获取成功', $result);
    }

    public function detail()
    {
        $data = $this->params;
        $result = $this->NewsService->getNewsDetail($data);
        if (false === $result) {
            return $this->_error($this->BannerService->getError());
        }
        return $this->_success('获取成功', $result);
    }

    public function msg()
    {
        $data = $this->params;
        $result = $this->MsgService->getMsgLists($data);
        foreach ($result['list'] as $key => $val) {
            $result['list'][$key]['content'] = str_replace("\n", '<br/>', $val['content']);
        }
        if (false === $result) {
            return $this->_error($this->MsgService->getError());
        }
        return $this->_success('获取成功', $result);
    }
}
