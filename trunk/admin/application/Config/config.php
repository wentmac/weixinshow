<?php

/**
 * zhuna_php 住哪网酒店分销联盟程序php版　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: config.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */

//软件摘要信息，****请不要删除本项**** 否则系统无法正确接收系统漏洞或升级信息
$TmacConfig['config']['version'] = 'V2';
$TmacConfig['config']['soft_lang'] = 'utf-8';
$TmacConfig['config']['soft_public'] = 'base';
$TmacConfig['config']['backup_dir'] = 'backupdata';   //数据库备份存放文件夹名

$TmacConfig['config']['admin_title'] = '后台管理系统';
$TmacConfig['config']['softname'] = 'api';
$TmacConfig['config']['soft_enname'] = 'TBlog_Professional';
$TmacConfig['config']['soft_devteam'] = 'TBlog-PHP团队';

//$TmacConfig['Session']['start'] = true;                 //是否自动开启Session 您可以在控制器中初始化，也可以在系统中自动加载 HttpResponse::session();