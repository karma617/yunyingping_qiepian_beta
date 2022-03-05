<?php

namespace app\member\model;

use app\member\model\Member;
use think\Db;

class MemberAccount extends Base
{
    //账户类型
    private $account_type = [
        'balance' => '余额',
        'frozen_balance' => '冻结余额',
        'point' => '积分',
        'growth' => '成长值',
        'exp_time' => '剩余有效天数'
    ];
    //来源类型
    private $from_type = [
        'adjust' => '调整',
        'order' => '消费',
        'upgrade' => '升级',
        'refund' => '退还',
        'withdraw' => '提现'
    ];
    // 模型事件
    public static function init()
    {
    }
    public function getAccountTypeTextAttr($value, $data)
    {
        return $this->account_type[$data['account_type']];
    }
    /**
     * 修改会员帐户信息
     *
     * @param [type] $member_id 用户ID
     * @param [type] $account_type 账户类型
     * @param [type] $account_data 账户数据
     * @param [type] $from_type 来源类型
     * @param [type] $remark 备注信息
     * @return void
     * @author Leo <13708867890>
     * @since 2021-03-30 12:27:39
     */
    public function ModifyMemberAccount($member_id, $account_type, $account_data, $from_type, $remark = '')
    {
        $member_account = model('member')->field($account_type . ',username, mobile, email')->where('id=' . $member_id)->find();
        if (!$member_account) throw new \Exception("用户信息不存在", 1);
        // 白名单
        $white_list = ['exp_time'];
        if (in_array($account_type, $white_list)) {
            $account_data = (int)$account_data;
            if (empty($member_account[$account_type])) {
                $account_new_data = strtotime(date('Y-m-d', strtotime ("{$account_data} day", time())));
            } else {
                $account_new_data = strtotime(date('Y-m-d', strtotime ("{$account_data} day", strtotime($member_account[$account_type]))));
            }
            
            if (date('Ymd', $account_new_data) < date('Ymd', time())) {
                throw new \Exception($this->account_type[$account_type] . '不足', 1);
            }
        } else {
            $account_new_data = (float) $member_account[$account_type] + (float) $account_data;
        }

        if ((float) $account_new_data < 0) throw new \Exception($this->account_type[$account_type] . '不足', 1);
        if ((float) $account_new_data == $member_account[$account_type]) throw new \Exception('数据无变化', 1);
        $type_info = $this->from_type[$from_type];
        Db::transaction(function () use ($type_info, $member_id, $account_type, $account_data, $from_type, $remark, $member_account, $account_new_data) {
            //日志
            $data = array(
                'member_id' => $member_id,
                'account_type' => $account_type,
                'account_data' => $account_data,
                'from_type' => $from_type,
                'type_name' => $type_info,
                'username' => $member_account['username'],
                'mobile' => $member_account['mobile'],
                'email' => $member_account['email'],
                'remark' => $remark
            );
            $rs = model('member_account')->create($data);

            //账户更新
            model('member')->update([
                $account_type => $account_new_data
            ], [
                'id' => $member_id
            ]);
            runhook('account_modify_after', $rs);
        });
    }
}
