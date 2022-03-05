<?php

namespace app\index\api;

use app\member\api\MemberInit;
use app\member\model\MemberLevel as MemberLevelModel;

class Fileapi extends MemberInit
{
    public function initialize()
    {
        parent::initialize();
    }

    public function all()
    {
        $api_limit = (new MemberLevelModel)->cache(md5('groupApiLimit'.$this->member['level_id']), 86400)->where('id', $this->member['level_id'])->value('api_limit');
        $api_limit = explode(',', $api_limit);
        $all_api = config('api.all_api');
        $ret = [];
        foreach ($api_limit as $key => $value) {
            $ret[] = $all_api[$value];
        }
        return $this->_success('获取成功', $ret);
    }
}
