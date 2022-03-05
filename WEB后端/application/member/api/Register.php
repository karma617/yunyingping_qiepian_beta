<?php

namespace app\member\api;

use app\member\model\Login;
use app\member\model\Member;
use app\one_api\api\ApiInit;

class Register extends ApiInit
{
    public function initialize()
    {
        parent::initialize();
    }
    //注册协议
    public function register_agree()
    {
        # code...
    }
    //用户名密码邮箱注册 改传 account password
    public function account_register()
    {
        $data = $this->params;
        $account = isset($data['account']) ? $data['account'] : $data['username'];
        
        $user['salt']        = random(6, 0);
        if (empty($account)) {
            return $this->_error('帐号必填');
        }
        if (!isset($data['password']) || empty($data['password'])) {
            return $this->_error('密码必填');
        }
        if (!isset($data['qq']) || empty($data['qq'])) {
            return $this->_error('qq必填');
        }

        if (is_email($account)) { // 邮箱
            $user['email']       = $account;
            $user['username'] = 'U'.random(6,0);
        } elseif (is_mobile($account)) { // 手机号
            $user['mobile']      = $account;
            $user['username'] = 'U'.random(6,0);
        } elseif (is_username($account)) { // 用户名
            $user['username']    = $account;
        }else{
            return $this->_error('帐号应为用户名|手机|邮箱');
        }
        $user['nickname']  = 'N_'.random(6,0);
        $user['salt'] = random(6,0);
        $user['password'] = $data['password'];
        $user['qq'] = $data['qq'];
        $validate = new \app\member\validate\Member;

        if (!$validate->check($user)) {
            return $this->_error($validate->getError());
        }
        $model = new Login();
        $rs = $model->register($user,$data);
        //自动登录
        action('member/login/login',$data,'api');

    }
    //手机号注册
    public function mobile_register()
    {
        # code...
    }
}
