<?php

/**
 * 本地上传驱动
 */

namespace app\system\admin\album\driver;


class LocalDriver implements UploadInterface
{

    protected $config = array();
    protected $errorMsg = '';

    public function __construct($config = array())
    {
        $this->config = $config;
    }

    public function rootPath($path)
    {
        if (!$this->mkdir('.' . $path)) {
            return false;
        }
        if (!is_writable('.' . $path)) {
            $this->errorMsg = '上传根目录不存在！';
            return false;
        }
        return true;
    }

    public function checkPath($path)
    {
        if (!$this->mkdir('.' . $path)) {
            return false;
        } else {
            if (!is_writable('.' . $path)) {
                $this->errorMsg = "上传目录 '{$path}' 不可写入！";
                return false;
            } else {
                return true;
            }
        }
    }

    public function saveFile($file)
    {
        
        $file_path = $this->config['rootPath'];
        // 检测目录
        $checkpath_result = $this->checkPath($file_path);//验证写入文件的权限
        if (!$checkpath_result){
            return $checkpath_result;
        }
        //上传
        $upload = $file->rule($this->config['saveRule'])->move($file_path);
        if(!$upload){
            $this->errorMsg = $upload->getError();
            return false;
        }
        //是否生成水印TODO
        //是否生成缩略图TODO
        $data['pic_name'] =  $upload->getInfo('name');
        $data['pic_path'] =  str_replace('\\','/','/'.$file_path.'/'.$upload->getSaveName());
        $data['pic_hash'] =  $upload->hash('md5');
        return $data;
    }

    public function delFile($file){
        @unlink('.'.$file);
        return true;
    }
    public function mkdir($path)
    {
        $dir = $path;
        if (is_dir($dir)) {
            return true;
        }
        try {
            mkdir($dir, 0777, true);
        } catch (\Exception $e) {
            $this->errorMsg = "上传目录 '{$path}' 创建失败！";
            return false;
        }
        return true;
    }

    public function getError()
    {
        return $this->errorMsg;
    }
}
