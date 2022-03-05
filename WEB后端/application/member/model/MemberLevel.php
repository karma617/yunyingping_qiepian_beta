<?php

namespace app\member\model;

use app\member\model\Member;

class MemberLevel extends Base
{
    protected $type = [
        'thumb'     =>  'json',
    ];
    // 模型事件
    public static function init()
    {
        //更新后
        self::event('after_write', function ($obj) {
            model('member')->update(['level_name' => $obj->level_name], ['level_id' => $obj->id]);
        });
    }
    //删除
    public function deleteMemberLevel($id)
    {
        $count = Member::where(['level_id' => $id])->count();
        if ($count > 0) {
            $this->error = '有会员不可删除';
            return false;
        }
        return $this->where('id=' . $id)->delete();
    }
    //下拉数据
    public function getSelect()
    {
        return $this->field('id AS value,level_name AS label')->select()->toArray();
    }
    //所属会员
    public function getMemberCountAttr($value, $data)
    {
        return model('member')->where('level_id=' . $data['id'])->count();
    }

    public function getApiLimitAttr($v, $d)
    {
        return explode(',', $v);
    }

    public function setApiLimitAttr($v, $d)
    {
        return implode(',', $v);
    }
}
