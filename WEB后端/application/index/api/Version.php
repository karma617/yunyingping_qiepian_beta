<?php

namespace app\index\api;

use app\one_api\api\ApiInit;

class Version extends ApiInit
{
    public function initialize()
    {
        parent::initialize();
    }

    public function ver()
    {
        $version = [
            'version' => config("app_config.app_version"),
            "url" => config("app_config.app_download"),
            "ffmpeg" => config("app_config.ffmpeg_download"),
            "ffmpeg_md5" => config("app_config.ffmpeg_md5"),
            "water_font" => config("app_config.water_font"),
        ];
        return $this->_success('获取成功', $version);
    }

    public function online()
    {
        $online_log = env('root_path') . "/count.txt";
        if (!file_exists($online_log)) {
            file_put_contents($online_log, '');
        }
        $timeout = 30; //30秒内没动作认为掉线
        $entries = file_get_contents($online_log);
        $entries = explode("\n", $entries);
        $temp = array();
        for ($i = 0; $i < count($entries); $i++) {
            $entry = explode(",", trim($entries[$i]));
            if (!empty(array_filter($entry))) {
                if (($entry[0] != getenv('REMOTE_ADDR')) && ($entry[1] > time())) {
                    array_push($temp, $entry[0] . "," . $entry[1] . "\n");
                }
            }
        }
        array_push($temp, getenv('REMOTE_ADDR') . "," . (time() + ($timeout)) . "\n");
        $users_online = count($temp); //计算在线人数
        $entries = implode("", $temp);
        file_put_contents($online_log, $entries);
        return $this->_success('获取成功', $users_online);
    }
}
