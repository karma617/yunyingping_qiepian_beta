<?php
namespace app\goods\validate;

use think\Validate;
/**
 * 订单验证器
 * @package app\user\validate
 */
class Order extends Validate
{
    //定义验证规则
    protected $rule = [
        'id|商品id' => 'require',
        'order_sn|订单号' => 'require',
    ];

    //定义验证提示
    protected $message = [
        'id.require' => '商品id不能为空',
        'order_sn.require' => '订单号不能为空',
    ];

    protected $scene = [
        'buy' => [
            'id'
        ],
        'getOrderInfo' => [
            'order_sn'
        ],
    ];
}
