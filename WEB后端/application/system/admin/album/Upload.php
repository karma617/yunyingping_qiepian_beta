<?php

namespace app\system\admin\album;

use app\system\model\SystemUpload;
use app\common\model\Album;
use app\common\model\AlbumPic;

class Upload
{

    protected $uploader = null;

    /**
     * 上传配置
     * @var array
     */
    protected $config = [
        'maxSize' => 1048576, //上传的文件大小限制 默认10M
        'allowExts' => [], //允许的文件后缀
        'rootPath' => 'upload', //上传根路径
        'savePath' => '', //保存路径
        'saveRule' => 'date', //命名规则
        'driver' => 'Local',
        'config' => [],
    ];

    /**
     * 上传文件信息
     * @var array
     */
    protected $uploadFileInfo = [];

    /**
     * 错误消息
     * @var string
     */
    protected $errorMsg = '';

    /**
     * 构建函数
     * @param array $config 上传配置
     */
    public function run($params)
    {

        $this->config = array_merge($this->config, $params);
        $this->setDriver();
        try {
            $res = $this->upload($params);
            if(false === $res){
                return $res = [
                    'code'=>1,
                    'msg'=>$this->getError()
                ];
            }
        } catch (\Exception $e) {
            return $res = [
                'code'=>1,
                'msg'=>$e->getMessage()
            ];
        }
        (new AlbumPic())->addAlbumPic($this->getUploadFileInfo());
        return $this->getUploadFileInfo();
    }

    /**
     * 上传远程文件
     * @param $url
     */
    public function uploadRemote($url)
    {
        //TODO
    }

    /**
     * 删除文件
     */
    public function delFile($params)
    {
        $this->config = array_merge([],$this->config, $params);
        $this->setDriver();
        $res = $this->uploader->delFile($params['file']);
        return $res;
    }

    /**
     * 上传配置
     * @param  string $param 上传字段
     * @return boolean
     */
    public function upload($param)
    {
        $input = isset($param['input']) ? $param['input'] : 'file'; //表单域名称
        $album_id = isset($param['album_id']) ? $param['album_id'] : 0; //分组ID
        $file = request()->file($input);
        $check_res = $this->checkImg($file);
        if (!$check_res) {
            return $check_res;
        }
        //加入必要属性
        $tmp_name = $file->getInfo('tmp_name');//获取上传缓存文件
        $extension = strtolower(pathinfo($file->getInfo('name'), PATHINFO_EXTENSION));
        
        //存储文件
        $info = $this->uploader->saveFile($file,$param);
        if (!$info) {
            $this->errorMsg = $this->uploader->getError();
            return false;
        }
        $info['album_id'] = $album_id;
        $info['drive'] = $this->config['driver'];
        $info['ext'] = $extension;
        $this->uploadFileInfo = $info;
        if (is_file($tmp_name)) {
            @unlink($tmp_name);
        }
        return true;
    }

    /**
     * 图片验证
     * @param $file
     * @return \multitype
     */
    protected function checkImg($file)
    {

        $rule_array = [];
        $size_rule = config('upload.upload_file_size');
        // $size_rule = 1;
        $ext_rule = config('upload.upload_image_ext') . ',' . config('upload.upload_file_ext');
        $mime_rule = null;
        if ($file->getMime() == 'text/x-php' || $file->getMime() == 'text/html') {
            $this->errorMsg = '禁止上传php,html文件！';
            return false;
        }
        if (!empty($size_rule)) {
            $rule_array['size'] = $size_rule;
        }
        if (!empty($ext_rule)) {
            $rule_array['ext'] = $ext_rule;
        }
        if (!empty($mime_rule)) {
            $rule_array['type'] = $mime_rule;
        }
        if (!empty($rule_array)) {
            if (!$file->check($rule_array)) {
                $this->errorMsg = $file->getError();
                return false;
            }
        }
        return true;
    }

    /**
     * 设置驱动
     */
    protected function setDriver()
    {
        if('Local' == $this->config['driver']){
            $config = $this->config;
        }else{
            $drive = SystemUpload::lists($this->config['driver']);
            if (!$drive ) {
                throw new \Exception("[{$drive['title']}]上传方式未安装或未开启！", 1);
            }
            $config = array_merge($this->config,$drive['config']);
        }

        
        $uploadDriver = __NAMESPACE__ . '\driver\\' . ucfirst($this->config['driver']) . 'Driver';
        $this->uploader = new $uploadDriver($config);
        if (!$this->uploader) {
            throw new \Exception("Upload Driver '{$this->config['driver']}' not found'", 500);
        }
    }

    /**
     * 获取上传文件信息
     * @return array
     */
    public function getUploadFileInfo()
    {
        return $this->uploadFileInfo;
    }

    /**
     * 获取框架错误
     * @return string
     */
    public function getError()
    {
        return $this->errorMsg;
    }
}
