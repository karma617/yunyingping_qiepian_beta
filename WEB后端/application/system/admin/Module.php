<?php

namespace app\system\admin;

use app\system\model\SystemModule as ModuleModel;
use app\system\model\SystemConfig as ConfigModel;
use app\system\model\SystemMenu as MenuModel;
use app\system\model\SystemHook as HookModel;
use one\Dir;
use one\PclZip;
use think\Db;
use think\Xml;
use think\facade\Log;
use think\facade\Env;

/**
 * 模块管理控制器
 * @package app\system\admin
 */
class Module extends Admin
{
    public $tabData = [];
    /**
     * 初始化方法
     */
    protected function initialize()
    {
        parent::initialize();

        $tabData['column'] = [
            [
                'label' => '已启用',
                'name'=>'s2',
                'url' => url('system/module/index?status=2'),
            ],
            [
                'label' => '已停用',
                'name'=>'s1',
                'url' => url('system/module/index?status=1'),
            ],
            [
                'label' => '待安装',
                'name'=>'s0',
                'url' => url('system/module/index?status=0'),
            ]
        ];

        $this->tabData = $tabData;
        $this->appPath = Env::get('app_path');
    }

    /**
     * 模块管理首页
     * @return mixed
     */
    public function index()
    {
        $status             = input('status/d', 2);
        $tabData            = $this->tabData;
        $tabData['current'] = 's'.$status;
        $tabData['val'] = $status;
        if ($this->request->isAjax()) {
            $status             = input('status/d', 2);
            $map                = [];
            $map['status']      = $status;
            $map['system']      = 0;
            $modules = ModuleModel::where($map)
            ->order('sort,id')
            ->column('id,title,author,intro,icon,default,system,app_keys,identifier,config,name,version,status');
            
            if ($status == 0) {
                
                // 自动将本地未入库的模块导入数据库
                $allModule  = ModuleModel::order('sort,id')->column('id,name', 'name');
                $files      = Dir::getList($this->appPath);
                $sysDir     = config('one_system.modules');
                array_push($sysDir, 'extra');
                
                foreach ((array)$files as $k => $f) {

                    // 排除系统模块和已存在数据库的模块
                    if (array_search($f, $sysDir) !== false ||
                    array_key_exists($f, $allModule) ||
                    !is_dir($this->appPath.$f)) {
                        continue;
                    }
                    
                    if (file_exists($this->appPath.$f.'/info.php')) {
                        $info = include_once $this->appPath.$f.'/info.php';
                        // 处理默认图标
                        $sql_icon = ModuleModel::defaultIcon($this->appPath.$f.'/', $info);
                        $sql                = [];
                        $sql['name']        = $info['name'];
                        $sql['identifier']  = $info['identifier'];
                        $sql['theme']       = $info['theme'];
                        $sql['title']       = $info['title'];
                        $sql['intro']       = $info['intro'];
                        $sql['author']      = $info['author'];
                        $sql['icon']        = $sql_icon;
                        $sql['version']     = $info['version'];
                        $sql['url']         = $info['author_url'];
                        $sql['config']      = '';
                        $sql['status']      = 0;
                        $sql['default']     = 0;
                        $sql['system']      = 0;
                        $sql['app_keys']    = '';
                        $db = ModuleModel::create($sql);
                        $sql['id'] = $db->id;
                        $modules = array_merge($modules, [$sql]);
                    }
                }
            }
            return $this->success('','',array_values($modules));
        }
        
        $this->assign('tabData', $tabData);
        
        return $this->fetch();
    }

    /**
     * 模块设计
     * @return mixed
     */
    public function design()
    {
        if (config('sys.app_debug') == 0) {
            return $this->error('非开发模式禁止使用此功能');
        }

        if ($this->request->isPost()) {
            $model = new ModuleModel();
            $data = $this->request->post('data');
            $result = $this->validate($data, 'app\system\validate\SystemModule');
            if ($result !== true) {
                return $this->error($result);
            }
            
            if (!$model->design($data)) {
                return $this->error($model->getError());
            }

            return $this->success('模块已自动生成完毕', url('index?status=0'));
        }
        return $this->fetch();
    }

