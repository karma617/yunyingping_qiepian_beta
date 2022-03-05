<?php

namespace app\install\one;

use app\common\controller\Common;
use think\exception\HttpException;
use app\system\model\SystemUser as UserModel;
use think\Db;
use think\facade\Log;
use Env;

class Error extends Common
{

    public function initialize()
    {
        $this->root_path = Env::get('root_path');
        $this->flag = true;
    }
    
    public function index()
    {
        
        if (is_file($this->root_path . '_install.lock')) {
            @rename($this->root_path . '_install.lock',$this->root_path . 'install.lock');
            return $this->redirect('/');
        }
        if (is_file(Env::get('app_path') . 'install.lock')) {
            return $this->error('如需重新安装，请手动删除/install.lock文件');
        }
        return $this->fetch('index');
    }

    /**
     * 第二步：环境检测
     * @return mixed
     */
    public function step2()
    {
        if ($this->request->isAjax()) {
            $data           = [];
            $data['env']    = self::checkNnv();
            $data['dir']    = self::checkDir();
            $data['func']   = self::checkFunc();
            $code = 0;
            $flag = $this->flag;
            return json(compact('data', 'code', 'flag'));
        }
    }


    /**
     * 第三步：执行安装
     * @return mixed
     */
    public function step3()
    {
        if (!$this->request->isPost()) {
            return $this->error('非法访问');
        }
        if (!is_writable($this->root_path . 'config/database.php')) {
            return $this->error('[config/database.php]无读写权限！');
        }

        $data = $this->request->post();

        $data['type'] = 'mysql';
        $rule = [
            'dbhost|服务器地址' => 'require',
            'dbport|数据库端口' => 'require|number',
            'dbname|数据库名称' => 'require',
            'dbuser|数据库账号' => 'require',
            'prefix|数据库前缀' => 'require|regex:^[a-z0-9]{1,20}[_]{1}',
            'cover|覆盖数据库'  => 'require|in:0,1',
            'acname|管理员账号' => 'require|alphaNum|length:4,20',
            'acpwd|管理员密码'  => 'require|length:6,20',
        ];

        $validate = $this->validate($data, $rule);

        if (true !== $validate) {
            return $this->error($validate);
        }

        // 创建数据库连接
        $db_connect = Db::connect("mysql://{$data['dbuser']}:{$data['dbpass']}@{$data['dbhost']}:{$data['dbport']}}#utf8");

        // 检测数据库连接
        try {
            $db_connect->execute('select version()');
        } catch (\Exception $e) {
            $this->error('数据库连接失败，请检查数据库配置！');
        }

        // 不覆盖检测是否已存在数据库
        if (!$data['cover']) {
            $check = $db_connect->execute('SELECT * FROM information_schema.schemata WHERE schema_name="' . $data['dbname'] . '"');
            if ($check) {
                $this->error('该数据库已存在，如需覆盖，请选择覆盖数据库！');
            }
        }

        // 创建数据库
        if (!$db_connect->execute("CREATE DATABASE IF NOT EXISTS `{$data['dbname']}` DEFAULT CHARACTER SET utf8")) {
            return $this->error($db_connect->getError());
        }

        // 生成配置文件
        $configFile    = Env::get('config_path') . 'database.php';

        $updateData    = [
            '{HOSTNAME}' => $data['dbhost'],
            '{DATABASE}' => $data['dbname'],
            '{USERNAME}' => $data['dbuser'],
            '{PASSWORD}' => $data['dbpass'],
            '{HOSTPORT}' => $data['dbport'],
            '{PREFIX}'   => $data['prefix'],
        ];
        $this->create_config($configFile, $updateData);
        //开始安装 并记录日志文件
        $this->success('数据库配置写入完成,即将初始化数据');
    }

