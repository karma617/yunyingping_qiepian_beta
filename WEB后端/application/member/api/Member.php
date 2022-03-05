<?php

namespace app\member\api;

use app\member\model\Login as LoginModel;
use app\member\model\MemberAccount;
use app\member\model\MemberFile;
use one\Http;

class Member extends MemberInit
{
    public function initialize()
    {
        parent::initialize();
        $this->MemberFileModel = new MemberFile();
    }
    //基础信息
    public function get_info()
    {
        $map = [];
        $map[] = ['userId', 'eq', $this->member['id']];
        $list = $this->MemberFileModel->field('fileId,delete_time,userId')->where($map)->select();
        $this->member['file_count'] = count($list);
        $this->member['token'] = $this->token;
        $res = runhook('create_token_afert', $this->member, true);
        return $this->_success('', $res[0]);
    }

    public function logout()
    {
        $token = md5($this->member['id'] . $this->member['salt']);
        cache($token, null);
        return $this->_success('退出成功');
    }

    //修改会员头像
    public function modify_headimg()
    {
        # code...
    }
    //修改昵称
    public function modify_nickname()
    {
        # code...
    }
    //修改手机号
    public function modify_mobile()
    {
        # code...
    }
    //修改密码
    public function modify_password()
    {
        # code...
    }
    //修改支付密码
    public function modify_pay_password()
    {
        # code...
    }
    //修改扩展信息
    public function modify_setting()
    {
        $data = $this->params;
        foreach ($data as $key => $val) {
            $this->memberObj[$key] = json_decode($val);
        }
        $this->memberObj->save();

        $ext_conifg = json_decode($data['ext_conifg'], true);
        $api_url = $ext_conifg['api_url'];
        unset($ext_conifg['api_url']);
        $ext_conifg['hosts'] = explode("\n", $ext_conifg['hosts']);
        $ext_conifg['token'] = $this->token;
        $param = [
            'type' => 'setconfig',
            'ext_conifg' => $ext_conifg
        ];
        $rs = Http::post($api_url, $param);
        if ($rs === 'ok') {
            return $this->_success('保存成功');
        }
        return $this->_error('授权域名同步失败，详情：' . $rs);
    }

    public function checkHost()
    {
        $data = $this->params;
        $param = [
            'type' => 'web_test',
            'token' => $this->token,
        ];
        $rs = Http::get($data['url'], $param);
        if ($rs === 'ok') {
            return $this->_success('通讯成功');
        }
        return $this->_error('通讯失败，详情：' . $rs);
    }
}
