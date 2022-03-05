<?php

namespace app\member\api;

use app\one_api\api\ApiInit;
use app\member\model\Base;
use app\member\model\Member;

class MemberInit extends ApiInit
{
    public function initialize()
    {
        parent::initialize();
        $this->MemberrModel = new Member();
        $id = (int)cache($this->token)['id'];
        $this->member = $this->memberObj = $this->MemberrModel->get($id);
        if (false == $this->member) {
            return $this->_error('用户信息不存在或登录超时', '', 1200);
        }
        if (0 == $this->member['status']) {
            cache($this->token, null);
            return $this->_error('帐号已禁用');
        }
        $this->member = $this->member->append(['exptime_text'])->toArray();
        //设置查询范围
        Base::$member_id = $this->member['id'];
    }
    //发送短信验证码
    public function get_code($type = 'modify_mobile', $target = '1300000000')
    {
        $cache_tag = $type;
        $cache_name = $type . '_' . md5(uniqid(null, true));
        $code = random(6);
        $data = ['code' => $code, 'target' => $target];
        cache($cache_name, $data, 600, $cache_tag);
        runhook('notify', $data);
        $this->_success('', ['key' => $cache_name]);
    }
}
