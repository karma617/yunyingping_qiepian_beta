<?php

namespace app\member\model;

use app\member\model\Member as MemberModel;
use app\member\model\MemberLevel as LevelModel;

class Login extends Base
{

    /**
     * 普通登录
     * account password 必传
     * @param [type] $data
     * @return void
     * @author Leo <13708867890>
     * @since 2021-03-30 21:09:08
     */
    public function login($data, $from = 'Yanqige-web')
    {
        $rs = [];

        if ((!isset($data['account']) && !isset($data['username'])) || !isset($data['password'])) {
            throw new \Exception("帐号或密码错误", 1);
        }
        $account = isset($data['account']) ? $data['account'] : $data['username'];

        $rs = MemberModel::where([['username|mobile|email', '=', $account]])->find();
        if (!$rs) {
            throw new \Exception("帐号不存在", 1);
        }
        if ($rs['status'] == 0) {
            throw new \Exception("帐号已禁用", 1);
        }

        if ($rs['password'] !== md5($rs['salt'] . $data['password'])) {
            throw new \Exception("密码错误", 1);
        }

        // 判断账号是否有权限登录使用
        if ($from != 'Yanqige-web') {
            $vip = config("app_config.vip_limit");
            $vip = explode(',', $vip);
            if (!in_array($rs['level_id'], $vip)) {
                throw new \Exception("当前会员组无权限，请到网页端升级会员", 1);
            }
            // 判断是否到期
            if (empty($rs['exp_time']) || strtotime($rs['exp_time']) <= time()) {
                throw new \Exception("您的会员已到期，请到网页端续费", 1);
            }
        }

        //更新登录时间
        $rs->update([
            'login_time' => time(),
            'login_num' => ['INC', 1],
            'login_ip' => request()->ip(),
            'last_login_time' => $rs['login_time'],
            'last_login_ip' => $rs['login_ip'],
            'last_login_type' => $rs['login_type']
        ], [['id', '=', $rs['id']]]);

        $rs = $rs->append(['exptime_text'])->toArray();

        runhook('login_afert', $rs);
        return $rs;
    }
    /**
     * 三方自动登录
     *
     * @param [type] $tag
     * @param [type] $open_id
     * @param array $data
     * @return void
     * @author Leo <13708867890>
     * @since 2021-03-30 21:08:14
     */
    public function authLogin($tag, $open_id, $data = [])
    {
        $rs = [];
        $tag_arr = ['wx_unionid', 'wx_openid', 'weapp_openid', 'qq_openid', 'ali_openid', 'baidu_openid', 'toutiao_openid'];
        if (!in_array($tag, $tag_arr)) {
            throw new \Exception("不支持此种登录方式", 1);
        }
        $map[] = [$tag, '=', $open_id];
        $rs = MemberModel::where($map)->field('password,salt', true)->find();

        //无此用户自动注册
        $data[$tag] = $open_id;
        if (!$rs) {
            //随机用户名
            $data['nickname'] = isset($data['nickname']) ? $data['nickname'] : 'A' . random(6, 0);
            $data['username'] = isset($data['username']) ? $data['username'] : 'U' . random(6, 0);
            $rs = $this->register($data, $data);
        }
        $rs = MemberModel::where('id=' . $rs['id'])->field('password,salt', true)->find();
        if ($rs['status'] == 0) {
            throw new \Exception("用户已禁用", 1);
        }
        //更新登录时间
        $rs->update([
            'login_time' => time(),
            'login_num' => ['INC', 1],
            'login_ip' => request()->ip(),
            'last_login_time' => $rs['login_time'],
            'last_login_ip' => $rs['login_ip'],
            'last_login_type' => $rs['login_type']
        ], [['id', '=', $rs['id']]]);
        runhook('login_afert', $rs);
        return $rs;
    }

    /**
     * 注册用户
     *
     * @param [type] $data 必要数据 account password
     * @param [type] $params 附加数据
     * @return void
     * @author Leo <13708867890>
     * @since 2021-04-03 11:49:33
     */
    public function register($data, $params)
    {
        //处理密码
        $salt = isset($data['salt']) ? $data['salt'] : random(6, 0);
        $_password = isset($data['password']) ? $data['password'] : random(6, 0);
        $password = md5($salt . $_password);
        //默认会员级别
        $member_level_info = LevelModel::where('is_default=1')->find();
        $data_reg = [
            'username' => $data['username'],
            'nickname' => $data['nickname'],
            'qq' => $data['qq'],
            'email' => isset($data['email']) ? $data['email'] : '',
            'mobile' => isset($data['mobile']) ? $data['mobile'] : '',
            'salt' => $salt,
            'password' => $password,
            'qq_openid' => isset($data['qq_openid']) ? $data['qq_openid'] : '',
            'wx_openid' => isset($data['wx_openid']) ? $data['wx_openid'] : '',
            'weapp_openid' => isset($data['weapp_openid']) ? $data['weapp_openid'] : '',
            'wx_unionid' => isset($data['wx_unionid']) ? $data['wx_unionid'] : '',
            'ali_openid' => isset($data['ali_openid']) ? $data['ali_openid'] : '',
            'baidu_openid' => isset($data['baidu_openid']) ? $data['baidu_openid'] : '',
            'toutiao_openid' => isset($data['toutiao_openid']) ? $data['toutiao_openid'] : '',
            'headimg' => isset($data['avatarUrl']) ? $data['avatarUrl'] : '',
            'level_id' => $member_level_info['id'],
            'level_name' => $member_level_info['level_name'],
            'reg_time' => time(),
            'login_time' => time(),
            'last_login_time' => time()
        ];
        $rs = MemberModel::create($data_reg);
        //允许传递的其它字段
        $rs->allowField(['username', 'nickname', 'headimg', 'realname', 'idcard', 'sex', 'location', 'birthday'])->save($params);
        runhook('register_afert', $rs);
        return $rs;
    }
}
