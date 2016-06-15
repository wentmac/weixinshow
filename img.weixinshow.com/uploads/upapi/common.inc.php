<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: common.inc.php 6 2014-09-20 15:13:57Z zhangwentao $
 */
error_reporting(0);
define('CFG_ROOT', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR); //图片上传保存的根目录 “\Public\uploads\”
define('STATIC_ROOT', substr(dirname(__FILE__), 0, -13)); //图片上传保存的根目录 “\Public\”
include STATIC_ROOT . 'images.config.php';
define('CFG_PICURL', CFG_INDEX_URL . 'thumb/');

//转义GET/POST/
function filter(&$array)
{
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            is_array($value) ? filter($value) : $array[$key] = addslashes($value);
        }
    }
}

if (!get_magic_quotes_gpc()) {
    filter($_GET);
    filter($_POST);
    filter($_COOKIE);
    filter($_FILES);
}
function_exists('ob_gzhandler') ? ob_start('ob_gzhandler') : ob_start(); //开启页面缓存
date_default_timezone_set('PRC');
?>

