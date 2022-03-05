<?php
namespace app\system\model;

use think\Model;
use one\Dir;
use think\facade\Env;
use think\facade\Build;
use think\facade\Cache;
use think\Db;

/**
 * 模块模型
 * @package app\system\model
 */
class SystemModule extends Model
{
    private static $identifier = '';
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

    /**
     * 获取模块配置信息
     * @param  string $name 配置名
     * @param  bool $update 是否更新缓存
     * @return mixed
     */
    public static function getConfig($name = '', $update = false)
    {
        $result = Cache::get('module_config');
        if ($result === false || $update == true) {
            $rows = self::where('status', 2)->column('name,config', 'name');
            $result = [];
            foreach ($rows as $k => $r) {
                if (empty($r)) {
                    continue;
                }
                $config = json_decode($r, 1);
                if (!is_array($config)) {
                    continue;
                }
                foreach ($config as $rr) {
                    $rr['value'] = htmlspecialchars_decode($rr['value']);
                    switch ($rr['type']) {
                        case 'array':
                        case 'checkbox':
                            $result['module_'.$k][$rr['name']] = parse_attr($rr['value']);
                            break;
                        default:
                            $result['module_'.$k][$rr['name']] = $rr['value'];
                            break;
                    }
                }
            }
            Cache::tag('one_module')->set('module_config', $result);
        }
        return $name != '' ? $result[$name] : $result;
    }

    /**
     * 将已安装模块添加到路由配置文件
     * @param  bool $update 是否更新缓存
     * @return array
     */
    public static function moduleRoute($update = false)
    {
        $result = cache('module_route');
        if (!$result || $update == true) {
            $map = [];
            $map['status'] = 2;
            $map['name'] =  ['neq', 'admin'];
            $result = self::where($map)->column('name');
            if (!$result) {
                $result = ['route'];
            } else {
                foreach ($result as &$v) {
                    $v = $v.'Route';
                }
            }
            array_push($result, 'route');
            cache('module_route', $result);
        }
        return $result;
    }

    /**
     * 获取所有已安装模块(下拉列)
     * @param string $select 选中的值
     * @return string
     */
    public static function getOption($select = '', $field='name,title')
    {
        $rows = self::column($field);
        $str = '';
        foreach ($rows as $k => $v) {
            if ($k == 1) {// 过滤超级管理员角色
                continue;
            }
            if ($select == $k) {
                $str .= '<option value="'.$k.'" selected>['.$k.']'.$v.'</option>';
            } else {
                $str .= '<option value="'.$k.'">['.$k.']'.$v.'</option>';
            }
        }
        return $str;
    }

