<?php

namespace app\news\home;

use app\news\server\Msg as MsgService;

class Index
{

    public function msg()
    {
        $data = input();
        $result = (new MsgService)->getMsgLists($data);
        $html = '';
        foreach ($result['list'] as $key => $value) {
            $html .= '<h5>'. ($key +1 ). '、' . $value['title'].'</h5>';
        }
        echo $html;
    }
}
