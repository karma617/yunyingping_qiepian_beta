<?php

namespace app\common\model;

use think\facade\Cache;
use app\common\model\Base;
use think\model\concern\SoftDelete;
use app\common\model\AlbumPic;

/**
 * 相册组件模型
 */
class Album extends Base
{
    use SoftDelete;
    protected $pk = 'album_id';
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    /**
     * 创建相册
     * @param $data
     */
    public static function addAlbum($data)
    {
        $site_id = isset($data['site_id']) ? $data['site_id'] : '0';

        $data["update_time"] = time();

        Cache::tag("album_" . $site_id)->clear();
        $album = self::create($data);
        if ($album === false) {
            return false;
        }
        return  $album->album_id;
    }

    /**
     * 编辑相册
     * @param $data
     * @param $condition
     */
    public static function editAlbum($data, $condition)
    {
        $check_condition = array_column($condition, 2, 0);
        $site_id         = isset($check_condition['site_id']) ? $check_condition['site_id'] : '0';
        if ($site_id === '') {
            return false;
        }

        $data["update_time"] = time();
        Cache::tag("album_" . $site_id)->clear();
        $res = model("album")->update($data, $condition);

        return $res;
    }

    /**
     * 删除相册
     * @param array $condition
     * @return multitype:string mixed
     */
    public static function deleteAlbum($condition)
    {
        $check_condition = array_column($condition, 2, 0);
        $site_id         = isset($check_condition['site_id']) ? $check_condition['site_id'] : '0';
        $temp_count = AlbumPic::getCount($condition, "*");
        if ($temp_count > 0) {
            return "当前删除相册中存在图片,不可删除!";
        }
        Cache::tag("album_" . $site_id)->clear();
        $res = self::destroy(function ($query) use ($condition) {
            $query->where($condition);
        });
        return true;
    }

    /**
     * 获取相册列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     * @return multitype:string mixed
     */
    public static function getAlbumList($condition = [], $field = "album_id, site_id, album_name, sort, cover, desc, is_default, update_time, num", $order = 'album_id desc', $limit = null)
    {
        $check_condition = array_column($condition, 2, 0);
        $site_id         = isset($check_condition['site_id']) ? $check_condition['site_id'] : '0';

        $data  = json_encode([$condition, $field, $order, $limit]);
        $cache = Cache::get("ablum_getAlbumList_" . $site_id . '_' . $data);
        if (!empty($cache)) {
            return $cache;
        }
        $list = model('album')->pageList($condition, $field, $order, 1, 0)['list'];
        Cache::tag("album_" . $site_id)->set("ablum_getAlbumList_" . $site_id . '_' . '_' . $data, $list);
        return $list;
    }

    /**
     * 同步修改相册下的图片数量
     * @param unknown $condition
     */
    public static function syncAlbumNum($album_id)
    {
        $count = model("album_pic")->where("album_id", "=", $album_id)->count(); //获取本商品分组下的图片数量
        $res   = self::where("album_id", "=", $album_id)->setField("num", $count);
        return $res;
    }
}