    /**
     * 设计并生成标准模块结构
     * @return bool
     */
    public function design($data = [])
    { 
        $app_path = Env::get('app_path');
        if (empty($data)) {
            $data = input('post.');
        }
        $tmp_icon = $data['icon'];
        $data['icon'] = $data['name'] . '.png';
        $mod_path = $app_path . $data['name'] . '/';
        if (is_dir($mod_path) || self::where('name', $data['name'])->find() || in_array($data['name'], config('one_system.modules')) !== false) {
            $this->error = '模块已存在！';
            return false;
        }
        if (!is_writable(Env::get('root_path').'application')) {
            $this->error = '[application]目录不可写！';
            return false;
        }
        $theme_dir = 'public/theme/';
        if (!is_dir($theme_dir)) {
            self::mkDir([$theme_dir]);
        }
        if (!is_writable('.'.ROOT_DIR.'theme')) {
            $this->error = '[theme]目录不可写！';
            return false;
        }
        if (!is_writable('.'.ROOT_DIR.'static')) {
            $this->error = '[static]目录不可写！';
            return false;
        }
        // 自动生成模块标识
        self::$identifier = md5($data['name'] . random(6) . time());
        // 生成模块目录结构
        $build = [];
        // 公共函数文件
        $build[$data['name']]['__file__'] = ['common.php'];
        // 模块目录
        $build[$data['name']]['__dir__'] = ['admin','home','model','sql','validate','view'];
        // 模型文件
        $build[$data['name']]['model'] = [ucfirst($data['name'])];
        // 模版文件
        $build[$data['name']]['view'] = ['index/index'];
        Build::run($build);
        if (!is_dir($mod_path)) {
            $this->error = '模块目录生成失败[application/'.$data['name'].']！';
            return false;
        }
        // 删除默认的应用配置目录
        Dir::delDir(Env::get('config_path').$data['name']);
        // 生成对应的前台主题模板目录、静态资源目录、后台静态资源目录
        $dir_list = [
            'public/theme/'.$data['name'].'/default/static/css',
            'public/theme/'.$data['name'].'/default/static/js',
            'public/theme/'.$data['name'].'/default/static/image',
            'public/theme/'.$data['name'].'/default/index',
            'public/static/'.$data['name'].'/css',
            'public/static/'.$data['name'].'/js',
            'public/static/'.$data['name'].'/image',
        ];
        self::mkDir($dir_list);
        self::mkThemeConfig('theme/'.$data['name'].'/default/', $data);
        self::mkSql($mod_path, $data);
        self::mkMenu($mod_path, $data);
        self::mkControl($mod_path, $data);
        self::mkInfo($mod_path, $data);
        // 配置默认图标文件
        $app_icon = Env::get('app_path') . $data['name'] . '/' . $data['name'] . '.png';
        $static_icon = '.' . ROOT_DIR . 'static/' . $data['name'] . '/' . $data['name'] . '.png';
        if (!empty($tmp_icon) && copy('.'.$tmp_icon, $app_icon) && copy('.'.$tmp_icon, $static_icon)) {
            $file = '.' . $tmp_icon;
            $path = pathinfo($file);
            if (is_file($file) && is_dir($path['dirname']) && Dir::delDir($path['dirname']) === false) {
                @unlink("$path");
            }
        }
        // 复制默认应用图标
        if (empty($tmp_icon)) {
            copy('./static/system/image/app.png', $static_icon);
            copy('./static/system/image/app.png', $app_icon);
        }
        // 将生成的模块信息添加到模块管理表
        $sql = [];
        $sql['name'] = $data['name'];
        $sql['identifier'] = self::$identifier;
        $sql['title'] = $data['title'];
        $sql['intro'] = $data['intro'];
        $sql['author'] = $data['author'];
        $sql['icon'] = ltrim($static_icon,'.');
        $sql['version'] = $data['version'];
        $sql['url'] = $data['url'];
        $sql['config'] = '';
        $sql['status'] = 0;
        self::create($sql);
        return true;
    }

    /**
     * 生成目录
     * @param array $list 目录列表
     */
    public static function mkDir($list)
    {
        $root_path = Env::get('root_path');
        foreach ($list as $dir) {
            if (!is_dir($root_path . $dir)) {
                // 创建目录
                mkdir($root_path . $dir, 0755, true);
            }
        }
    }