    /**
     * 安装
     * @return mixed
     */
    public function setup()
    {
        if (!$this->request->isPost()) {
            return $this->error('非法访问');
        }
        $this->log('', true);
        $data = $this->request->post();
        extract($data);

        // 导入系统初始数据库结构
        $this->log('导入系统初始数据库结构');

        $sqlFile = Env::get('app_path') . 'install/sql/install.sql';

        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            $sqlList = parse_sql($sql, 0, ['one_' => $prefix]);
            if ($sqlList) {
                $sqlList = array_filter($sqlList);
                foreach ($sqlList as $v) {

                    try {
                        if (preg_match_all("/(create|drop|insert|replace into)([^`]+`)(\w+)(`.*)/i", $v, $out)) {

                            $op = strtolower($out[1][0]);
                            $message = '';
                            //动作
                            if ($op == 'create' && $v) $message = "创建表 " . ($out[3][0]) . " ";
                            else if ($op == 'drop' && $v) $message = "校验表 " . ($out[3][0]) . " ";
                            else if ($op == 'insert' && $v) $message = "写入表 " . ($out[3][0]) . " ";
                            else if ($op == 'replace into' && $v) $message = "写入表 " . ($out[3][0]) . " ";
                            //日志
                            $this->log($message);
                        }
                        Db::execute($v);
                    } catch (\Exception $e) {
                        $this->log($e->getMessage());
                    }
                }
            }
        }

        // 注册管理员账号
        $user = new UserModel;

        $map                    = [];
        $map['role_id']         = 1;
        $map['nick']            = '超级管理员';
        $map['username']        = $acname;
        $map['password']        = password_hash(md5($acpwd), PASSWORD_DEFAULT);
        try {
            $res = $user->create($map);
        } catch (\Exception $e) {
            $res = false;
            $this->log($e->getMessage());
        }
        if (!$res) {
            return $this->log($user->getError() ? $user->getError() : '管理员账号设置失败！');
        }
        $this->log('管理员账号设置完成');

        file_put_contents($this->root_path . '_install.lock', "如需重新安装，请手动删除此文件\n安装时间：" . date('Y-m-d H:i:s'));

