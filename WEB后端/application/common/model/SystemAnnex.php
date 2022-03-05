<?php
namespace app\common\model;

use think\Model;

/**
 * 附件模型
 * @package app\common\model
 */
class SystemAnnex extends Model
{
    // 定义时间戳字段名
    protected $createTime = 'ctime';
    protected $updateTime = false;
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    /**
     * [兼容旧版]附件上传
     */
    public static function upload()
    {
        $param = input('post.',[]);
        return (new \app\system\admin\album\Upload)->run($param);
    }
    
}