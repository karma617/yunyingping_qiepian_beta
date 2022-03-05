<?php

namespace app\member\api;

use app\one_api\api\ApiInit;
use app\member\model\Login as LoginModel;

class Login extends ApiInit
{
    public function initialize()
    {
        parent::initialize();
    }
    //普通登录
    public function login()
    {
        $login       = new LoginModel();
        try {
            $res = $login->login($this->params, $this->karma);
        } catch (\Exception $e) {
            return $this->_error($e->getMessage());
        }
        $res['token'] = $this->create_token($res);
        $res = runhook('create_token_afert', $res, true);
        return $this->_success('', $res[0]);
    }

    /**
     * 三方登录,自动注册
     * tag 登录方式 open_id 三方ID 必传
     * @return void
     * @author Leo <13708867890>
     * @since 2021-03-30 21:05:12
     */
    public function auth()
    {
        $login       = new LoginModel();
        if (!isset($this->params['tag']) || !isset($this->params['open_id'])) {
            return $this->_error('缺少登录方式和open_id-1');
        }
        if ($this->params['tag'] == '' || $this->params['open_id'] == '') {
            return $this->_error('缺少登录方式和open_id-2');
        }
        try {
            $res = $login->authLogin($this->params['tag'], $this->params['open_id'], $this->params);
        } catch (\Exception $e) {
            return $this->_error($e->getMessage());
        }
        $res['token'] = $this->create_token($res);
        runhook('create_token_afert', $res);
        return $this->_success('', $res);
    }
    //token
    public function create_token($data)
    {
        $token = md5($data['id'].$data['salt']);
        cache($token, $data, 86400 * 10, 'member');
        return $token;
    }
}
