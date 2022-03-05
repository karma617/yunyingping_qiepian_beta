<?php

namespace app\one_pay\admin;
use app\system\admin\Admin;
use app\one_pay\model\OnePayPayment as PaymentModel;
use app\one_pay\model\OnePayLog as LogModel;

class Logs extends Admin
{
    protected $oneModel = 'one_pay_log';
    /**
     * 支付日志管理
     * @return mixed
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $map    = $data = [];
            $page   = $this->request->param('page/d', 1);
            $limit  = $this->request->param('limit/d', 15);
            $date   = $this->request->param('date');
            $method = $this->request->param('method');
            $uid    = $this->request->param('uid');
            $status = $this->request->param('status');
            $orderNo= $this->request->param('order_no');
            $tradeNo= $this->request->param('trade_no');
                
            if ($date) {
                $expl = explode(' - ', $date);
                $map['ctime'] = ['between', [strtotime($expl[0]), strtotime($expl[1].' 23:59:59')]];
            }

            if ($uid) {
                $map['uid'] = $uid;
            }

            if ($method) {
                $map['method'] = $method;
            }

            if (is_numeric($status)) {
                $map['status'] = $status;
            }

            if ($orderNo) {
                $map['order_no'] = $orderNo;
            }

            if ($tradeNo) {
                $map['trade_no'] = $tradeNo;
            }

            $data['data']   = LogModel::where($map)->order('id desc')->page($page)->limit($limit)->select();
            $data['count']  = LogModel::where($map)->count('id');
            $data['code']   = 0;

            return json($data);
        }

        $tabData = [
            'column' => [
                ['label' => '在线支付', 'name' => 'pay', 'url' => url('one_pay/index/index')],
                ['label' => '支付日志', 'name' => 'log', 'url' => url('one_pay/logs/index')],
            ],
            'current' => 'log',
        ];
        // $payment =  PaymentModel::field('code as value,title as label')->select();
        $payment =  PaymentModel::column('code,title');
        $this->assign('payment',$payment);
        $this->assign('tabData', $tabData);
        return $this->fetch();
    }
}