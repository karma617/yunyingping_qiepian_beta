<?php
// +----------------------------------------------------------------------
// | 微信支付
// +----------------------------------------------------------------------

if (!function_exists('arrayToXml')) {
    /*
    *array to xml
    */
    function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
             $xml.="<".$key.">".$val."</".$key.">"; 
        }
        $xml.="</xml>";
        return $xml; 
    }
}

if (!function_exists('postXmlCurl')) {
    /*
    *与微信通讯获得二维码地址信息，必须以xml格式
    */
    function postXmlCurl($xml, $url, $useCert = false, $certPem = '', $keyPem = '', $second = 30)
    {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);

        if (stripos($url,"https://")!==FALSE) {
            curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        } else {
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);
        }

        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

        if($useCert == true){
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            if (substr(0, 1) != '.') {
                $certPem = '.'.$certPem;
            }
            if (substr(0, 1) != '.') {
                $keyPem = '.'.$keyPem;
            }
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, $certPem);
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, $keyPem);
        }

        $data = curl_exec($ch);
        // curl_close($ch);
        //返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else { 
            $error = curl_errno($ch);
            curl_close($ch);
            return $error;
        }
    }
}

if (!function_exists('xmlToArray')) {
    /*
    *xml to array
    */
    function xmlToArray($xml)
    {   
        $xml = str_replace('--', '', $xml);
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);      
        return $array_data;
    }
}
