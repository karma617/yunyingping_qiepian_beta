<?php
namespace app\system\model;

use think\Model;
use think\facade\Cache;
use app\system\model\SystemUser;

/**
 * 系统配置模型
 * @package app\system\model
 */
class SystemConfig extends Model
{

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

    public static function init()
    {
        //更新后
        self::event('after_write', function ($obj) {
            if(!isset($obj['name'])){
                return true;
            }
            $_name = $obj['name'];
            $_value = $obj['value'];
            //处理ENV文件
            $rootPath = \Env::get('root_path');
            if ('app_debug' == $_name || 'app_trace' == $_name) {
                $file = new \one\WriteIniFile($rootPath.'.env');
                $rs = $file->update(["{$_name}"=> $_value ? 'true' : 'false'])->write();
            }
            //登录入口
            if ('admin_path' == $_name) {
                $webPath = './';
                if (is_file($webPath.config('sys.admin_path')) && is_writable($webPath.config('sys.admin_path'))) {
                    @rename($webPath.config('sys.admin_path'), $webPath.$_value);
                }
            }
        });
    }
    public function getOptionsAttr($val, $data)
    {
        $_val = $val;
        if(in_array($data['type'],['radio','select','checkbox'])){
            $_val = [];
            $_tmp = parse_attr($val);
            foreach ($_tmp as $k => $v) {
                $_val[] = ['label'=>$k,'value'=>$v];
            }
        }
        
        return $_val;
    }
    /**
     * 获取系统配置信息
     * @param  string $name 配置名
     * @param  bool $update 是否更新缓存
     * @return mixed
     */
    public static function getConfig($name = '', $update = false)
    {
        $result = Cache::get('sys_config');
        if ($result === false || $update == true) {
            $configs = self::column('value,type,group', 'name');
            $result = [];
            foreach ($configs as $config) {
                $config['value'] = htmlspecialchars_decode($config['value']);
                switch ($config['type']) {
                    case 'array':
                    case 'checkbox':
                        if ($config['name'] == 'config_group') {
                            $v = parse_attr($config['value']);
                            if (!empty($config['value'])) {
                                $result[$config['group']][$config['name']] = array_merge(config('one_system.config_group'), $v);
                            } else {
                                $result[$config['group']][$config['name']] = config('one_system.config_group');
                            }
                        } else {
                            $result[$config['group']][$config['name']] = parse_attr($config['value']);
                        }
                        break;
                    default:
                        $result[$config['group']][$config['name']] = $config['value'];
                        break;
                }
            }
            Cache::tag('one_config')->set('sys_config', $result);
        }
        return $name != '' ? $result[$name] : $result;
    }

    /**
     * 删除配置
     * @param string|array $id 节点ID
     * @return bool
     */
    public function del($ids = '') {
        if (is_array($ids)) {
            $error = '';
            foreach ($ids as $k => $v) {
                $map = [];
                $map['id'] = $v;
                $row = self::where($map)->find();
                if ($row['system'] == 1) {
                    $error .= '['.$row['title'].']为系统配置，禁止删除！<br>';
                    continue;
                }
                self::where($map)->delete();
            }
            if ($error) {
                $this->error = $error;
                return false;
            }
            return true;
        }
        $this->error = '参数传递错误';
        return false;
    }
}
