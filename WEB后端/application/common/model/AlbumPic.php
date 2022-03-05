<?php
namespace app\common\model;

use app\common\model\Album;
use think\facade\Cache;
use app\common\model\Base;
use think\model\concern\SoftDelete;

/**
 * 相册组件模型
 */
class AlbumPic extends Base
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
    protected $pk = 'pic_id';

    public static function getCount($condition = [], $field= '*'){
        return self::where($condition)->field($field)->count();
    }

    /**
     * 添加相册图片
     * @param $data
     */
    public static function addAlbumPic($data)
    {
        $site_id = isset($data['site_id']) ? $data['site_id'] : '0';
        $info = self::where("drive ='{$data['drive']}' AND pic_hash ='{$data['pic_hash']}'")->find();
        if($info instanceof AlbumPic){
            $info->update_time=time();
            $info->save();
        }else{
            self::create($data);
            Cache::clear("album_pic_" . $site_id);
            Album::syncAlbumNum($data["album_id"]);//同步当前相册下的图片数量
        }
        return true;
    }

    /**
     * 编辑相册图片
     * @param $data
     * @param $condition
     */
    public static function editAlbumPic($data, $condition)
    {
        $check_condition = array_column($condition, 2, 0);
        $site_id         = isset($check_condition['site_id']) ? $check_condition['site_id'] : '0';

        Cache::clear("album_pic_" . $site_id);
        $res = model("album_pic")->update($data, $condition);
        Album::syncAlbumNum($check_condition["album_id"]);//同步当前相册下的图片数量
        if ($res === false) {
            return false;
        }
        return $res;
    }

    /**
     * 删除相册图片
     * @param array $condition
     * @return multitype:string mixed
     */
    public static function deleteAlbumPic($condition)
    {
        $check_condition = array_column($condition, 2, 0);
        $site_id         = isset($check_condition['site_id']) ? $check_condition['site_id'] : '0';

        Cache::clear("album_pic_" . $site_id);
        $_pic_ids = self::where($condition)->column('pic_id');
        $_album_ids = array_unique(self::where($condition)->column('album_id'));
        $res = self::destroy($_pic_ids);
        foreach ($_album_ids as $v) {
            Album::syncAlbumNum($v);//同步当前相册下的图片数量
        }
        return $res;
    }

    /**
     * 编辑图片所在分组
     * @param $album_id
     * @param $condition
     */
    public static function modifyAlbumPicAlbum($album_id, $condition)
    {
        $check_condition = array_column($condition, 2, 0);
        $site_id         = isset($check_condition['site_id']) ? $check_condition['site_id'] : '0';


        $info              = model("album_pic")->getInfo($condition);
        $original_album_id = $info["album_id"];
      
        Cache::clear("album_pic_" . $site_id);
        Cache::clear("album_" . $site_id);
        $res = model("album_pic")->update(["album_id" => $album_id], $condition);//切换图片所在分组
        Album::syncAlbumNum($album_id);//同步当前相册下的图片数量
        Album::syncAlbumNum($original_album_id);//同步当前相册下的图片数量
        return $res;
    }

    /**
     * 获取相册图片列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     * @return multitype:string mixed
     */
    public static function getAlbumPicList($condition = [], $field = "pic_id, pic_name, pic_path, pic_spec,ext, site_id, update_time", $order = '',$page = 1, $limit = null)
    {
        $check_condition = array_column($condition, 2, 0);
        $site_id         = isset($check_condition['site_id']) ? $check_condition['site_id'] : '0';

        $data  = json_encode([$condition, $field, $order, $limit]);
        $cache = Cache::get("album_pic_getAlbumPicList_" . $site_id . '_' . $data);
        if (!empty($cache)) {
            return $cache;
        }
        $list = model('album_pic')->pageList($condition, $field, $order,$page,$limit);
        Cache::tag("album_pic_" . $site_id)->set("album_pic_getAlbumPicList_" . $site_id . '_' . '_' . $data, $list);
        return $list;
    }
}