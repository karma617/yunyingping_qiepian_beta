<?php

namespace app\system\validate;

use think\Validate;

/**
 * 配置验证器
 * @package app\system\validate
 */
class SystemConfig extends Validate
{
    //定义验证规则
    protected $rule = [
        'name|配置标识'  => 'require|unique:system_config',
        'title|配置标题' => 'require',
        'type|配置类型'  => 'require',
    ];
}
