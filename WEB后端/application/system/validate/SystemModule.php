<?php

namespace app\system\validate;

use think\Validate;

/**
 * 模块验证器
 * @package app\system\validate
 */
class SystemModule extends Validate
{
    //定义验证规则
    protected $rule = [
        'name|插件名称'     => 'require|alpha|unique:system_module',
        'title|插件标题'     => 'require|chsAlphaNum|unique:system_module',
        'author|开发者'     => 'requireWith:author|chsAlphaNum',
        'version|版本号'     => 'regex:/^[0-9][.][0-9][.][0-9]+$/',
    ];
}