        $this->log('锁定安装配置完成');
        //站点密匙
        $auth = password_hash(request()->time(), PASSWORD_DEFAULT);
        $one_auth = <<<INFO
<?php
return ['key' => '{$auth}'];
INFO;
        file_put_contents(Env::get('config_path') . 'one_auth.php', $one_auth);
        $this->log((PHP_EOL . '恭喜您！系统安装成功,后台地址(/admin.php)'));
        $this->log(('温馨提示：为保证安全，安装完成后请删除/application/install文件夹'));
        $this->log(('done'));
    }

    public function installDone()
    {
        @rename($this->root_path . '_install.lock',$this->root_path . 'install.lock');
    }

    /**
     * 环境检测
     * @return array
     */
    private function checkNnv()
    {
        $items = [
            'os'      => ['操作系统', '不限制', '类Unix', PHP_OS, 'ok'],
            'php'     => ['PHP版本', '5.6', '5.6及以上', PHP_VERSION, 'ok'],
            'gd'      => ['GD库', '2.0', '2.0及以上', '未知', 'ok'],
        ];

        if ($items['php'][3] < $items['php'][1]) {

            $items['php'][4] = 'no';
            $this->flag = false;
        }

        $tmp = function_exists('gd_info') ? gd_info() : [];

        if (empty($tmp['GD Version'])) {

            $items['gd'][3] = '未安装';
            $items['gd'][4] = 'no';
            $this->flag = false;
        } else {

            $items['gd'][3] = $tmp['GD Version'];
        }

        return $items;
    }

    /**
     * 目录权限检查
     * @return array
     */
    private function checkDir()
    {
        $items = [
            ['dir', $this->root_path . 'application', 'application', '读写', '读写', 'ok'],
            ['dir', $this->root_path . 'runtime', 'runtime', '读写', '读写', 'ok'],
            ['dir', $this->root_path . 'extend', 'extend', '读写', '读写', 'ok'],
            ['dir', $this->root_path . 'backup', 'backup', '读写', '读写', 'ok'],
            ['dir', './static', './static', '读写', '读写', 'ok'],
            ['dir', './upload', './upload', '读写', '读写', 'ok'],
            ['file', $this->root_path . 'config', 'config', '读写', '读写', 'ok'],
            ['file', $this->root_path . 'version.php', 'version.php', '读写', '读写', 'ok'],
            ['file', './admin.php', './admin.php', '读写', '读写', 'ok'],
        ];

        foreach ($items as &$v) {
            if ($v[0] == 'dir') { // 文件夹
                if (!is_writable($v[1])) {
                    if (is_dir($v[1])) {
                        $v[4] = '不可写';
                        $v[5] = 'no';
                    } else {
                        $v[4] = '不存在';
                        $v[5] = 'no';
                    }
                    $this->flag = false;
                }
            } else { // 文件
                if (!is_writable($v[1])) {
                    $v[4] = '不可写';
                    $v[5] = 'no';
                    $this->flag = false;
                }
            }
        }
        return $items;
    }

    /**
     * 函数及扩展检查
     * @return array
     */
    private function checkFunc()
    {
        $items = [
            ['pdo', '支持', 'yes', '类'],
            ['pdo_mysql', '支持', 'yes', '模块'],
            ['zip', '支持', 'yes', '模块'],
            ['fileinfo', '支持', 'yes', '模块'],
            ['curl', '支持', 'yes', '模块'],
            ['xml', '支持', 'yes', '函数'],
            ['file_get_contents', '支持', 'yes', '函数'],
            ['mb_strlen', '支持', 'yes', '函数'],
            ['gzopen', '支持', 'yes', '函数'],
        ];

        foreach ($items as &$v) {
            if (('类' == $v[3] && !class_exists($v[0])) || ('模块' == $v[3] && !extension_loaded($v[0])) || ('函数' == $v[3] && !function_exists($v[0]))) {
                $v[1] = '不支持';
                $v[2] = 'no';
                $this->flag = false;
            }
        }

        return $items;
    }


    /**
     * 根据默认模板生成config文件
     *
     * @param [type] $config_file
     * @param [type] $updateData
     * @return void
     * @author Leo <13708867890>
     * @since 2020-12-12 15:40:20
     */
    private function create_config($config_file, $updateData)
    {
        $defaultData = file_get_contents($config_file);
        $configData  = str_replace(array_keys($updateData), array_values($updateData), $defaultData);
        file_put_contents($config_file, $configData);
        return true;
    }
    /**
     * 记录日志
     *
     * @param [type] $msg
     * @param boolean $clean
     * @return void
     * @author Leo <13708867890>
     * @since 2020-12-12 14:28:30
     */
    public function log($msg, $clean = false)
    {
        $file = Env::get('runtime_path') . 'log' . DIRECTORY_SEPARATOR . 'install.log';
        if ($clean) {
            @unlink($file);
            return;
        }
        file_put_contents($file, PHP_EOL . $msg, FILE_APPEND);
    }
    /**
     * 按行读取配置文件
     *
     * @param integer $start
     * @param [type] $limit
     * @param integer $length
     * @return void
     * @author Leo <13708867890>
     * @since 2020-12-12 15:41:33
     */
    public function read_log($start = 1, $limit = null, $length = 40960)
    {
        $file = Env::get('runtime_path') . 'log' . DIRECTORY_SEPARATOR . 'install.log';
        $returnTxt = null; // 初始化返回
        $i = 1; // 行数
        $end = is_null($limit) ? $start : $start + $limit;
        if (!is_file($file)) {
            return $this->success('false');
        }
        $handle = fopen($file, "r");
        $data = [];
        while (!feof($handle)) {
            $buffer = fgets($handle, $length);
            if ($i >= $start && $i <= $end) {
                $data[] = ($buffer);
            }
            if ($i > $end) {
                break;
            }
            $i++;
        }
        fclose($handle);
        if (empty($data)) {
            return $this->success('false');
        }
        return $this->success('', '', $data);
    }
}
