<?php

namespace app\system\model;

use think\Model;
use app\system\model\SystemUser as UserModel;
use app\system\model\SystemUserRole as IndexModel;

/**
 * 后台角色模型
 * @package app\system\model
 */
class SystemRole extends Model
{

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

    protected $auto = ['auth'];

    public function setAuthAttr($value)
    {
        if (empty($value)) return '';
        return json_encode($value);
    }

    public function getAuthAttr($value)
    {
        if (empty($value)) return [];
        return json_decode($value, 1);
    }

    // 模型事件
    public static function init()
    {
        // 更新前
        self::event('before_update', function ($obj) {

            $roles = explode(',', ADMIN_ROLE);
            if (in_array($obj->id, $roles)) {
                throw new \Exception("禁止修改当前角色）");
                return false;
            }
            
            return true;

        });

        // 删除前
        self::event('before_delete', function ($obj) {
            if (IndexModel::where('role_id', 'in', $obj->id)->find()) {
                throw new \Exception("已有管理员绑定此角色（请先取消绑定）");
                return false;
            }
            return true;
        });
    }

    /**
     * 获取所有角色(下拉列)
     * @param int $id 选中的ID
     * @return string
     */
    public static function getOption($id = 0)
    {
        $rows = self::column('id,name');
        $str = '';
        foreach ($rows as $k => $v) {
            if ($k == 1) {// 过滤超级管理员角色
                continue;
            }
            if ($id == $k) {
                $str .= '<option value="'.$k.'" selected>'.$v.'</option>';
            } else {
                $str .= '<option value="'.$k.'">'.$v.'</option>';
            }
        }
        return $str;
    }

    /**
     * 获取所有角色
     * @return array
     */
    public static function getAll()
    {
        return self::column('id,name');
    }

    /**
     * 检查访问权限
     * @param int $id 需要检查的节点ID
     * @return bool
     */
    public static function checkAuth($id = 0)
    {
        $login = session('admin_user');
        // 超级管理员直接返回true
        if ($login['uid'] == '1' || $login['role_id'] == '1') {
            return true;
        }
        // 获取当前角色的权限明细
        $roleAuth = (array)session('role_auth_'.$login['uid']);
        if (!$roleAuth) {

            $roleAuth = self::getRoleAuth($login['role_id']);

            // 非开发模式，缓存数据
            if (config('sys.app_debug') == 0) {
                session('role_auth_'.$login['uid'], $roleAuth);
            }
        }
        if (!$roleAuth) return false;
        return in_array($id, $roleAuth);
    }

    /**
     * 获取角色权限ID集
     */
    public static function getRoleAuth($id)
    {
        $rows   = self::where('id', 'in', $id)->field('auth')->select();
        $auths  = [];

        foreach($rows as $k => $v) {
            $auths = array_merge($auths, $v['auth']);
        }
        
        return array_unique($auths);
    }
}
