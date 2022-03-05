<?php

namespace app\system\admin;

use app\common\model\Album as AlbumModel;
use app\common\model\AlbumPic as AlbumPicModel;
use app\system\model\SystemUpload;
use app\system\admin\album\Upload;

/**
 * 相册
 * @package app\shop\controller
 */
class Album extends Admin
{

    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
        $this->tabData = [
            'column' => [
                ['label' => '附件管理', 'name' => 'album', 'url' => url('system/album/index')],
                ['label' => '回收管理', 'name' => 'recycle', 'url' => url('system/album/recycle')],
                ['label' => '上传配置', 'name' => 'config', 'url' => url('system/album.drive/index')],
            ],
            'current' => 'album'
        ];
        $drives = array_merge([['id' => 0, 'code' => 'Local', 'title' => '本地']], SystemUpload::where('status = 1')->order('sort asc')->column('code,title', 'id'));
        $this->assign('tabData', $this->tabData);
        $this->assign('drives', $drives);
    }



    /**
     * 获取相册分组
     */
    function getAlbumList()
    {
        if (request()->isAjax()) {
            $album_list  = AlbumModel::getAlbumList();
            return $this->success('', '', $album_list);
        }
    }

    /**
     * 添加分组
     */
    public function addAlbum()
    {
        if (request()->isAjax()) {
            $album_name  = input('album_name', '');
            $data        = array(
                'site_id'    => 0,
                'album_name' => $album_name
            );
            $res         = AlbumModel::addAlbum($data);
            if (!$res) {
                return $this->error('添加失败');
            }
            return $this->success('添加成功', '', (int)$res);
        }
    }

    /**
     * 修改组名
     */
    public function editAlbum()
    {
        if (request()->isAjax()) {
            $album_name  = input('album_name');
            $album_id    = input('album_id');
            $data        = array(
                'album_name' => $album_name
            );
            $condition   = array(
                ['site_id', "=", 0],
                ['album_id', "=", $album_id]
            );
            $res         = AlbumModel::editAlbum($data, $condition);
            return $this->success('成功', '', $res);
        }
    }

    /**
     * 删除分组
     */
    public function deleteAlbum()
    {
        if (request()->isAjax()) {
            $album_id    = input('album_id');
            $album_model = new AlbumModel();
            $condition   = array(
                ["album_id", "=", $album_id],
                ["site_id", "=", 0]
            );
            $res         = AlbumModel::deleteAlbum($condition);
            if ($res === true) {
                return $this->success('删除成功');
            }
            return $this->error($res);
        }
    }

    /**
     * 修改文件名
     */
    public function modifyPicName()
    {
        if (request()->isAjax()) {
            $pic_id   = input('pic_id', 0);
            $pic_name = input('pic_name', '');
            $album_id = input('album_id', 0);
            $condition   = array(
                ["pic_id", "=", $pic_id],
                ["site_id", "=", 0],
                ['album_id', '=', $album_id]
            );
            $data        = array(
                "pic_name" => $pic_name
            );
            $res         = AlbumPicModel::editAlbumPic($data, $condition);
            if ($res) {
                return $this->success('修改成功');
            }
            return $this->error('修改失败');
        }
    }

    /**
     * 删除图片
     */
    public function deleteFile()
    {
        if (request()->isAjax()) {
            $pic_id      = input('pic_id/a', []); //图片id
            $album_id    = input('album_id', 0);
            $condition   = array(
                ["pic_id", "in", $pic_id],
                ["site_id", "=", 0],
            );
            $res         = AlbumPicModel::deleteAlbumPic($condition);
            if (!$res) {
                return $this->error('删除失败');
            }
            return $this->success('删除成功', '', $res);
        }
    }

    /**
     * 相册管理界面
     * @return mixed
     */
    public function album()
    {
        $album_model = new AlbumModel();
        if (request()->isAjax()) {
            $page_index = input('page', 1);
            $list_rows  = input('limit', 10);
            $album_id   = input('album_id', '');
            $pic_name   = input("pic_name", "");
            $condition  = array(
                ['site_id', "=", null],
                ['album_id', "=", $album_id],
            );
            if (!empty($pic_name)) {
                $condition[] = ['pic_name', 'like', '%' . $pic_name . '%'];
            }
            $list = AlbumModel::getAlbumPicPageList($condition, $page_index, $list_rows, 'update_time desc');
            return $list;
        }
    }

    /**
     * 附件管理
     *
     * @return void
     */
    public function index()
    {
        header("Expires:-1");
        header("Cache-Control:no_cache");
        header("Pragma:no-cache");
        if (request()->isAjax()) {
            $page      = input('page', 1);
            $limit     = input('limit', 10);
            $album_id  = input('album_id/d', 0);
            $pic_name  = input("pic_name", "");
            $order     = input("order", "update_time desc");
            $site_id   = input('site_id/d', 0);
            $condition = [];
            $condition[] = ['site_id', '=', $site_id];
            if ($album_id > 0) {
                $condition[] = ['album_id', '=', $album_id];
            }
            if (!empty($pic_name)) {
                $condition[] = ['pic_name', 'like', '%' . $pic_name . '%'];
            }
            $list = AlbumPicModel::getAlbumPicList($condition, null, $order, $page, $limit);
            return $this->success('', '', $list);
        }
        return $this->fetch();
    }
    /**
     * 回收管理
     *
     * @return void
     */
    public function recycle()
    {
        header("Expires:-1");
        header("Cache-Control:no_cache");
        header("Pragma:no-cache");
        if (request()->isAjax()) {
            $page      = input('page', 1);
            $limit     = input('limit', 10);

            $order     = input("order", "update_time desc");
            $site_id   = input('site_id/d', 0);
            $condition = [];
            $condition[] = ['site_id', '=', $site_id];

            $obj = AlbumPicModel::onlyTrashed()->where($condition)->order($order);
            $list['count'] = $obj->count();
            $list['list'] = $obj->page($page, $limit)->select();
            return $this->success('', '', $list);
        }
        $this->tabData['current'] = 'recycle';
        $this->assign('tabData', $this->tabData);
        return $this->fetch();
    }

    public function recycle_action()
    {
        $action = input('action/s');
        $pic_id = input('pic_id/a');
        $model = db('album_pic')->where('pic_id', 'IN', $pic_id);
        $rows = $model->column('pic_id,drive,pic_path,album_id', 'pic_id');
        $album_ids = array_unique(array_column($rows,'album_id'));
        if ('true' == $action) {
            $upload = new Upload();
            foreach ($rows as $v) {
                $upload->delFile([
                    'driver' => $v['drive'],
                    'file' => $v['pic_path'],
                ]);
            }
            $model->delete(true);
            foreach ($album_ids as $v) {
                AlbumModel::syncAlbumNum($v);
            }
            return $this->success('删除成功');
        } else {
            $model->setField('delete_time',0);
            foreach ($album_ids as $v) {
                AlbumModel::syncAlbumNum($v);
            }
            return $this->success('恢复成功');
        }
    }

    /**
     * 修改分组
     *
     * @return void
     */
    public function changeGroup()
    {
        if (request()->isAjax()) {
            $pic_id   = input('pic_id', 0);
            $album_id = input('album_id', 0);
            $album_id_old = input('album_id_old', 0);
            $condition   = array(
                ["pic_id", "=", $pic_id],
                ["site_id", "=", 0],
                ['album_id', '=', $album_id_old]
            );
            $data        = array(
                "album_id" => $album_id,
                "update_time" => time()
            );
            $res         = AlbumPicModel::editAlbumPic($data, $condition);
            if (!$res) {
                return $this->error('修改失败');
            }
            return $this->success('修改成功', '', $res);
        }
    }
}
