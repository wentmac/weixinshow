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
$tmac_path = dirname(__FILE__); //当前目录
$tmac_base_path = substr(dirname(__FILE__), 0, -12); //上级目录
//Tmac物理根目录
define('TMAC_BASE_PATH', $tmac_base_path . DIRECTORY_SEPARATOR);
//定义MVC system核心目录
define('TMAC_PATH', TMAC_BASE_PATH . 'Framework' . DIRECTORY_SEPARATOR);
//定义项目名称和路径
define('APP_NAME', 'www');

//加载框架入口文件
require(TMAC_PATH . "TmacPHP.php");

//开启网站进程
new Tmac();
?>