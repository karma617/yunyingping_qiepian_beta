<?php

namespace app\one_pay\admin;

use app\system\admin\Admin;
use app\one_pay\model\OnePayPayment as PaymentModel;
use one\Dir;
use Env;

class Index extends Admin
{
    protected $oneModel = 'one_pay_payment';

    protected function initialize()
    {
        parent::initialize();
        $this->driverPath = Env::get('app_path') . 'one_pay/driver/';
        $this->tabData = [
            'column' => [
                ['label' => '在线支付', 'name' => 'pay', 'url' => url('one_pay/index/index')],
                ['label' => '支付日志', 'name' => 'log', 'url' => url('one_pay/logs/index')],
                ['label' => '使用帮助', 'name' => 'help', 'url' => url('one_pay/index/help')],
            ],
        ];
    }
    /**
     * 支付方式管理
     * @return mixed
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $rows = PaymentModel::order('sort asc')->column('code,id,title,applies,intro,status,sort', 'code');
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

        $this->tabData['current'] = 'pay';
        $this->assign('tabData', $this->tabData);
        return $this->fetch();
    }

    /**
     * 安装支付方式
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

            if (PaymentModel::where($map)->find()) {
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
            $sqlmap['config']   = json_encode($config, 1);

            if (!PaymentModel::create($sqlmap)) {
                return $this->error('安装失败');
            }

            return $this->success('安装成功', url('index'));
        }

        if (PaymentModel::where($map)->find()) {

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
     * 卸载支付方式
     * @return mixed
     */
    public function uninstall($code = '')
    {
        if (empty($code)) {
            return $this->error('参数传递错误');
        }

        $map = [];
        $map['code'] = $code;
        $title = PaymentModel::where($map)->value('title');

        if (!PaymentModel::where($map)->delete()) {
            return $this->error($title . ' 卸载失败');
        }

        return $this->success($title . ' 卸载成功');
    }

    /**
     * 支付方式配置
     * @return mixed
     */
    public function config($code = '')
    {
        if (empty($code)) {
            return $this->error('参数传递错误！');
        }
        $map = [];
        $map['code'] = $code;
        $row = PaymentModel::where($map)->find();

        if (!$row) {
            return $this->error($code . ' 支付方式不存在！');
        }

        if ($this->request->isPost()) {
            $config = $this->request->post();
            unset($config['code'],$config['_csrf']);
            
            $sqlmap = [];
            $sqlmap['id'] = $row['id'];
            $sqlmap['config'] = json_encode($config, 1);

            if (!PaymentModel::update($sqlmap)) {
                return $this->error('保存失败');
            }

            // 更新缓存数据
            PaymentModel::lists('', true);
            return $this->success('保存成功', url('index'));
        }

        $config     = json_decode($row['config'], 1);
        $xml        = file_get_contents($this->driverPath . $code . '/config.xml');
        $xmlData = xml2array($xml);

        //组织表单
        $_config = $this->parase_form_desc($xmlData['config']);
        $xmlData['config'] = $_config;
        $xmlData['data'] = $config['config'];

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
        return $new;
    }
    public function help()
    {
        return $this->fetch();
    }
}
