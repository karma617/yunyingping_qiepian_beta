<?php
namespace app\system\admin\album;
use app\system\admin\Admin;
use app\system\model\SystemUpload;
use one\Dir;
use Env;

class Drive extends Admin
{
    protected $oneModel = 'SystemUpload';

    protected function initialize()
    {
        parent::initialize();
        $this->driverPath = Env::get('app_path') . 'system/admin/album/driver/';
        Dir::create($this->driverPath);
        $this->tabData = [
            'column' => [
                ['label' => '附件管理', 'name' => 'album', 'url' => url('system/album/index')],
                ['label' => '回收管理', 'name' => 'recycle', 'url' => url('system/album/recycle')],
                ['label' => '上传配置', 'name' => 'config', 'url' => url('system/album.drive/index')],
            ],
            'current' => 'album'
        ];
    }
    public function index()
    {
        if ($this->request->isAjax()) {
            $rows = SystemUpload::order('sort asc')->column('code,id,title,applies,intro,status,sort', 'code');
            
            $dlist = Dir::getList($this->driverPath);
            
            $payment = [];
            foreach ($dlist as $k => $v) {
                if (!is_dir($this->driverPath . $v)) {
                    continue;
                }

                $xml = file_get_contents($this->driverPath . $v . '/config.xml');
                $config             = xml2array($xml);
                $config['install']  = 0;
                $config['id']       = 0;
                $config['sort']     = 100;

                if (array_key_exists($v, $rows)) {
                    $config['status']   = $rows[$v]['status'];
                    $config['install']  = 1;
                    $config['id']       = $rows[$v]['id'];
                    $config['sort']     = $rows[$v]['sort'];
                }

                if (isset($payment[$config['sort']])) {
                    $payment[$k] = $config;
                } else {
                    $payment[$k] = $config;
                }
            }

            ksort($payment);
            return $this->success('', '', array_values($payment));
        }

        $this->tabData['current'] = 'config';
        $this->assign('tabData', $this->tabData);
        return $this->fetch();
    }

    /**
     * 安装上传方式
     * @return mixed
     */
    public function install($code = '')
    {
        if (empty($code)) {
            return $this->error('参数传递错误！');
        }

        $map        = [];
        $map['code'] = $code;
        $xml        = file_get_contents($this->driverPath . $code . '/config.xml');
        $xmlData    = xml2array($xml);

        if ($this->request->isPost()) {

            if (SystemUpload::where($map)->find()) {
                return $this->error('请勿重复安装');
            }

            $config = $this->request->post();
            unset($config['code'], $config['_csrf']);
            $sqlmap             = [];
            $sqlmap['code']     = $xmlData['code'];
            $sqlmap['title']    = $xmlData['title'];
            $sqlmap['intro']    = $xmlData['intro'];
            $sqlmap['applies']  = $xmlData['applies'];
            $sqlmap['sort']     = 100;
            $sqlmap['status']   = 1;
            $sqlmap['config']   = $config['config'];

            if (!SystemUpload::create($sqlmap)) {
                return $this->error('安装失败');
            }

            return $this->success('安装成功', url('index'));
        }

        if (SystemUpload::where($map)->find()) {

            $this->redirect(url('config?code=' . $code));
        }

        //组织表单
        $_config = $this->parase_form_desc($xmlData['config']);
        $xmlData['config'] = $_config;

        $this->tabData['current'] = 'pay';
        $this->assign('tabData', $this->tabData);
        $this->assign('formData', $xmlData);
        return $this->fetch();
    }

    /**
     * 卸载上传方式
     * @return mixed
     */
    public function uninstall($code = '')
    {
        if (empty($code)) {
            return $this->error('参数传递错误');
        }

        $map = [];
        $map['code'] = $code;
        $title = SystemUpload::where($map)->value('title');

        if (!SystemUpload::where($map)->delete()) {
            return $this->error($title . ' 卸载失败');
        }

        return $this->success($title . ' 卸载成功');
    }

    /**
     * 上传方式配置
     * @return mixed
     */
    public function config($code = '')
    {
        
        $map = [];
        $map['code'] = $code;
        $row = SystemUpload::where($map)->find();

        if (!$row) {
            return $this->error($code . ' 上传方式不存在！');
        }

        if ($this->request->isPost()) {
            $config = $this->request->post();
            if (!$row::update($config)) {
                return $this->error('保存失败');
            }

            // 更新缓存数据
            // OneUpload::lists('', true);
            return $this->success('保存成功', url('index'));
        }

        $config     = $row;
        $xml        = file_get_contents($this->driverPath . $code . '/config.xml');
        $xmlData = xml2array($xml);

        //组织表单
        $_config = $this->parase_form_desc($xmlData['config']);
        $xmlData['config'] = $_config;
        $xmlData['data'] = $config;

        $this->assign('formData', $xmlData);
        return $this->fetch();
    }
    /**
     * 处理表单
     *
     * @param [type] $xmlData
     * @param [type] $config
     * @return void
     * @author Leo <13708867890>
     * @since 2020-12-10 21:19:31
     */
    public function parase_form_desc($config)
    {
        $new = [];
        $new = array_map(function ($v, $prop) {
            if (isset($v['options'])) {
                $v['options'] = parse_attr($v['options']);
            }
            $v['prop'] = $prop;
            return $v;
        }, $config, array_keys($config));
        // var_dump($config);
        return $new;
    }
    public function help()
    {
        return $this->fetch();
    }
}