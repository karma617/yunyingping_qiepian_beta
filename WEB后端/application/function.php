<?php
// 为方便系统升级，二次开发中用到的公共函数请写在此文件，禁止修改common.php文件
// ===== 系统升级时此文件永远不会被覆盖 =====

if (!function_exists('unique_order_number')) {
    /**
     * 生成订单号
     *
     * @param [type] $id 唯一因子
     * @return void
     * @Description
     * @author 617 <email：723875993@qq.com>
     */
    function unique_order_number($id = '')
    {
        $timestamp = time();
        $y = date('Ymd', $timestamp);
        $z = date('z', $timestamp);
        $key = str_pad($id, 6, 'X', STR_PAD_LEFT);
        $num = substr_count($key, 'X');
        $ramdom_str = random($num);
        $key = $ramdom_str . $id;
        return $y . $key . str_pad($z, 3, '0', STR_PAD_LEFT) . str_pad(random(6), 5, '0', STR_PAD_LEFT);
    }
}