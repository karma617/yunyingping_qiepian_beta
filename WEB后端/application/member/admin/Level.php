<?php
namespace app\member\admin;
use app\system\admin\Admin;

class Level extends Admin
{

    protected $oneModel = 'MemberLevel';

    protected function initialize()
    {
        parent::initialize();
        $this->member_level_model = model('MemberLevel');
    }

    public function index()
    {
        if ($this->request->isAjax()) {
            $condition= [];
            $page=input('page/d',1);
            $limit=input('limit/d',15);
            $order='';
            $field='';
            $append = ['member_count'];
            $result = $this->member_level_model->pageList($condition,$field, $order,$page,$limit,$append);
            return $this->success('获取成功', '', $result);
        }
        return $this->fetch();
    }

    public function edit()
    {
        $id=input('id/d',0);
        $formData = [];
        if($id>0){
            $formData = $this->member_level_model->find($id)->toArray();
        }
        if ($this->request->isAjax()) {
            if($id>0){
                parent::edit();
            }else{
                parent::add();
            }
            $this->success('操作成功');
        }
        $_apiKeys = config('api.all_api');
        $apiKeys = [];
        foreach ($_apiKeys as $key => $value) {
            $apiKeys[$key]['label'] = $value['tag'];
            $apiKeys[$key]['value'] = $key;
        }
        return $this->assign(compact('formData', 'apiKeys'))->fetch('form');
    }

    public function del()
    {
        $id=input('id/d',0);
        if($this->member_level_model->deleteMemberLevel($id)){
            $this->success('操作成功');
        }else{
            return $this->error($this->member_level_model->getError());
        }
    }
}