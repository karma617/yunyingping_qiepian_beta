<?php
namespace think;
header('Content-Type:application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods:GET,POST");
header("Access-Control-Allow-Headers: token,secret,timestamp,t,sign,karma,content-type,did");
if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS') exit('Connected!');
if(version_compare(PHP_VERSION,'5.6.0','<'))  die('PHP版本过低，最少需要PHP5.6，请升级PHP版本！');
define('APP_PATH', __DIR__ . '/application/');
define('ENTRANCE','api');
define('DOC_ROOT', __DIR__ );
define('DS', DIRECTORY_SEPARATOR);
require __DIR__ . '/../library/Base.php';
require __DIR__ . '/../thinkphp/base.php';
Container::get('app')->run()->send();