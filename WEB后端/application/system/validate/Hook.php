<?php

namespace app\system\validate;

use think\Validate;

/**
 * 钩子验证器
 * @package app\system\validate
 */
class Hook extends Validate
{
    //定义验证规则
    protected $rule = [
		'name|钩子名称'	=> 'require|unique:system_hook',
		'intro|钩子描述' => 'require',
    ];
}
