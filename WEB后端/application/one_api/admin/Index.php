<?php
namespace app\one_api\admin;
use app\system\admin\Admin;
use app\one_api\model\OneApi as apiModel;
use think\Db;

class Index extends Admin
{
    protected $oneModel = 'one_api';//模型名称[通用添加、修改专用]
    protected $oneTable = '';//表名称[通用添加、修改专用]
    protected $oneAddScene = 'apiAdd';//添加数据验证场景名
    protected $oneEditScene = 'apiEdit';//更新数据验证场景名

    public function index()
    {

        if ($this->request->isAjax()) {

            $where      = [];
            $page       = $this->request->param('page/d', 1);
            $limit      = $this->request->param('limit/d', 15);
           
            $data['data'] = apiModel::
                            where($where)
                            ->page($page)
                            ->limit($limit)
                            ->order('id desc')
                            ->select();

            $data['count'] = apiModel::where($where)->count('id');
            $data['code'] = 0;

            return json($data);
        }
        return $this->fetch();
    }
}