    /**
     * 生成模块控制器
     */
    public static function mkControl($path = '', $data = [])
    {
        // 删除默认控制器目录和文件
        unlink($path.'controller/Index.php');
        rmdir($path.'controller');
        // 生成后台默认控制器
        if (is_dir($path.'admin')) {
            $admin_contro = "<?php\nnamespace app\\".$data["name"]."\\admin;\nuse app\system\admin\Admin;\n\nclass Index extends Admin\n{\n\n    protected " . '$oneModel' . " = '" . ucfirst($data['name']) . "';\n\n    public function index()\n    {\n        if (" . '$this' . "->request->isAjax()) {\n            " . '$data' . " = [];\n            return " . '$this' . "->success('获取成功', '', " . '$data' . ");\n        }\n        return ".'$this->fetch()'.";\n    }\n}";
            // 删除框架生成的html文件
            @unlink($path . 'view/index/index.html');
            file_put_contents($path . 'admin/Index.php', $admin_contro);
            file_put_contents($path . 'view/index/index.html', "<div v-cloak id=\"app\" class=\"main-content\">\n    <one-table :option=\"option\" :data=\"data\" :loading=\"loading\"  @current-change=\"handleCurrentChange\" @size-change=\"handleSizeChange\">\n        <!-- 工具菜单 -->\n        <template slot=\"menuLeft\">\n            <el-button type=\"primary\" size=\"mini\" @click=\"handleAdd\">添加</el-button>\n        </template>\n        <!-- 自定义列 -->\n        <template slot=\"status\" slot-scope=\"scope\">\n            <el-tag v-if=\"scope.row.status == 1\">正常</el-tag>\n            <el-tag v-else>禁用</el-tag>\n        </template>\n        <!-- 自定义操作列 -->\n        <template slot=\"operates\" slot-scope=\"scope\">\n            <el-button size=\"mini\" :disabled=\"scope.row.system\" class=\"_tool\"\n                @click=\"handleEdit(scope.row, scope.index)\">\n                <i class=\"el-icon-edit\"></i>\n            </el-button>\n            <el-popconfirm\n                confirm-button-text=\"好的\"\n                cancel-button-text=\"不用了\"\n                icon=\"el-icon-info\"\n                icon-color=\"red\"\n                title=\"删除不能恢复,确定删除吗？\"\n                @confirm=\"handleDel(scope.row, scope.index)\"\n            >\n            <el-button :disabled=\"scope.row.system\" slot=\"reference\" size=\"mini\" class=\"_tool\" type=\"danger\">\n                <i class=\"el-icon-delete\"></i>\n            </el-button>\n            </el-popconfirm>\n        </template>\n    </one-table>\n</div>\n{include file=\"common@components/one-table\"}\n<script>\n    new Vue({\n        el: \"#app\",\n        data() {\n            return {\n                loading: false,\n                data: [],\n                option: {\n                    index: true,\n                    header: true,\n                    selection: false,\n                    operates: {width:100},\n                    page: {},\n                    size: \"mini\",\n                    column: [\n                        {\n                            label: \"状态\",\n                            prop: \"status\",\n                            slot: true\n                        },\n                    ]\n                }\n            }\n        },\n        created() {\n            this.getData()\n        },\n        methods: {\n            // 获取数据\n            getData() {\n                let _this = this\n                _this.loading = true\n                request({\n                    params: {\n                        s: \"".$data["name"]."/index/index\",\n                        page: _this.option.page.currentPage,\n                        limit: _this.option.page.pageSize,\n                    },\n                    method: \"post\"\n                }).then(e => {\n                    _this.loading = false;\n                    _this.data = e.data.data.list;\n                    _this.option.page.total = e.data.data.count;\n                })\n            },\n            //分页\n            handleCurrentChange(currentPage) {\n                let _this = this;\n                _this.option.page.currentPage = currentPage;\n                _this.getData();\n            },\n            //每页显示数\n            handleSizeChange(val) {\n                let _this = this;\n                _this.option.page.pageSize = val;\n                _this.getData();\n            },\n            //改变状态\n            changeSwitch(row) {\n                let _this = this;\n                _this.loading = true;\n                request({\n                    params: {\n                        s: \"".$data["name"]."/index/status/\",\n                        id: row.id,\n                        val: row.status,\n                    },\n                    method: \"post\"\n                }).then(e => {\n                    _this.loading = false;\n                })\n            },\n           \n            //删除行\n            handleDel (row, index) {\n                let _this = this\n                _this.loading = true\n                request({\n                    params: {\n                        s: \"".$data["name"]."/index/del/\",\n                		id:row.id,\n                	},\n                	method: \"post\"\n                }).then(e => {\n                    _this.data.splice(index, 1);\n                	_this.loading = false;\n                })\n            },\n            //添加\n            handleAdd() {\n                let _this = this\n                navigateTo({\n                    s: \"".$data["name"]."/index/edit\",\n                })\n            },\n            //编辑\n            handleEdit(row, index){\n                let _this = this\n                navigateTo({\n                    s: \"".$data["name"]."/index/edit\",\n                    id:row.id,\n                })\n            },\n           \n        }\n    })\n</script>");
            // 生成表单模版文件
            file_put_contents($path . 'view/index/form.html', "<div v-cloak id=\"app\" class=\"main-content\">\r\n    <div class=\"form-body\">\r\n        <one-form :config=\"config\" v-model=\"fdata\" ref=\"ruleForm\">\r\n            <div slot=\"menuLeft\">\r\n                <el-button class=\"button-item\" :loading=\"btnLoading\" type=\"primary\" @click=\"store('ruleForm')\" size=\"small\">\r\n                    保存\r\n                </el-button>\r\n                <el-button size=\"small\" @click=\"backpage()\">返回</el-button>\r\n            </div>\r\n            <!-- 上传图片 -->\r\n            <template slot=\"image\" slot-scope=\"scope\">\r\n                <one-upload class=\"one-attachment-simple-upload\" v-loading=\"uploading\" :disabled=\"uploading\"\r\n                    :accept=\"'image'\"\r\n                    @success=\"uploadSuccess(" . '$event' . ",scope.row.prop)\"\r\n                    flex=\"main:left cross:center\">\r\n                    <el-avatar fit=\"scale-down\" shape=\"square\" size=\"60\" :src=\"fdata[scope.row.prop]\"></el-avatar>\r\n                </one-upload>\r\n            </template>\r\n        </one-form>\r\n    </div>\r\n</div>\r\n{include file=\"common@components/one-form\"}\r\n{include file=\"common@components/one-upload\"}\r\n<script>\r\n    const _formData = {:json_encode((array)".'$formData'.", 1)} ;\r\n    const app = new Vue({\r\n        el: \"#app\",\r\n        data() {\r\n            return {\r\n                uploading:false,\r\n                loading:false,\r\n                btnLoading:false,\r\n                config: {\r\n                    formdesc: [\r\n                        {\r\n                            label: \"标题 :\",\r\n                            prop: \"title\",\r\n                            type: \"input\",\r\n                            rules:[{ required: true, message: \"标题不能为空\"}],\r\n                            bind: {\r\n                                \"placeholder\": \"请输入标题\",\r\n                            },\r\n                        },\r\n                        {\r\n                            label: \"图片 :\",\r\n                            prop: \"img\",\r\n                            type: \"image\",\r\n                            content: \"图片\",\r\n                        },\r\n                    ],\r\n                    labelWidth:\"200px\",\r\n                    rowSize: 1, //一行可以展示几列表单，默认为3列\r\n                },\r\n                fdata:{\r\n                    id: 0,\r\n                    title: \"\",\r\n                    img: \"\",\r\n                },\r\n            }\r\n        }, \r\n        created() {\r\n        },\r\n        mounted(){\r\n            if (Object.keys(_formData).length > 0) {\r\n                for(let key in this.fdata){\r\n                    this.fdata[key] = _formData[key];\r\n                }\r\n            }\r\n        },\r\n        methods: {\r\n            //表单验证\r\n            getFormPromise(form) {\r\n                return new Promise(resolve => {\r\n                    form.validate(res => {\r\n                        resolve(res);\r\n                    })\r\n                })\r\n            },\r\n            store(formName) {\r\n                let _this = this;\r\n                // 获取到组件中的form\r\n                const configForm = this.".'$refs'.".ruleForm.".'$refs'.".ruleForm;\r\n                // 使用Promise.all去校验结果,可加入多个表单\r\n                Promise.all([configForm].map(this.getFormPromise)).then(res => {\r\n                    const validateResult = res.every(item => !!item);\r\n                    if (validateResult) {\r\n                        _this.loading = true;\r\n                        request({\r\n                            params: {\r\n                                s: \"".$data["name"]."/index/edit\"\r\n                            },\r\n                            method: \"post\",\r\n                            data: _this.fdata\r\n                        }).then(e => {\r\n                            _this.loading = false;\r\n                            if (e.data.code == 0) {\r\n                                _this.backpage()\r\n                            } else {\r\n                                _this.".'$message'.".error(e.data.msg);\r\n                            }\r\n                        })\r\n\r\n                    } else {\r\n                        console.log(\"表单未校验通过\");\r\n                    }\r\n                })\r\n                \r\n            },\r\n            backpage(){\r\n                navigateTo({\r\n                    s: \"".$data["name"]."/index/index\",\r\n                })\r\n            },\r\n            //图片上传完成\r\n            uploadSuccess(file,prop){\r\n                file=file.response.data.data.file\r\n                this.fdata[prop] = file\r\n            },\r\n        },\r\n\r\n    });\r\n</script>");
        }
        // 生成前台默认控制器
        if (is_dir($path.'home')) {
            $home_contro = "<?php\nnamespace app\\".$data["name"]."\\home;\nuse app\common\controller\Common;\n\nclass Index extends Common\n{\n    public function index()\n    {\n        return ".'$this->fetch()'.";\n    }\n}";
            file_put_contents($path . 'home/Index.php', $home_contro);
            file_put_contents('.'.ROOT_DIR.'theme/'.$data['name'].'/default/index/index.html', '前台模板[/theme/'.$data['name'].'/default/index/index.html]');
        }
    }

