<?php

/**
 * Power By Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Tmac.config.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org�� 
 * 如果不作修改就是项目统一的配置值，如果要覆盖惯例配置的值，可在项目配置文件中设定和惯例不符的配置项
 */
!defined('TMAC_BASE_PATH') && exit('Access Denied!');
require(TMAC_BASE_PATH . 'database.config.php');
$TmacConfig = array();

/* Tmac�����Ŀ�������� */
$TmacConfig['Common']['urlrewrite'] = false;            //是否开启urlrewrite(隐藏index.php)
$TmacConfig['Common']['urlseparator'] = '.';            //Controller与Action的分隔符  以上几项修改后 需要删除Cache\template下的模板缓存
$TmacConfig['Common']['charset'] = 'UTF-8';             //文档编码(UTF-8|GB2312)
$TmacConfig['Common']['timezone'] = 'Asia/Chongqing';   //时区
$TmacConfig['Common']['autofilter'] = true;             //是否进行自动对POST.GET.COOKIE进行过滤
$TmacConfig['Common']['gzip'] = true;                   //是否启用gzip页面压缩
$TmacConfig['Common']['debug'] = false;     //是否开启DEBUG模式 上线后关闭
$TmacConfig['Common']['errorreport'] = true;           //是否开启页面报错 上线后关闭
$TmacConfig['Common']['url_case_insensitive'] = true;  //URL地址是否不区分大小写 建议true不区分url大小写
$TmacConfig['Common']['cookiepre'] = 'tmac_';           //cookie前缀
$TmacConfig['Common']['cookiecrypt'] = true;            //cookie是否加密 建议您调试程序的时候关闭 发布程序的时候开启

/* Tmac框架项目默认数据库配置 */
$TmacConfig['Common']['Database'] = 'default';          //Tmac 数据库设置 选择数据库 支持多库

/* Tmac框架项目模板相关设置 */
$TmacConfig['Template']['template'] = 'View';           //设置系统模板文件的存放目录
$TmacConfig['Template']['template_dir'] = 'default';    //设置前台模板目录名
$TmacConfig['Template']['cache_dir'] = 'Cache' . DIRECTORY_SEPARATOR . 'template';        //指定模板缓存文件存放目录
$TmacConfig['Template']['auto_update'] = true;          //当模板文件有改动时重新生成缓存 [关闭该项会快一些]
$TmacConfig['Template']['cache_lifetime'] = 0;          //缓存生命周期(分钟)，为 0 表示永久 [设置为 0 会快一些]
$TmacConfig['Template']['suffix'] = '.tpl';            //模板文件后缀名
$TmacConfig['Template']['cache_open'] = false;          //是否开启缓存，程序调试时关闭,上线时打开

/* Tmac框架项目缓存设置 */
$TmacConfig['Cache']['class'] = 'File';                 //Tmac缓存方式 可以为File Apc EAccelerator
//File 设置
$TmacConfig['Cache']['File']['DATA_CACHE_SUBDIR'] = true;         //文件缓存方式开启子目录缓存
$TmacConfig['Cache']['File']['DATA_PATH_LEVEL'] = 2;              //哈希子目录缓存仅对File方式的缓存有效
//Memcached 设置
$TmacConfig['Cache']['Memcached']['host'] = '121.40.143.194';    //Memcached 主机
$TmacConfig['Cache']['Memcached']['port'] = 11212;          //Memcached 端口
$TmacConfig['Cache']['Memcached']['persistent'] = true;     //Memcached 长连接
$TmacConfig['Cache']['Memcached']['weight'] = 1;            //Memcached 权重
$TmacConfig['Cache']['Memcached']['timeout'] = 1;           //Memcached 连接时间
$TmacConfig['Cache']['Memcached']['compression'] = true;    //Memcached 压缩

/* Tmac框架项目的SESSION设置 */
$TmacConfig['Session']['start'] = FALSE;                 //是否自动开启Session 您可以在控制器中初始化，也可以在系统中自动加载 HttpResponse::session();
$TmacConfig['Session']['name'] = 'TmacID';              //默认Session_name
$TmacConfig['Session']['type'] = 'File';            //默认Session类型 支持 DB 和 File memcache memcached 默认无需设置 除非扩展了session hander驱动
$TmacConfig['Session']['path'] = '';                    //采用默认的Session save path
$TmacConfig['Session']['expire'] = '';                  //默认Session有效期 get_cfg_var("session.gc_maxlifetime");
$TmacConfig['Session']['table'] = 'tmac_session';       //数据库Session方式表名

/* Tmac框架项目的COOKIE设置 */
$TmacConfig['Cookie']['domain'] = '.weixinshow.com';       //cookie���� $_SERVER['HTTP_HOST']

require(APPLICATION_ROOT . 'Config' . DIRECTORY_SEPARATOR . 'config.php');
?>