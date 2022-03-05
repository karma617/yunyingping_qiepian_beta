<?php
namespace app\member\admin;
use app\system\admin\Admin;

class Account extends Admin
{

    protected $oneModel = 'MemberAccount';

    protected function initialize()
    {
        parent::initialize();
        $this->member_account_model = model('MemberAccount');
    }

    public function index()
    {   
        $id = input('id');
        $member = model('member')->where('id='.$id)->field('password,salt',true)->find()->toArray();
        if ($this->request->isAjax()) {
            $account_type = input('account_type', '');
            list($start_date,$end_date) = input('time_rage/a',[0,0]);
            $condition= [];
            $page=input('page/d',1);
            $limit=input('limit/d',15);
            $order='id DESC';
            $field='';
            $append = ['account_type_text'];

            $condition[] = [ 'member_id', '=', $id ];
            //账户类型
            if ($account_type != '') {
                $condition[] = [ 'account_type', '=', $account_type ];
            }
            //发生时间
            if ($start_date != 0 && $end_date != 0) {
                $condition[] = [ 'create_time', 'between', [ strtotime($start_date), strtotime($end_date)+86400-1 ] ];
            } 
            $result = $this->member_account_model->pageList($condition,$field, $order,$page,$limit,$append);
            return $this->success('获取成功', '', $result);
        }
        return $this->assign(compact('member'))->fetch();
    }

}