    /**
     * 生成SQL文件
     */
    public static function mkSql($path = '', $data)
    {
        if (!is_dir($path . 'sql')) {
            mkdir($path . 'sql', 0755, true);
        }
        file_put_contents($path . 'sql/install.sql', "/*\n sql安装文件*/\nCREATE TABLE `" . config('database.prefix') . "{$data["name"]}`  (\n  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,\n  `status` int(11) NOT NULL DEFAULT '1' COMMENT '状态 0 禁用 1 启用',\nPRIMARY KEY (`id`) USING BTREE\n) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '{$data["title"]}' ROW_FORMAT = Compact;");
        file_put_contents($path . 'sql/uninstall.sql', "/*\n sql卸载文件\n*/\nDROP TABLE IF EXISTS `" . config('database.prefix') . "{$data["name"]}`;");
        file_put_contents($path . 'sql/demo.sql', "/*\n 演示数据\n*/");
    }

    /**
     * 生成模块菜单文件
     */
    public static function mkMenu($path = '', $data = [])
    {
        // 菜单示例代码
        $menus = <<<INFO
<?php
/**
 * 模块菜单
 * 字段说明
 * url 【链接地址】格式：{$data['name']}/控制器/方法，可填写完整外链[必须以http开头]
 * param 【扩展参数】格式：a=123&b=234555
 */
return [
    [
        'pid'           => 0,
        'is_menu'       => '{$data['is_menu']}',
        'title'         => '{$data['title']}',
        'icon'          => '{$data['small_icon']}',
        'module'        => '{$data['name']}',
        'url'           => '{$data['name']}/index/index',
        'param'         => '',
        'target'        => '_self',
        'nav'           => 1,
        'sort'          => 100,
        'childs'         => [
            [
                'title' => '列表',
                'icon' => 'fa fa-credit-card',
                'module' => '{$data['name']}',
                'url' => '{$data['name']}/index/index',
                'param' => '',
                'target' => '_self',
                'debug' => 0,
                'system' => 0,
                'nav' => 1,
                'sort' => 0,
                'childs' => [
                    [
                    'title' => '编辑',
                    'icon' => 'fa fa-credit-card',
                    'module' => '{$data['name']}',
                    'url' => '{$data['name']}/index/edit',
                    'param' => '',
                    'target' => '_self',
                    'debug' => 0,
                    'system' => 0,
                    'nav' => 0,
                    'sort' => 0,
                    ],
                ],
            ],
        ]
    ],
];
INFO;
        file_put_contents($path . 'menu.php', $menus);
    }

