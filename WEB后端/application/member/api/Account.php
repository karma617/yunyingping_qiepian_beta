<?php
namespace app\member\api;
use app\member\model\Login as LoginModel;
use app\member\model\MemberAccount;

class Account extends MemberInit
{
    public function initialize() {
        parent::initialize();
    }
    /**
     * 帐户详情
     *
     * @return void
     * @author Leo <13708867890>
     * @since 2021-03-30 22:35:36
     */
    public function get_detail()
    {
        $condition= [];
        $page=input('page/d',1);
        $limit=input('limit/d',15);
        $order='id DESC';
        $field='';
        $append = ['account_type_text'];
        $hidden = ['member_id'];
        $model = new MemberAccount;
        $rs = $model->pageList($condition,$field, $order,$page,$limit,$append,$hidden);
        $this->_success('',$rs);
    }
}