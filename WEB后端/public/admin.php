<?php

// [ 后台入口文件 ]
namespace think;

header('Content-Type:text/html;charset=utf-8');

// 加载基础文件
require __DIR__ . '/../thinkphp/base.php';

// 定义应用目录
define('APP_PATH', __DIR__ . '/application/');

define('DS', DIRECTORY_SEPARATOR);

// 支持事先使用静态方法设置Request对象和Config对象

// 定义入口为admin
define('ENTRANCE', 'admin');


// 检查是否安装
if(!is_file('./../install.lock')) {
    header('location: /');
} else {
    Container::get('app')->run()->send();
}
