<?php

namespace app\member\api;

use app\member\model\MemberFile as MemberFileModel;

class MemberFile extends MemberInit
{

    public function fileLists()
    {
        $data = $this->params;
        $page = $data['page'];
        $limit = $data['limit'];

        $where = "(userId = {$this->member['id']} or (ext_ids <> '' and find_in_set({$this->member['id']}, ext_ids) ) )";

        if (isset($data['keywords']) && !empty($data['keywords'])) {
            $where .= " and (fileName like '%{$data['keywords']}%' or fileSortName like '%{$data['keywords']}%')";
        }
        $list = (new MemberFileModel)->pageList($where, true, 'time desc', $page, $limit);
        return $this->_success('获取成功', $list);
    }

    public function del()
    {
        $data = $this->params;
        $map = [];
        $map[] = ['fileId', 'eq', $data['id']];

        // 查询是否有修改权
        if (MemberFileModel::where($map)->value('userId') != $this->member['id']) {
            return $this->_error('该文件不是您上传的，无权操作');
        }

        $file = MemberFileModel::get(['fileId' => $data['id']]);
        $file->delete();
        return $this->_success('删除成功');
    }

    public function addFile()
    {
        $data = $this->params;

        // 校验用户是否已过期，是否有权限
        $vip_id = config("app_config.vip_limit");
        $vip_id = explode(',', $vip_id);
        $exp_time = $this->member['exp_time'] != 0 ? strtotime($this->member['exp_time']) : 0;
        if (!in_array($this->member['level_id'], $vip_id) || ($exp_time > 0 && $exp_time < time())) {
            return $this->_error('您的会员已到期，请续费！' . $this->member['exp_time']);
        }

        $name = empty($data['name']) ? '未命名' : $data['name'];
        $url = empty($data['url']) ? "无链接" : $data['url'];
        $from = empty($data['referer']) ? "无来源" : urldecode($data['referer']);
        $resolution = empty($data['resolution']) ? "未知" : $data['resolution'];

        $sha = sha1($name . $this->token); //检测是否已经存在
        $info = MemberFileModel::withTrashed()->where(['fileSha' => $sha])->find();
        if ($info) {
            if ($info->delete_time > 0) {
                MemberFileModel::onlyTrashed()->find($info->fileId)->restore();
            }
            $info->fileUrl = $url;
            $info->time = time();
            $info->save();
            return $this->_success('入库成功');
        }
        $_data = [
            'userId' => $this->member['id'],
            'fileType' => '',
            'fileSortName' => $name,
            'fileName' => $name,
            'fileSha' => $sha,
            'fileUrl' => $url,
            'from' => $from,
            'resolute' => $resolution,
            'num' => 0,
        ];
        if ((new MemberFileModel)->save($_data)) {
            return $this->_success('入库成功');
        }
        return $this->_error('入库失败');
    }

    public function extUsers()
    {
        $data = $this->params;
        // if (!isset($data['ext_ids']) || empty($data['ext_ids'])) {
        //     return $this->_error('请填写想要共享资源的用户id,多个用英文逗号分割');
        // }

        $model = new MemberFileModel;

        $ids = array_unique(explode(',', str_replace('，', ',', $data['ext_ids'])));

        $map = [];
        if (isset($data['fileId']) && !empty($data['fileId'])) {
            $map[] = ['fileId', 'eq', $data['fileId']];
            // 查询是否有修改权
            if ($model->where($map)->value('userId') != $this->member['id']) {
                return $this->_error('该文件不是您上传的，无权操作');
            }
        } else {
            $map[] = ['userId', 'eq', $this->member['id']];
        }

        $model->where($map)->update(['ext_ids' => implode(',', $ids)]);
        return $this->_success('保存成功');
    }
}
