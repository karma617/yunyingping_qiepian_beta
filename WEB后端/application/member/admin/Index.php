<?php

namespace app\member\admin;

use app\system\admin\Admin;

class Index extends Admin
{

    protected $oneModel = 'Member';
    protected $oneValidate = 'app\member\validate\Member';
    protected $oneAddScene = 'memberAdd'; //添加数据验证场景名
    protected $oneEditScene = 'memberEdit'; //更新数据验证场景名

    protected function initialize()
    {
        parent::initialize();
        $this->member_model = model('Member');
    }
    //首页
    public function index()
    {
        if ($this->request->isAjax()) {
            $level_id = input('level_id/d', 0);
            $keyword = input('keyword/s');
            $condition = [];
            $page = input('page/d', 1);
            $limit = input('limit/d', 15);
            $order = 'id DESC';
            $field = '';
            if ($keyword) {
                $condition[] = ['username|nickname|mobile|email', 'like', "%" . $keyword . "%"];
            }
            //会员等级
            if ($level_id != 0) {
                $condition[] = ['level_id', '=', $level_id];
            }

            $result = model('Member')->pageList($condition, $field, $order, $page, $limit, ['exptime_text']);
            return $this->success('获取成功', '', $result);
        }
        $_level_select = model('memberLevel')->getSelect();
        return $this->assign(compact('_level_select'))->fetch();
    }
    //编辑
    public function edit()
    {
        $id = input('id/d', 0);
        $formData = [];
        if ($id > 0) {
            $formData = $this->member_model->find($id)->toArray();
        }
        if ($this->request->isAjax()) {
            if ($id > 0) {
                parent::edit();
            } else {
                //新增加用户强插密码
                $salt = random(6, 0);
                $_password = input('password');
                $password = md5($salt . $_password);
                $this->request->withPost(array_merge($_POST, ['salt' => $salt, 'password' => $password, 'reg_time' => time(), 'login_time' => time(), 'last_login_time' => time()]));
                parent::add();
            }
            $this->success('操作成功');
        }
        $_level_select = model('memberLevel')->getSelect();
        return $this->assign(compact('formData', '_level_select'))->fetch('form');
    }

    //重置会员密码
    public function modify_pwd($id)
    {
        $password = random(6, 0);
        $map = 'id=' . $id;
        if ($this->member_model->resetMemberPassword($password, $map)) {
            return $this->success('操作成功,新密码为:' . $password);
        } else {
            return $this->error($this->member_model->getError());
        }
    }
    //修改帐户
    public function modify_account($member_id, $account_type, $account_data, $remark)
    {
        $from_type = 'adjust';
        try {
            model('memberAccount')->ModifyMemberAccount($member_id, $account_type, $account_data, $from_type, $remark);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
        $this->success('操作成功');
    }
}
