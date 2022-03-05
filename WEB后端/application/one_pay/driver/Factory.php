<?php

namespace app\one_pay\driver;
use app\one_pay\model\OnePayPayment as PayPayment;
class Factory
{

    public function __construct($code = '')
    {
        defined('PAY_CODE') OR define('PAY_CODE', $code);
        $this->adapter($code);
    }


    /**
     * 构造适配器
     * @param  $code 支付平台code
     * @param  $config 支付平台配置
     */
    public function adapter($code = '')
    {
        if (empty($code)) return false;
        
        $payment = PayPayment::lists($code);
        if (!$payment) {
            throw new \Exception("['.$code.']支付方式未安装或未开启！", 1);
        }
        
        $class = '\\app\\one_pay\\driver\\'.$code.'\\'.$code;

        if (!class_exists($class)) {
            throw new \Exception('缺少['.$code.']支付驱动！', 1);

        }
        $this->instance = new $class($payment['config']['config']);
        return $this->instance;
    }
    
    public function __call($method_name, $method_args) {
        if (method_exists($this, $method_name)) {
            return call_user_func_array(array(& $this, $method_name), [$method_args]);
        } elseif (
            !empty($this->instance)
            && ($this->instance instanceof PayMentInterFace)
            && method_exists($this->instance, $method_name) ) {
            return call_user_func_array(array(& $this->instance, $method_name), [$method_args]);
        }
    }  
}