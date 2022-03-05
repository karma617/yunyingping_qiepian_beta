<?php

/**
 * 七牛上传驱动
 * https://developer.qiniu.com/kodo/1671/region-endpoint-fq
 */

namespace app\system\admin\album\driver;

class QiniuDriver implements UploadInterface
{

    protected $config = [
        'ak' => '',
        'sk' => '',
        'bucket' => '',
        'domain' => '',
        'url' => 'http://upload-z2.qiniup.com'

    ];
    protected $errorMsg = '';

    public function __construct($config = array())
    {
        $this->config = array_merge($this->config, $config);
    }

    public function rootPath($path)
    {
        if (empty($this->config['ak']) || empty($this->config['sk']) || empty($this->config['bucket']) || empty($this->config['domain'])) {
            $this->errorMsg = '请先配置七牛上传参数！';
            return false;
        }
        return true;
    }

    public function checkPath($path)
    {
        return true;
    }

    public function saveFile($file)
    {
        $uploadToken = $this->uploadToken();
        $subPath = isset($this->config['subpath']) ? $this->config['subpath'] : time();
        $_file_name =  $file->getInfo('name');
        $_file_ext = pathinfo($_file_name, PATHINFO_EXTENSION);
        $name = $subPath . '/';
        $name .= md5($_file_name) . '.' . $_file_ext;
        $postFields = array(
            'token' => $uploadToken,
            'file'  => curl_file_create($file->getInfo('tmp_name'), $_file_ext, $name),
            'key' => $name
        );
        
        $data = $this->curl($this->config['url'], $postFields);
        if (empty($data)) {
            $this->errorMsg = '图片服务器连接失败！';
            return false;
        }
        $data = json_decode($data, true);
        if (isset($data['error'])) {
            $this->errorMsg = $data['error'];
            return false;
        }
        $fileData['pic_name'] = $_file_name;
        $fileData['pic_path'] = $this->config['domain'] . '/' . $name;
        $fileData['pic_hash'] = $data['hash'];

        return $fileData;
    }
    public function delFile($file)
    {
        $file=ltrim(parse_url($file)['path'],'/');
        $entry = "{$this->config['bucket']}:{$file}";
        $url = 'http://rs.qiniu.com/delete/'.$this->encode($entry);
        $header = [$this->authorization($url)];
        $data = $this->curl($url, [],$header);
        return json_decode($data, true);
    }

    public function curl($url, $post_data = array(), $header = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3000);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3000);
        curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_HOST']);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);
        $ret = curl_errno($ch);
        if ($ret !== 0) {
            return curl_error($ch);
            curl_close($ch);
        }
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $result;
    }

    public function getError()
    {
        return $this->errorMsg;
    }

    protected function uploadToken($param = [])
    {
        $deadline = time() + 3600;
        $data = array('scope' => $this->config['bucket'], 'deadline' => $deadline);
        $data = array_merge($data, $param);
        $data = json_encode($data);
        $data = $this->encode($data);
        return $this->sign($this->config['sk'], $this->config['ak'], $data) . ':' . $data;
    }

    protected function encode($str)
    {
        $find = array('+', '/');
        $replace = array('-', '_');
        return str_replace($find, $replace, base64_encode($str));
    }

    protected function sign($sk, $ak, $data)
    {
        $sign = hash_hmac('sha1', $data, $sk, true);
        return $ak . ':' . $this->encode($sign);
    }

    protected function authorization($url, $body = null, $contentType = null)
    {
        $url = parse_url($url);
        $data = '';
        if (array_key_exists('path', $url)) {
            $data = $url['path'];
        }
        if (array_key_exists('query', $url)) {
            $data .= '?' . $url['query'];
        }
        $data .= "\n";

        if ($body !== null && $contentType === 'application/x-www-form-urlencoded') {
            $data .= $body;
        }

        $sign = $this->sign($this->config['sk'], $this->config['ak'], $data);
        $auth = 'QBox '.$sign;
        return 'Authorization:'.$auth;
    }
}
