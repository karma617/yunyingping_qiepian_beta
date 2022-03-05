<?php
namespace app\index\home;

class Index
{
    public function index()
    {
        echo '<html><head><meta http-equiv="Content-Type"content="text/html; charset=UTF-8"/><meta http-equiv="X-UA-Compatible"content="IE=edge"/><meta name="viewport"content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"/><meta name="robots"content="noarchive"/><title>每日一图 - 必应</title><style>h1{color:#FFFFFF;text-align:center;font-family:Microsoft Jhenghei}p{color:#FFFFFF;font-size:1.2rem;text-align:center;font-family:Microsoft Jhenghei}</style></head><body style="background: #FFFFFF url(index/index/img) no-repeat fixed center;"></body></html>';
        die;
    }
    
    public function img()
    {
        $str = file_get_contents('https://cn.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1');
        $str = json_decode($str, true);
        $imgurl = 'https://cn.bing.com' . $str['images'][0]['url'];
        header("Location: {$imgurl}");
        die;
    }

}
