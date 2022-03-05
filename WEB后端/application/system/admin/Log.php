<?php

namespace app\system\admin;

use app\system\model\SystemLog as LogModel;

/**
 * 日志管理控制器
 * @package app\system\admin
 */
class Log extends Admin
{
    protected $oneTable = 'SystemLog';

    /**
     * 日志首页
     * @return mixed
     */
    public function index()
    {
        if ($this->request->isAjax()) {

            $where  = $data = [];
            $page   = $this->request->param('page/d', 1);
            $limit  = $this->request->param('limit/d', 15);
            $uid    = $this->request->param('uid/d');

            if ($uid) {
                $where['uid'] = $uid;
            }

            $data['data']   = LogModel::with('user')->where($where)->page($page)->order('update_time desc')->limit($limit)->select();
            $data['count']  = LogModel::where($where)->count('id');
            $data['code']   = 0;

            return json($data);
        }

        return $this->fetch();
    }
    /**
     * 清空日志
     * @return mixed
     */
    public function clear()
    {
        if (!LogModel::where('id > 0')->delete()) {
            return $this->error('日志清空失败');
        }
        return $this->success('日志清空成功');
    }
}
