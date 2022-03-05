<?php

/**
 * 模块菜单
 * 字段说明
 * url 【链接地址】格式：pay/控制器/方法，可填写完整外链[必须以http开头]
 * param 【扩展参数】格式：a=123&b=234555
 */
return [
    [
        'title' => '在线支付',
        'icon' => 'mdi mdi-wallet-giftcard',
        'module' => 'one_pay',
        'url' => 'one_pay/index',
        'param' => '',
        'nav' => 1,
        'target' => '_self',
        'sort' => 100,
        'childs' => [
            [
                'title' => '支付管理',
                'module' => 'one_pay',
                'url' => 'one_pay/index/index',
                'param' => '',
                'childs' => [[
                    'title' => '安装',
                    'module' => 'one_pay',
                    'url' => 'one_pay/index/install',
                    'param' => '',
                    'target' => '_self',
                    'nav' => 0,
                ], [
                    'title' => '卸载',
                    'module' => 'one_pay',
                    'url' => 'one_pay/index/uninstall',
                    'param' => '',
                    'target' => '_self',
                    'nav' => 0,
                ], [
                    'title' => '状态设置',
                    'module' => 'one_pay',
                    'url' => 'one_pay/index/status',
                    'param' => '',
                    'target' => '_self',
                    'nav' => 0,
                ], [
                    'title' => '配置',
                    'module' => 'one_pay',
                    'url' => 'one_pay/index/config',
                    'param' => '',
                    'target' => '_self',
                    'nav' => 0,
                ], [
                    'title' => '排序',
                    'module' => 'one_pay',
                    'url' => 'one_pay/index/sort',
                    'param' => '',
                    'target' => '_self',
                    'nav' => 0,
                ],]
            ],
            [
                'title' => '支付日志',
                'module' => 'one_pay',
                'url' => 'one_pay/logs/index',
                'param' => '',
                'debug' => 0,
                'system' => 0,
                'nav' => 1,
                'childs' => [[
                    'title' => '删除',
                    'module' => 'one_pay',
                    'url' => 'one_pay/index/logsDel',
                    'param' => '',
                    'target' => '_self',
                    'debug' => 0,
                    'system' => 0,
                    'nav' => 0,
                ]]
            ],
        ],
    ],
];
