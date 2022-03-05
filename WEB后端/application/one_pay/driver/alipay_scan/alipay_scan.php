<?php

namespace app\one_pay\driver\alipay_scan;
use app\one_pay\driver\BasicAliPay;
use app\one_pay\driver\PayMentInterFace;

class alipay_scan extends BasicAliPay implements PayMentInterFace
{
    public function __construct($options = [])
    {
        parent::__construct($options);
        //兼容手机和PC
        $this->options['method']=  'alipay.trade.precreate';
        $this->params['product_code']  = 'FACE_TO_FACE_PAYMENT' ;

    }
    
    /* 支付提交接口 */
    public function _submit($options){
        parent::applyData($options);
        return $this->getResult($options);
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
