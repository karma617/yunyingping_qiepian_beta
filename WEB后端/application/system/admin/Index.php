<?php
namespace app\system\admin;

use Env;
use one\Dir;

/**
 * 后台默认首页控制器
 * @package app\system\admin
 */

class Index extends Admin
{
    /**
     * 首页
     * @return mixed
     */
    public function index()
    {   
        return $this->fetch();
    }

    /**
     * 欢迎首页
     * @return mixed
     */
    public function welcome()
    {
        return $this->fetch('index');
    }

    /**
     * 天气
     */
    public function get_weather(){
        $ip = get_client_ip();
        $_cache_key = 'one_weather_'.ADMIN_ID.$ip;
        if(!cache($_cache_key)){
            $rs = \one\Http::get('https://www.tianqiapi.com/free/week?appid=23035354&appsecret=8YvlPNrz&ip='.$ip);
            cache($_cache_key,$rs,3600*3);
        }
        echo cache($_cache_key);
    }
    /**
     * 清理缓存

     * @return mixed
     */
    public function clear()
    {
        $path   = Env::get('runtime_path');
        $cache  = $this->request->param('cache/d', 0);
        $log    = $this->request->param('log/d', 0);
        $temp   = $this->request->param('temp/d', 0);

        if ($cache == 1) {
            Dir::delDir($path.'cache');
        }

        if ($temp == 1) {
            Dir::delDir($path.'temp');
        }

        if ($log == 1) {
            Dir::delDir($path.'log');
        }

        return $this->success('任务执行成功');
    }
}
