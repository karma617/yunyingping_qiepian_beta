<?php

namespace app\one_pay\home;

use app\member\api\MemberInit;
use app\one_pay\driver\Factory;
use app\one_pay\model\OnePayLog as PayLogModel;
use app\one_pay\model\OnePayPayment as PaymentModel;
use app\goods\model\GoodsOrder as GoodsOrderModel;

/**
 * 支付请求控制器
 * @package app\one_pay\home
 */
class Index extends MemberInit
{
    /**
     * 发起支付请求
     *
     * @param string $method *[wechat_qr,wechat_js,wechat_mini,wechat_mweb,wechat_app,alipay,alipay_app,alipay_scan]
     * @param string $order_no 订单号
     * @param integer $money 金额 微信需要*100 生产环境需要高精度处理 intval(strval($val*100)); 
     * @param string $body 支付宝描述 微信描述
     * @param string $subject 支付宝标题 转换为微信detail
     * @param integer $uid 用户标识
     * @param string $back_url 返回地址
     * @return void
     */
    public function apply()
    {
        $data = $this->params;

        $method = $data['method'];
        $order_no = $data['order_no'];

        if (empty($method) || empty($order_no)) {
            return $this->_error("缺少必要参数");
        }

        // 查询订单金额
        $info = (new GoodsOrderModel)->where('order_sn', $order_no)->find();
        if (empty($info)) {
            return $this->_error("未找到订单信息");
        }

        $money = $info['price'];
        if (in_array($method, ['wechat_qr','wechat_js','wechat_mini','wechat_mweb','wechat_app'])) {
            $money = intval(strval($money*100));
        }

        $body = empty($info['remark']) ? $info['goods_name'] : $info['remark'];
        $subject = $info['goods_name'];
        $uid = $this->member['id'];

        $param             = [];
        $param['method']   = $method;
        $param['money']    = $money;
        $param['uid']      = $uid;
        $param['order_no'] = $order_no;
        $param['body']     = $body;
        $param['subject']  = $subject;

        // 保存请求数据
        $_data                = [];
        $_data['order_no']    = $param['order_no'];
        $_data['type']        = 1;
        $_data['method']      = $method;
        $_data['money']       = $param['money'];
        $_data['bank']        = isset($param['bank']) ? $param['bank'] : '';
        $_data['request']     = json_encode($param);
        $_data['uid']         =  $param['uid'];

        $res = PayLogModel::create($_data);

        //实例化支付
        $factory = new Factory($method);
        if(strstr($method,'alipay')){
            $rs = $factory->__call('_submit', [
                'out_trade_no' => $param['order_no'],
                'total_amount' => $param['money'],
                'body'         => $param['body'],
                'subject'      => $param['subject'],
            ]);
        }
        if(strstr($method,'wechat')){
            $rs = $factory->__call('_submit', [
                'out_trade_no' => $param['order_no'],
                'total_fee' => $param['money']*100,
                'body'         => $param['body'],
                'detail'      => $param['subject'],
            ]);
        }

        //以下方式返回参数到前端拼接

        if (in_array($method, ['wechat_qr','wechat_mweb','wechat_js', 'wechat_app', 'alipay_app','alipay_scan'])) {
            return json($rs);
        }
       
        //直接输出 支付宝表单
        echo $rs;
    }

    /**
     * 检查订单支付状态
     */
    public function checkStatus()
    {
        $orderNo = $this->request->param('order_no');

        if (PayLogModel::where('order_no', $orderNo)->where('status', '=', 2)->find()) {
            return $this->success('支付成功');
        }
        return $this->error('待支付');
    }

    public function refund()
    {
        $factory = new Factory('wechat_qr');
        $rs = $factory->__call('_refundSubmit', [
            'out_trade_no' => '2020122620073705186',
            'out_refund_no'=>order_number(),
            'total_fee' => 10,
            'refund_fee' => 10,
        ]);
        
        var_dump($rs);
    }
}