    /**
     * 生成模块信息文件
     */
    public static function mkInfo($path = '', $data = [])
    {
        $identifier = self::$identifier;
        // 配置内容
        $config = <<<INFO
<?php
/**
 * 模块基本信息
 */
return [
    // 核心框架[必填]
    'framework' => 'thinkphp5.1',
    // 模块名[必填]
    'name'        => '{$data['name']}',
    // 模块标题[必填]
    'title'       => '{$data['title']}',
    // 模块唯一标识[必填]，格式：模块名.[应用市场ID].module.[应用市场分支ID]
    'identifier'  => '{$identifier}',
    // 主题模板[必填]，默认default
    'theme'        => 'default',
    // 模块图标[选填]
    'icon'        => '{$data['icon']}',
    // 模块简介[选填]
    'intro' => '{$data['intro']}',
    // 开发者[必填]
    'author'      => '{$data['author']}',
    // 开发者网址[选填]
    'author_url'      => 'https://www.ouenyi.cn/',
    // 版本[必填],格式采用三段式：主版本号.次版本号.修订版本号
    // 主版本号【位数变化：1-99】：当模块出现大更新或者很大的改动，比如整体架构发生变化。此版本号会变化。
    // 次版本号【位数变化：0-999】：当模块功能有新增或删除，此版本号会变化，如果仅仅是补充原有功能时，此版本号不变化。
    // 修订版本号【位数变化：0-999】：一般是 Bug 修复或是一些小的变动，功能上没有大的变化，修复一个严重的bug即发布一个修订版。
    'version'     => '{$data['version']}',
    // 模块依赖[可选]，格式[[模块名, 模块唯一标识, 依赖模块版本号, 版本号对比方式version_compare(当前的，需要的)  < lt；<= le；> gt；>= ge；==|= eq； !=|<> ne  ]]
    'module_depend' => [],
    // 插件依赖[可选]，格式[[插件名, 插件唯一标识, 依赖模块版本号, 版本号对比方式version_compare(当前的，需要的)  < lt；<= le；> gt；>= ge；==|= eq； !=|<> ne  ]]
    'plugin_depend' => [],
    // 模块数据表[有数据库表时必填,不包含表前缀]
    'tables' => [
        // 'table_name',
    ],
    // 原始数据库表前缀,模块带sql文件时必须配置
    'db_prefix' => 'db_',
    // 模块预埋钩子[非系统钩子，必须填写]
    'hooks' => [
        // '钩子名称' => '钩子描述'
    ],
    // 模块配置，格式['sort' => '100','title' => '配置标题','name' => '配置名称','type' => '配置类型','options' => '配置选项','value' => '配置默认值', 'tips' => '配置提示'],各参数设置可参考管理后台->系统->系统功能->配置管理->添加
    'config' => [],
];
INFO;
        file_put_contents($path . 'info.php', $config);
    }

