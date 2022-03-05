<?php
namespace app\system\model;

use think\Model;

class SystemUpload extends Model
{
    protected $type = [
        'config'      =>  'json',
    ];
    /**
     * 获取所有插件
     * @param bool $update 是否更新缓存
     * @param array $data 入库数据
     * @return array
     */
    public static function lists($code = '', $update = true, $status = 1)
    {
        $result = cache('upload');
        if (!$result || $update == true) {
            $map = [];
            if (is_numeric($status)) {
                $map[] = ['status', '=', $status];
            }
            $result = self::where($map)->order('sort asc')->column('code,title,applies,config', 'code');
            foreach ($result as $k => &$v) {
                if ($v['config']) {
                    $v['config'] = json_decode($v['config'], 1);
                }
            }
            cache('upload', $result);
        }

        if ($code && isset($result[$code])) {
            return $result[$code];
        } else if ($code) {
            return false;
        }

        return $result;
    }
}