    /**
     * 安装模块
     * @return mixed
     */
    public function install($id = 0)
    {
        if ($this->request->isPost()) {
            $postData = $this->request->post();
            $result = self::execInstall($id, $postData['clear']);
            if ($result !== true) {
                return $this->error($result);
            }
            return $this->success('模块已安装成功', url('index?status=2'));
        }
    }
    /**
     * 重置模块
     *
     * @param integer $id
     * @return void
     * @author Leo <13708867890>
     * @since 2020-12-08 20:17:30
     */
    public function reinstall($id = 0)
    {
        if ($this->request->isPost()) {
            $postData = $this->request->post();
            $result = self::execInstall($id, 0 ,1);
            if ($result !== true) {
                return $this->error($result);
            }
            return $this->success('模块已重置成功', url('index?status=2'));
        }
    }
    /**
     * 执行模块安装
     * @date   2018-11-01
     * @access public
     * @param  int          $id    模块ID
     * @param  integer      $clear 清空旧数据
     * @param  int          $id    重载除数据库外的其它配置
     * @return bool|string  
     */
    public function execInstall($id, $clear = 1, $reload = 0)
    {
        
        $mod = ModuleModel::where('id', $id)->find();
        if (!$mod) {
            return '模块不存在';
        }

        if ($mod['status'] > 0 && $reload ==0) {
            return '请勿重复安装此模块';
        }

        $modPath = $this->appPath.$mod['name'].'/';
        if (!file_exists($modPath.'info.php')) {
            return '模块配置文件不存在[info.php]';
        }

        $info = include_once $modPath.'info.php';
        // 模块依赖检查
        $msg = "模块依赖：\n\r";
        foreach ($info['module_depend'] as $k => $v) {
            if (!isset($v[3])) {
                $v[3] = '=';
            }
            // 判断模块是否存在
            if (!is_dir($this->appPath.$v[0])) {
                return $msg.'此模块需安装依赖模块：' . $v[0];
            }
            if (!file_exists($this->appPath.$v[0].'/info.php')) {
                return $msg.$v[0] . '模块配置文件不存在';
            }
            $dinfo = include $this->appPath.$v[0].'/info.php';
            // 判断依赖的模块标识是否一致
            if ($dinfo['identifier'] != $v[1]) {
                return $msg.$v[0] . '模块标识不匹配';
            }
            // 版本对比
            if (version_compare($dinfo['version'], $v[2], $v[3]) === false) {
                return $msg.$v[0] . '模块需要的版本必须'.$v[3].$v[2];
            }
        }

        // 过滤系统表
        foreach ($info['tables'] as $t) {
            if (in_array($t, config('one_system.tables'))) {
                return '模块数据表与系统表重复['.$t.']';
            }
        }

        //重置时删除相关配置
        if($reload == 1){
            //重置不可修改名称和标识
            if($mod['name']!=$info['name']){
                return '模块配置文件不匹配[info.php]';
            }
            // 删除路由
            if ( file_exists(Env::get('route_path').$mod['name'].'.php') ) {
                unlink(Env::get('route_path').$mod['name'].'.php');
            }
            // 删除当前模块菜单
            MenuModel::where('module', $mod['name'])->delete();
            // 删除模块钩子
            model('SystemHook')->where('source', 'module.'.$mod['name'])->delete();
            //跳过SQL安装
            goto _config;
        }

        // 导入安装SQL
        $sqlFile = realpath($modPath.'sql/install.sql');
        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            $sqlList = parse_sql($sql, 0, [$info['db_prefix'] => config('database.prefix')]);
            if ($sqlList) {
                if ($clear == 1) {// 清空所有数据
                    foreach ($info['tables'] as $table) {
                        if (Db::query("SHOW TABLES LIKE '".config('database.prefix').$table."'")) {
                            Db::execute('DROP TABLE IF EXISTS `'.config('database.prefix').$table.'`;');
                        }
                    }
                }
                $sqlList = array_filter($sqlList);
                foreach ($sqlList as $v) {
                    // 过滤sql里面的系统表
                    foreach (config('one_system.tables') as $t) {
                        if (stripos($v, '`'.config('database.prefix').$t.'`') !== false) {
                            return 'install.sql文件含有系统表['.$t.']';
                        }
                    }
                    if (stripos($v, 'DROP TABLE') === false) {
                        try {
                            Db::execute($v);
                        } catch(\Exception $e) {
                            return $e->getMessage();
                        }
                    }
                }
            }
        }

        // 导入演示SQL
        $sqlFile = realpath($modPath.'sql/demo.sql');
        if (file_exists($sqlFile) && $this->request->param('demo_data/d', 0) === 1) {
            $sql = file_get_contents($sqlFile);
            $sqlList = parse_sql($sql, 0, [$info['db_prefix'] => config('database.prefix')]);
            if ($sqlList) {
                $sqlList = array_filter($sqlList);
                foreach ($sqlList as $v) {
                    // 过滤sql里面的系统表
                    foreach (config('one_system.tables') as $t) {
                        if (stripos($v, '`'.config('database.prefix').$t.'`') !== false) {
                            return 'demo.sql文件含有系统表['.$t.']';
                        }
                    }

                    if (stripos($v, 'DROP TABLE') === false) {
                        try {
                            Db::execute($v);
                        } catch(\Exception $e) {
                            return $e->getMessage();
                        }
                    }
                }
            }
        }
        _config:
        // 导入路由
        if ( file_exists($modPath.'route.php') ) {
            copy($modPath.'route.php', Env::get('route_path').$mod['name'].'.php');
        }

