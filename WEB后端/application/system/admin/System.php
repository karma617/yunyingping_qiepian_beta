<?php

namespace app\system\admin;

use app\system\model\SystemMenu as MenuModel;
use app\system\model\SystemRole as RoleModel;
use app\system\model\SystemConfig as ConfigModel;
use app\system\model\SystemModule as ModuleModel;
use app\system\model\SystemPlugins as PluginsModel;
use think\Validate;
use Env;

/**
 * 系统设置控制器
 * @package app\system\admin
 */
class System extends Admin
{
    /**
     * 系统基础配置
     * @return mixed
     */
    public function index($group = '')
    {
        $tabData = [];
        foreach ((array)config('sys.config_group') as $key => $value) {
            $arr = [];
            $arr['label'] = $value;
            $arr['name'] = $key;
            $arr['url'] = url('?group='.$key);
            // 菜单id
            $map = [];
            $map[] = ['pid', 'eq', 8];
            $map[] = ['param', 'eq', 'group='.$key];
            $arr['mid'] = MenuModel::where($map)->value('id');
            $tabData['column'][] = $arr;
        }
        foreach ($tabData['column'] as $key => $value) {
            if (!RoleModel::checkAuth($value['mid'])) {
                unset($tabData['column'][$key]);
            }
        }
        $tabData['column'] = array_values($tabData['column']);
        $tabData['current'] = $group ?: $tabData['column'][0]['name'];

        $map = [];
        $map['group'] = $tabData['current'];
        $map['status'] = 1;
        $formData = ConfigModel::where($map)->where('id','>',1)->order('sort,id')->field('id,name,title,group,url,value,type,options,tips')->select()->toArray();

        // 模块配置
        $module = ModuleModel::where('status', 2)->column('name,title,config', 'name');
        foreach ($module as $mod) {
            if (empty($mod['config'])) {
                continue;
            }
            $arr = [];
            $arr['label'] = $mod['title'];
            $arr['name'] = $mod['name'];
            $arr['url'] = url('?group='.$mod['name']);
            $tabData['column'][] = $arr;
            if ($group == $mod['name']) {
                $formData = json_decode($mod['config'], 1);
                foreach ($formData as $k => &$v) {
                    if (!empty($v['options'])) {
                        $v['options'] = parse_attr($v['options']);
                    }
                    $v['id'] = $k;
                }
                $tabData['module'] = $mod['name'];
            }
        }


        $this->assign('formData', $formData);
        $this->assign('tabData', $tabData);

        return $this->fetch();
    }
    /**
     * 保存配置
     *
     * @return void
     * @author Leo <13708867890>
     * @since 2020-12-07 17:20:43
     */
    public function save_config(){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $url = '';
            if ((empty($data['module']))) {
                //系统配置保存
                foreach ($data['data'] as $k => $v) {
                    $_val = is_array($v) ? json_encode($v, 256) :$v;
                    //查找后更新,模型事件能取得完整数据
                    $config = (new ConfigModel())->where('group', $data['group'])->where('name', $k)->find();

                    //更新入口后返回新入口,前端对应处理跳转
                    if ($config->name=='admin_path' && $_val != $config->value) {
                        $url = ROOT_DIR.$_val.'/system/system/index/group/'.$data['group'].'.html';
                    }

                    $config->value=$_val;
                    $config->save();
                }
            } else {
                //非系统配置保存
                $row = ModuleModel::where('name', $data['module'])->value('config');
                if (!$row) {
                    return $this->error('保存失败(原因：'.$data['module'].'模块无需配置)');
                }
                $row = json_decode($row, 1);
                foreach ($row as $key => $conf) {
                    $val = isset($data['data'][$conf['name']])? $data['data'][$conf['name']] : $conf['value'] ;
                    $row[$key]['value'] = $val;
                }
                if (ModuleModel::where('name', $data['module'])->setField('config', json_encode($row, 1)) === false) {
                    return $this->error('保存失败');
                }
            }

            // 更新配置缓存
            $config = ConfigModel::getConfig('', true);

            return $this->success('保存成功', $url);
        }
    }
}