<?php

namespace app\one_pay\driver\alipay_app;
use app\one_pay\driver\BasicAliPay;
use app\one_pay\driver\PayMentInterFace;

class alipay_app extends BasicAliPay implements PayMentInterFace
{
    public function __construct($options = [])
    {
        parent::__construct($options);
        //兼容手机和PC
        $this->options['method']=  'alipay.trade.app.pay';
        $this->params['product_code']  = 'QUICK_MSECURITY_PAY' ;

    }
    
    /* 支付提交接口 */
    public function _submit($options){
        parent::applyData($options);
        return http_build_query($this->options);
    }
    /* 同步通知接口 */
    public function _sync($param){
        return parent::notify($param);
    }
    /* 异步通知接口 */
    public function _async($param){
        return parent::notify($param);
    }
    /* 退款提交接口 */
    public function _refundSubmit($param){
        return parent::refund($param);
    }
    /* 同步退款通知接口 */
    public function _syncRefund($param){

    }
    /* 异步退款通知接口 */
    public function _asyncRefund($param){

    }
}
