<?php

namespace app\system\model;

use think\facade\Cache;
use think\Model;

/**
 * 管理员角色索引模型
 * @package app\system\model
 */
class SystemUserRole extends Model
{
    protected $autoWriteTimestamp = false;

    protected $auto = ['auth'];

    // 缓存标签名
    const CACHE_TAG = 'system@user_role';

    protected static function init()
    {
        // 新增后
        self::event('after_insert', function ($obj) {
            Cache::rm(self::CACHE_TAG);
        });

        // 更新后
        self::event('after_update', function ($obj) {
            Cache::rm(self::CACHE_TAG);
        });

        // 删除后
        self::event('after_delete', function ($obj) {
            Cache::rm(self::CACHE_TAG);
        });
    }

    public function setAuthAttr($value)
    {
        if (empty($value)) {
            return '';
        }
        return json_encode($value);
    }

    /**
     * 获取同组织下的所有管理员ID
     *
     * @param string|array $roleIds
     * @return array
     */
    public static function getOrgUserId($roleIds)
    {
        $cacheName = 'org_user_id_'.$roleIds;
        $ids = Cache::get($cacheName);
        if (!$ids) {
            $ids = self::where('role_id', 'in', $roleIds)->distinct(true)->column('user_id');
            Cache::tag(self::CACHE_TAG)->set($cacheName, $ids);
        }
        
        return $ids;
    }
}
