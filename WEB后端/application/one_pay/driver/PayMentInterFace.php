<?php
namespace app\one_pay\driver;

interface PayMentInterFace
{
    /* 支付提交接口 */
    public function _submit($param);
    /* 同步通知接口 */
    public function _sync($param);
    /* 异步通知接口 */
    public function _async($param);
    /* 退款提交接口 */
    public function _refundSubmit($param);
    /* 同步退款通知接口 */
    public function _syncRefund($param);
    /* 异步退款通知接口 */
    public function _asyncRefund($param);
}