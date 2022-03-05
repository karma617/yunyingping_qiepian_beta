<?php

namespace app\member\model;

use think\model\concern\SoftDelete;
use app\member\model\MemberLevel;

class Member extends Base
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
    protected $type = [
        'login_time'    =>  'timestamp',
        'last_login_time'     =>  'timestamp',
        'exp_time'     =>  'timestamp',
        'ext_conifg'     =>  'json',
    ];

    public static function init()
    {
        // //更新前
        self::event('before_insert', function ($obj) {
            $expire = model('MemberLevel')->where('id', $obj->level_id)->value('expire');
            $obj->exp_time = strtotime(date('Y-m-d H:i:s', strtotime("+{$expire} day")));
        });
    }

    /**
     * 重置密码
     * @param string $password
     * @param $condition
     * @return array
     */
    public function resetMemberPassword($password = '123456', $condition)
    {
        $salt = random(6, 0);
        $res = $this->update([
            'salt' => $salt,
            'password' => md5($salt . $password)
        ], $condition);
        runhook('repwd_after');
        return $res;
    }

    public function getExptimeTextAttr($val, $data)
    {
        if ($data['exp_time'] > 0) {
            $day1 = time();
            $day2 = $data['exp_time'];
            $cha = $data['exp_time'] - $day1;
            $cha = date_diff(date_create(date('Ymd', $day1)), date_create(date('Ymd', $day2)) ,true);
            return $cha->days;
        }
        return $data['level_id'] > 1 ? '已到期' : '无限期';
    }
}