        // 导入菜单
        if ( file_exists($modPath.'menu.php') ) {
            $menus = include_once $modPath.'menu.php';
            // 如果不是数组且不为空就当JSON数据转换
            if (!is_array($menus) && !empty($menus)) {
                $menus = json_decode($menus, 1);
            }
            if (MenuModel::importMenu($menus, $mod['name']) == false) {
                // 执行回滚
                MenuModel::where('module', $mod['name'])->delete();
                return '添加菜单失败，请重新安装';
            }
        }
        
        // 导入模块钩子
        if (!empty($info['hooks'])) {
            $hookModel = new HookModel;
            foreach ($info['hooks'] as $k => $v) {
                $map            = [];
                $map['name']    = $k;
                $map['intro']   = $v;
                $map['source']  = 'module.'.$mod['name'];
                $hookModel->storage($map);
            }
        }
        cache('hook_plugins', null);

        // 导入模块配置
        if (isset($info['config']) && !empty($info['config'])) {
            $menu           = [];
            $menu['pid']    = 10;
            $menu['module'] = $mod['name'];
            $menu['title']  = $mod['title'].'配置';
            $menu['url']    = 'system/system/index';
            $menu['param']  = 'group='.$mod['name'];
            $menu['system'] = 0;
            $menu['debug']  = 0;
            $menu['sort']   = 100;
            $menu['status'] = 1;
            $menu_mod = new MenuModel;
            $menu_mod->storage($menu);
            ModuleModel::where('id', $id)->setField('config', json_encode($info['config'], 1));
        }
        // 更新模块基础信息
        try {
            ModuleModel::where('id', $id)->setField('status', 2);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
        ModuleModel::getConfig('', true);
        return true;
    }

    /**
     * 卸载模块
     * @return mixed
     */
    public function uninstall()
    {
        $id = get_num();
        $mod = ModuleModel::where('id', $id)->find();
        if (!$mod) {
            return $this->error('模块不存在');
        }
        if ($mod['status'] == 0) {
            return $this->error('模块未安装');
        }

        if ($this->request->isPost()) {
            $modPath = $this->appPath.$mod['name'].'/';
            // 模块自定义配置
            if (!file_exists($modPath.'info.php')) {
                return $this->error('模块配置文件不存在[info.php]');
            }
            $info = include_once $modPath.'info.php';

            // 过滤系统表
            foreach ($info['tables'] as $t) {
                if (in_array($t, config('one_system.tables'))) {
                    return $this->error('模块数据表与系统表重复['.$t.']');
                }
            }

            $post = $this->request->post();
            // 导入SQL
            $sqlFile = realpath($modPath.'sql/uninstall.sql');
            if (file_exists($sqlFile) && $post['clear'] == 1) {
                $sql = file_get_contents($sqlFile);
                $sqlList = parse_sql($sql, 0, [$info['db_prefix'] => config('database.prefix')]);
                if ($sqlList) {
                    $sqlList = array_filter($sqlList);
                    foreach ($sqlList as $v) {
                        // 防止删除整个数据库
                        if (stripos(strtoupper($v), 'DROP DATABASE') !== false) {
                            return $this->error('uninstall.sql文件疑似含有删除数据库的SQL');
                        }
                        // 过滤sql里面的系统表
                        foreach (config('one_system.tables') as $t) {
                            if (stripos($v, '`'.config('database.prefix').$t.'`') !== false) {
                                return $this->error('uninstall.sql文件含有系统表['.$t.']');
                            }
                        }
                        try {
                            Db::execute($v);
                        } catch(\Exception $e) {
                            return $e->getMessage();
                        }
                    }
                }
            }
            // 删除路由
            if ( file_exists(Env::get('route_path').$mod['name'].'.php') ) {
                unlink(Env::get('route_path').$mod['name'].'.php');
            }
            // 删除当前模块菜单
            MenuModel::where('module', $mod['name'])->delete();
            // 删除模块钩子
            model('SystemHook')->where('source', 'module.'.$mod['name'])->delete();
            cache('hook_plugins', null);
            // 更新模块状态为未安装
            ModuleModel::where('id', $id)->update(['status' => 0, 'default' => 0, 'config' => '']);
            ModuleModel::getConfig('', true);
            $this->success('模块已卸载成功', url('index?status=0'));
        }

        $this->assign('formData', $mod);
        return $this->fetch();
    }

