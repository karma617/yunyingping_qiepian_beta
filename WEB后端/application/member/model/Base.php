<?php

namespace app\member\model;

use think\Model;


class Base extends Model
{
    public static $member_id;
    protected function base($query)
    {
        //有member_id自动限定范围
        if (self::$member_id > 0 && in_array('member_id',$query->getTableFields($query->getTable()))) {
            $query->where(['member_id' => self::$member_id]);
        }
    }
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    /**
     * 获取分页列表数据
     */
    final public function pageList($condition = [], $field = true, $order = '', $page = 1, $page_size = 15,$append=[],$hidden=[], $alias = 'a', $join = [], $group = null)
    {
        $_obj = $this->field($field)->alias($alias)->where($condition)->order($order);
        if (!empty($join)) {
            $_obj = $this->parseJoin($_obj, $join);
        }
        if (!empty($group)) {
            $_obj = $_obj->group($group);
        }
        $count = $_obj->count();
        if ($page_size == 0) {
            //查询全部
            $result_data = $_obj->append($append)->select();
        } else {
            $result_data = $_obj->page($page, $page_size)->append($append)->hidden($hidden)->select();
        }
        $result['count'] = $count;
        $result['list'] = $result_data;
        return $result;
    }
}
