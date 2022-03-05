<?php

namespace app\member\hook;

use app\member\model\MemberAccount;
use app\member\model\MemberLevel as LevelModel;

class Member
{
    //登录后
    public function loginAfert($params)
    {
        //成长值+1
        // $account = new MemberAccount;
        // $account->ModifyMemberAccount($params['id'],'growth',1,'upgrade','登录奖励');
    }
    //登录创建token后
    public function createTokenAfert($params)
    {
        //添加删除返回接口中的数据
        unset(
            $params['password'],
            $params['salt'],
            $params['qq_openid'],
            $params['wx_openid'],
            $params['weapp_openid'],
            $params['wx_unionid'],
            $params['ali_openid'],
            $params['baidu_openid'],
            $params['toutiao_openid'],
            $params['douyin_openid'],
            $params['status'],
            $params['delete_time']
        );
        return $params;
    }
    //注册后
    public function registerAfert($params)
    {
        //初始等级处理
        $account = new MemberAccount;
        $level = LevelModel::where([['id', '=', $params['level_id']], ['status', '=', '1']])->find();
        if (!$level) {
            return false;
        }
        //奖励积分
        if ($level['send_point'] > 0) {
            $account->ModifyMemberAccount($params['id'], 'point', $level['send_point'], 'upgrade', '注册初始奖励');
        }
        //奖励余额
        if ($level['send_balance'] > 0) {
            $account->ModifyMemberAccount($params['id'], 'balance', $level['send_balance'], 'upgrade', '注册初始奖励');
        }
    }
    //修改密码后
    public function repwdAfter($params)
    {
        # code...
    }
    public function accountModifyAfter($params)
    {
        //TODO帐户变动后等级改变
    }
}