    /**
     * 删除模块
     * @return mixed
     */
    public function del()
    {
        $id = get_num();
        $module = ModuleModel::where('id', $id)->find();
        if (!$module) {
            return $this->error('模块不存在');
        }
        if ($module['name'] == 'system') {
            return $this->error('禁止删除系统模块');
        }
        if ($module['status'] != 0) {
            return $this->error('已安装的模块禁止删除');
        }

        // 删除模块文件
        $path = $this->appPath.$module['name'];
        if (is_dir($path) && Dir::delDir($path) === false) {
            return $this->error('模块删除失败['.$path.']');
        }

        // 删除模块路由
        $path = $this->appPath.$module['name'].'.php';
        if (is_file($path)) {
            @unlink($path);
        }

        // 删除模块模板
        $error = '';
        $path = '.'.ROOT_DIR.'theme/'.$module['name'];
        if (is_dir($path) && Dir::delDir($path) === false) {
            $error = '模块模板删除失败['.$path.']';
        }

        // 删除模块相关附件
        $path = '.'.ROOT_DIR.'static/'.$module['name'];
        if (is_dir($path) && Dir::delDir($path) === false) {
            $error .= '<br>模块删除失败['.$path.']';
        }

        // 删除模块图标
        $file = '.'.$module['icon'];
        $path = pathinfo($file);
        if (is_file($file) && is_dir($path['dirname']) && Dir::delDir($path['dirname']) === false) {
            @unlink("$path");
        }

        // 删除模块记录
        ModuleModel::where('id', $id)->delete();
        // 删除菜单记录
        MenuModel::where('module', $module['name'])->delete();
        // 删除权限记录 TODO
        if ($error) {
            return $this->error($error);
        }

        return $this->success('模块删除成功');
    }

    /**
     * 设置默认模块
     * @return mixed
     */
    public function setDefault()
    {
        $id     = $this->request->param('id/d');
        $val    = $this->request->param('val/d');
        if ($val == 1) {
            $res = ModuleModel::where('id', $id)->find();
            if ($res['system'] == 1) {
                return $this->error('禁止设置系统模块');
            }
            if ($res['status'] != 2) {
                return $this->error('禁止设置未启用或未安装的模块');
            }

            ModuleModel::where('id > 0')->setField('default', 0);
            ModuleModel::where('id', $id)->setField('default', 1);
        } else {
            ModuleModel::where('id', $id)->setField('default', 0);
        }
        return $this->success('操作成功');
    }

    /**
     * 状态设置
     * @return mixed
     */
    public function status()
    {
        $val    = $this->request->param('val/d');
        $id     = get_num();
        // if ($id == 1) {
        //     return $this->error('禁止设置系统模块');
        // }
        $model = ModuleModel::get($id);
        if ($model['status'] <= 0) {
            return $this->error('只允许操作已安装模块');
        }
        $res = $model->save(['status' => $val]);
        if ($res === false) {
            return $this->error('操作失败');
        }
        if ($val == 1) {
            MenuModel::where('module', $model['name'])->setField('status', 0);
        } else {
            MenuModel::where('module', $model['name'])->setField('status', 1);
        }
        return $this->success('操作成功');
    }

    /**
     * 添加模型菜单
     * @param array $data 菜单数据
     * @param string $mod 模型名称
     * @param int $pid 父ID
     * @return bool
     */    
    private function addMenu($data = [], $mod = '', $pid = 0)
    {
        if (empty($data)) {
            return false;
        }
        foreach ($data as $v) {
            $v['pid'] = $pid;
            $childs = $v['childs'];
            unset($v['childs']);
            $res = model('SystemMenu')->storage($v);
            if (!$res) {
                return false;
            }
            if (!empty($childs)) {
                $this->addMenu($childs, $mod, $res['id']);
            }
        }
        return true;
    }

    public function editor($id)
    {
        $mod = ModuleModel::where('id', $id)->find();
        if (!$mod) {
            return $this->error('模块不存在');
        }
        $path = $this->appPath.$mod['name'];
        //配置文件
        $edit = [
            ['name'=>'模块设置','file' => DS.'info.php','content'=>''],
            ['name'=>'菜单配置','file' => DS.'menu.php','content'=>''],
        ];
        //模块设置文件
        $_config_files      = @Dir::getList($path.DS.'config');
        if(count($_config_files)){
            foreach ($_config_files as $k => $v) {
                array_push($edit,['name'=>'配置文件','file' => DS.'config'.DS.$v,'content'=>'']);
            }
        }
        if ($this->request->isAjax()) {
            //TODO 处理缓存
            $_key = input('key');
            $_content = input('val');
            @file_put_contents($path.$edit[$_key]['file'],$_content);
            return $this->success('更新成功');
        }
        foreach ($edit as $k => $v) {
            $edit[$k]['content'] = @file_get_contents($path.$v['file']);
        }
        return $this->assign('edit',$edit)->assign('id',$id)->fetch();
    } 
 
}
