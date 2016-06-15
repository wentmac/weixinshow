<?php

/**
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: index.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org； 
 */
PHP_VERSION < '5.1' && die('Tmac is only available for use with PHP 5.1.0 or later.');

//Tmac执行时间开启
$TMAC_START_TIME = microtime(true);
//The directory in which your application specific resources are located.
define('APPLICATION', 'application');
//The directory in which your var are located.
define('VARROOT', 'var');
//Tmac返回项目文件路径的信息
$tmac_path = dirname(__FILE__);
$tmac_base_path = dirname(dirname(__file__));

//Tmac物理根目录
define('TMAC_BASE_PATH', $tmac_base_path . DIRECTORY_SEPARATOR);
//Tmac虚拟根目录
define('ROOT', str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));
//application目录
define('APPLICATION_ROOT', TMAC_BASE_PATH . APPLICATION . DIRECTORY_SEPARATOR);
//var目录
define('VAR_ROOT', TMAC_BASE_PATH . VARROOT . DIRECTORY_SEPARATOR);
//Webroot Htdocs 网站目录
define('WEB_ROOT', str_replace('\\', '/', dirname(__FILE__) . DIRECTORY_SEPARATOR));
//加载配置文件
require(TMAC_BASE_PATH . 'Tmac.config.php');

//###### add by zuncms
if (!file_exists(VAR_ROOT . 'Data/install.lock')) {
    header('Location:install/index.php');
    exit;
}
//###### over zuncms

ini_set('include_path',
        ini_get('include_path') . PATH_SEPARATOR
        . APPLICATION_ROOT . 'Tmac' . DIRECTORY_SEPARATOR . PATH_SEPARATOR
        . APPLICATION_ROOT . 'Tmac' . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . PATH_SEPARATOR
        . APPLICATION_ROOT . 'Tmac' . DIRECTORY_SEPARATOR . 'Cache' . DIRECTORY_SEPARATOR . PATH_SEPARATOR
        . APPLICATION_ROOT . 'Model' . DIRECTORY_SEPARATOR . PATH_SEPARATOR
        . APPLICATION_ROOT . 'Plugin' . DIRECTORY_SEPARATOR . PATH_SEPARATOR
        . APPLICATION_ROOT . 'Controller' . DIRECTORY_SEPARATOR . PATH_SEPARATOR
);

/**
 * 自动加载类
 *
 * @param string $class
 */
function __autoload($class)
{
    require_once($class . '.class.php');
}

//开启网站进程
new Tmac();
?>