<?php

namespace app\goods\home;

use app\common\controller\Common;
use app\goods\model\GoodsOrder as GoodsOrderModel;
use app\member\model\Member as MemberModel;
use app\goods\model\Goods as GoodsModel;
use app\one_pay\model\OnePayLog as PayLogModel;

class Callback extends Common
{

    // array (
    //     'gmt_create' => '2021-12-11 15:36:21',
    //     'charset' => 'utf-8',
    //     'seller_email' => '723875993@qq.com',
    //     'subject' => '包月会员',
    //     'sign' => 'POT7N8U0WecyLOCs3ExVx8egj7MujNJGGD0n1Ae0x9ZALlDVKhweMmeZGgIoIHDKJIvnr6Oi/O0TnreXRBdHrL5nuoxg4lsCLpTZja367XtzcnZw/SEDpzmn501gUZSdJKAZgqLLgRatx0ow/ybaucWiY948I4CQ0hGYUDUkL1X7H84P6fo+5tGtLM3tq03pJuxyOMDtWp+aUnajMAjakGm8XTPjz52agsyutiIgM9QWzJkY32w9vMbjPvtrbTvRLcRvlRVIO1CjD65e4QfUeMGvy9o6CbhpzqOmb2heoI8zjoyRPunF8tQ4Q/tsYB3ptXYKCHJvZf6fdxPWlUDblA==',
    //     'buyer_id' => '****',
    //     'body' => '包月会员',
    //     'invoice_amount' => '0.01',
    //     'notify_id' => '******',
    //     'fund_bill_list' => '[{"amount":"0.01","fundChannel":"ALIPAYACCOUNT"}]',
    //     'notify_type' => 'trade_status_sync',
    //     'trade_status' => 'TRADE_SUCCESS',
    //     'receipt_amount' => '0.01',
    //     'buyer_pay_amount' => '0.01',
    //     'app_id' => '****',
    //     'sign_type' => 'RSA2',
    //     'seller_id' => '*****',
    //     'gmt_payment' => '2021-12-11 15:36:25',
    //     'notify_time' => '2021-12-11 15:36:25',
    //     'version' => '1.0',
    //     'out_trade_no' => '****',
    //     'total_amount' => '0.01',
    //     'trade_no' => '******',
    //     'auth_app_id' => '****',
    //     'buyer_logon_id' => '*******',
    //     'point_amount' => '0.00',
    //     'method' => 'alipay_scan',
    //   )
    public function index($param)
    {
        $online_log = env('root_path') . "/paylog.txt";
        file_put_contents($online_log, var_export($param, true));
        $GoodsOrderModel = new GoodsOrderModel;
        $MemberModel = new MemberModel;

        $pay_log_status = (new PayLogModel)->where('order_no', $param['out_trade_no'])->value('status');

        // 如果支付成功
        if ($pay_log_status == 2) {
            $order_info = $GoodsOrderModel->where('order_sn', $param['out_trade_no'])->find();
            if ($order_info) {
                // 修改订单状态
                $order_info->trade_no = $param['trade_no'];
                $order_info->status = 1;
                $order_info->save();

                // 判断商品模型 0 会员 1 普通商品
                $model = $order_info->type == 1 ? '\app\goods\model\Goods' : '\app\member\model\MemberLevel';
                // 查询商品信息
                $goods_info = (new $model)->where('id', $order_info->goods_id)->find();
                if ($goods_info) {
                    switch ($order_info->type) {
                        case 0:
                            // 调整用户会员组
                            $user = $MemberModel->where('id', $order_info->member_id)->find();
                            if ($user) {
                                $user->level_id = $goods_info->id;
                                $user->level_name = $goods_info->level_name;
                                $exp_time = $user->exp_time;
                                $create_time = $user->create_time;
                                if (is_string($exp_time)) {
                                    $exp_time = strtotime($user->exp_time);
                                }
                                if (is_string($create_time)) {
                                    $create_time = strtotime($user->create_time);
                                }
                                if (empty($exp_time) || ($exp_time == $create_time) || ($exp_time < time())) {
                                    $user->exp_time = strtotime(date('Y-m-d H:i:s', strtotime("{$goods_info->expire} day", time())));
                                } else {
                                    $user->exp_time = strtotime(date('Y-m-d H:i:s', strtotime("{$goods_info->expire} day", $exp_time)));
                                }
                                $user->save();
                            }
                            break;
                    }
                }
            }
        }
    }
}
