<?php

namespace app\member\validate;

use think\Validate;

class Member extends Validate
{
    //定义验证规则
    protected $rule = [
        'username|用户名' => 'require|alphaNum|unique:member',
        'nickname|昵称'   => 'require',
        'password|密码'   => 'require',
        'qq|QQ'           => 'require|unique:member',
        'email|邮箱'      => 'requireWith:email|email|unique:member',
        'mobile|手机号'   => 'requireWith:mobile|regex:^1\d{10}|unique:member',
    ];


    // 自定义场景
    public function sceneMemberAdd()
    {
        return $this->only(['username', 'nickname', 'password', 'email', 'mobile', 'qq']);
    }
    public function sceneMemberEdit()
    {
        return $this->only(['username', 'nickname', 'password', 'email', 'mobile']);
    }

    
}
