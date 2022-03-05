<?php
namespace app\goods\model;

use app\common\model\Base;
use think\model\concern\SoftDelete;

class Goods extends Base
{

    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';


    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
    protected $type = [
        'goods_img'     =>  'json',
    ];

    

}