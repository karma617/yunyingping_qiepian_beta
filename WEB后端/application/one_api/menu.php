<?php

/**
 * 模块菜单
 * 字段说明
 * url 【链接地址】格式：one_api/控制器/方法，可填写完整外链[必须以http开头]
 * param 【扩展参数】格式：a=123&b=234555
 */
return [
    [
        'pid'           => 0,
        'title'         => 'API授权',
        'icon'          => 'mdi mdi-artstation',
        'module'        => 'one_api',
        'url'           => 'one_api/index/index',
        'param'         => '',
        'target'        => '_self',
        'sort'          => 100,
        'childs' => [
            [
              'title' => '添加',
              'module' => 'one_api',
              'url' => 'one_api/index/add',
              'param' => '',
              'debug' => 0,
              'nav' => 0,
            ],
            [
              'title' => '编辑',
              'module' => 'one_api',
              'url' => 'one_api/index/edit',
              'param' => '',
              'debug' => 0,
              'nav' => 0,
            ],
            [
              'title' => '删除',
              'module' => 'one_api',
              'url' => 'one_api/index/del',
              'param' => '',
              'debug' => 0,
              'nav' => 0,
            ],
            [
              'title' => '改变状态',
              'module' => 'one_api',
              'url' => 'one_api/index/status',
              'param' => '',
              'debug' => 0,
              'nav' => 0,
            ],
        ],
    ],
];