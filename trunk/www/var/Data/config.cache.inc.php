<?php
$cfg_webname = 'Tblog';
$cfg_nameinfo = '博客程序';
$cfg_keywords = '';
$cfg_description = '';
$cfg_style = 'default';
$cfg_indexurl = 'http://local.tblog/';
$cfg_state = '0';
$cfg_state_reason = '网站维护中';
$cfg_powerby = '&lt;p&gt;版权：艺程酒店预订网 Copyright 2009-2010 Corporation, All Rights Reserved&lt;/p&gt;&lt;p&gt;备案序号：备案信息提交审查中&lt;/p&gt;';
$cfg_rewrite = '1';
$cfg_rewrite_rule = 'ErrorDocument 404 /index.php?m=error
RewriteEngine on
RewriteRule ^index.html$				 /index.php [L]
RewriteRule ^([a-zA-Z0-9_]+)\.html$			 /index.php?m=$1 [L]
RewriteRule ^weather-([0-9]+)-([0-9]+)\.html$		 /index.php\?m=weather&amp;cityid=$1&amp;xid=$2 [L]

RewriteRule ^news-c([0-9]+)-([0-9]+)\.html$		 /index.php\?m=news&amp;class_id=$1&amp;page=$2 [L]
RewriteRule ^news-c([0-9]+)\.html$			 /index.php\?m=news&amp;class_id=$1 [L]
RewriteRule ^newsinfo-([0-9]+)\.html$			 /index.php\?m=newsinfo&amp;aid=$1 [L]

RewriteRule ^liansuo-([0-9]+)-([0-9]+)\.html$		 /index.php\?m=liansuohotel&amp;cityid=$1&amp;chain_id=$2 [L]
RewriteRule ^liansuo-([a-zA-Z_]+)-([0-9]+)\.html$	 /index.php\?m=liansuohotel&amp;$1=$2 [L]

RewriteRule ^city-([0-9]+)\.html$			 /index.php\?m=city&amp;cityid=$1 [L]

RewriteRule ^hotel-([0-9]+)-([0-9]+)\.html$		 /index.php\?m=hotelinfo&amp;cityid=$1&amp;hotel_id=$2 [L]
RewriteRule ^hotel-([0-9]+)\.html$			 /index.php\?m=hotelinfo&amp;hotel_id=$1 [L]
RewriteRule ^(hotelmap|hotelcomment|hotelnearby|hotelpicture|hotelquestion|hotellable)-([0-9]+)\.html$	/index.php\?m=$1&amp;hotel_id=$2 [L]

RewriteRule ^(comment|question|weather|searchlist)-([0-9]+)-([0-9]+)\.html$	 /index.php\?m=$1&amp;cityid=$2&amp;page=$3 [L]
RewriteRule ^hotellist-([0-9]+)\.html?([\w%]*)$		 /index.php\?m=hotellist&amp;cityid=$1&amp;$2 [QSA,L]
RewriteRule ^(comment|question|weather|map|hotellist|searchlist)-([0-9]+)\.html$	 /index.php\?m=$1&amp;cityid=$2 [L]

RewriteRule ^lable-cityid([0-9]+)-([0-9]+)-([0-9]+)\.html$	/index.php\?m=lable&amp;cityid=$1&amp;classid=$2&amp;page=$3 [L]
RewriteRule ^lable-(pid|cityid)([0-9]+)-([0-9]*)\.html$	 /index.php\?m=lable&amp;$1=$2&amp;classid=$3 [L]
RewriteRule ^lable-([0-9]+)\.html$				/index.php\?m=lable&amp;classid=$1 [L]

RewriteRule ^admin$		 /index.php\?m=admin [L]';
?>