    public static function mkThemeConfig($path, $data = [])
    {
        $str = '<?xml version="1.0" encoding="ISO-8859-1"?>
<root>
    <item id="title"><![CDATA[默认模板]]></item>
    <item id="version"><![CDATA[v1.0.0]]></item>
    <item id="time"><![CDATA['.date('Y-m-d H:i').']]></item>
    <item id="author"><![CDATA[OnePHP]]></item>
    <item id="copyright"><![CDATA[OnePHP]]></item>
    <item id="db_prefix"><![CDATA[db_]]></item>
    <item id="identifier" title="默认模板必须留空，非默认模板必须填写对应的应用标识"><![CDATA[]]></item>
    <item id="depend" title="请填写当前对应的模块标识"><![CDATA['.self::$identifier.']]></item>
</root>';
        file_put_contents($path.'config.xml', $str);
    }

    public static function defaultIcon($mod_path, $info)
    {
        // 处理默认图标
        $_icon_file = realpath($mod_path.'/'.$info['icon']);
        $_icon_save_file = '';
        if (!is_dir('./static/'.$info['name'].'/')) {
            Dir::create('./static/'.$info['name'].'/', 0755);
        }
        if(is_file($_icon_file)){
            $_icon_save_file = './static/'.$info['name'].'/'.$info['icon'];
            @copy($_icon_file,  $_icon_save_file);
        }
        return ltrim($_icon_save_file,'.');
    }
}
