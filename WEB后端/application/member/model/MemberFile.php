<?php
namespace app\member\model;
use think\model\concern\SoftDelete;

class MemberFile extends Base
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    protected $pk = 'fileId';

}