<?php
namespace app\system\model;

use think\Model;
use app\system\model\SystemUser as UserModel;
/**
 * 后台日志模型
 * @package app\system\model
 */
class SystemLog extends Model
{

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    
    public function user()
    {
        return $this->hasOne('SystemUser', 'id', 'uid');
    }

    public function getUidAttr($val, $data)
    {
        $username = UserModel::where('id='.$val)->value('username');
        return $username ? $username : '未知用户';
    }
}
