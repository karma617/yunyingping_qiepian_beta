<?php
return [
    //登录后
    'login_afert'=> [
        'app\\member\\hook\\Member',
    ],
    //登录创建token后
    'create_token_afert'=>[
        'app\\member\\hook\\Member',
    ],
    //注册后
    'register_afert'=> [
        'app\\member\\hook\\Member'
    ],
    //重置密码后
    'repwd_after'=> [
        'app\\member\\hook\\Member'
    ],
    //帐户变动后
    'account_modify_after'=>[
        'app\\member\\hook\\Member'
    ]

];