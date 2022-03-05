<?php
/**
 * 模块菜单
 * 字段说明
 * url 【链接地址】格式：goods/控制器/方法，可填写完整外链[必须以http开头]
 * param 【扩展参数】格式：a=123&b=234555
 */
return [
    [
        'pid'           => 0,
        'is_menu'       => '1',
        'title'         => '订单管理',
        'icon'          => 'el-icon-suitcase',
        'module'        => 'goods',
        'url'           => 'goods/index/index',
        'param'         => '',
        'target'        => '_self',
        'nav'           => 1,
        'sort'          => 100,
        'childs'         => [
            [
                'title' => '订单列表',
                'icon' => 'fa fa-credit-card',
                'module' => 'goods',
                'url' => 'goods/order/index',
                'param' => '',
                'target' => '_self',
                'debug' => 0,
                'system' => 0,
                'nav' => 1,
                'sort' => 0,
            ],
        ]
    ],
];