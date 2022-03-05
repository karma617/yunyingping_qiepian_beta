<?php

namespace app\goods\api;

use app\member\api\MemberInit;
use app\goods\model\GoodsOrder as GoodsOrderModel;
use app\goods\validate\Order as OrderValidate;

class Order extends MemberInit
{
    public function initialize()
    {
        parent::initialize();
        $this->GoodsOrderModel = new GoodsOrderModel();
        $this->OrderValidate = new OrderValidate();
    }

    public function buy()
    {
        $data = $this->params;
        if ($this->OrderValidate->scene('buy')->check($data) !== true) {
            return $this->_error($this->OrderValidate->getError());
        }
        $type = isset($data['type']) ? $data['type'] : 0;
        $model = $type == 1 ? '\app\goods\model\Goods' : '\app\member\model\MemberLevel';
        // 查询商品信息
        $info = (new $model)->where('id', $data['id'])->find();
        if (empty($info)) {
            return $this->_error('未获取到商品信息');
        }

        switch ($type) {
            case 0:
                $goods_name = $info['level_name'];
                break;
            case 1:
                $goods_name = $info['goods_name'];
                break;
        }

        $order_sn = unique_order_number($this->member['id']);

        $this->GoodsOrderModel->startTrans();
        try {
            $_data = [
                'order_sn' => $order_sn,
                'member_id' => $this->member['id'],
                'goods_id' => $info->id,
                'type' => $type,
                'price' => $info->limit_price > 0 ? $info->limit_price : $info->price,
                'goods_name' => $goods_name,
                'remark' => $info->intro,
            ];
            $this->GoodsOrderModel->save($_data);
            $this->GoodsOrderModel->commit();
        } catch (\Throwable $th) {
            $this->GoodsOrderModel->rollback();
        }
        return $this->_success('下单成功', $order_sn);
    }

    public function getOrderInfo()
    {
        $data = $this->params;
        if ($this->OrderValidate->scene('getOrderInfo')->check($data) !== true) {
            return $this->_error($this->OrderValidate->getError());
        }
        $info = $this->GoodsOrderModel->where('order_sn', $data['order_sn'])->find();
        if (!$info) {
            return $this->_error('未获取到订单信息');
        }
        return $this->_success('获取成功', $info);
    }

    public function getOrderStatus()
    {
        $data = $this->params;
        if ($this->OrderValidate->scene('getOrderInfo')->check($data) !== true) {
            return $this->_error($this->OrderValidate->getError());
        }
        $status = $this->GoodsOrderModel->where('order_sn', $data['order_sn'])->value('status');
        $status = $status === 0 ? 'waiting' : 'success';
        return $this->_success('获取成功', $status);
    }
}
