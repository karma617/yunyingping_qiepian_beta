<?php

namespace app\one_pay\model;

use think\Model;

class OnePayPayment extends Model
{

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

    /**
     * 获取所有支付插件
     * @param bool $update 是否更新缓存
     * @param array $data 入库数据
     * @return array
     */
    public static function lists($code = '', $update = true, $status = 1)
    {
        $result = cache('pay_payment');
        if (!$result || $update == true) {
            $map = [];
            if (is_numeric($status)) {
                $map[] = ['status', '=', $status];
            }
            $result = self::where($map)->order('sort asc')->column('code,title,applies,config', 'code');
            foreach ($result as $k => &$v) {
                if ($v['config']) {
                    $v['config'] = json_decode($v['config'], 1);
                }
            }
            cache('pay_payment', $result);
        }

        if ($code && isset($result[$code])) {
            return $result[$code];
        } else if ($code) {
            return false;
        }

        return $result;
    }

    /**
     * 获取可用的支付方式，自动识别wap 和 PC
     * @return array
     */
    public static function available($applies = '')
    {
        $agent = request()->server('HTTP_USER_AGENT');
        $where = [];
        $where[] = ['status', '=', 1];
        if ($applies) {
            $where[] = ['applies', 'like', '%'.$applies.'%'];
        } else {
            if (request()->isMobile()) {
                if (strpos($agent, 'MicroMessenger') !== false) {
                    $where[] = ['applies', 'like', '%wechat%'];
                } else {
                    $where[] = ['applies', 'like', '%wap%'];
                }
            } else {
                $where[] = ['applies', 'like', '%pc%'];
            }
        }
        
        return self::where($where)->order('sort asc')->cache(true)->column('code,title,abbrev', 'code');
    }
}