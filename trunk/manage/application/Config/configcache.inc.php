<?php
$config['config']['cfg_webname'] = 'Tblog12';
$config['config']['cfg_nameinfo'] = '博客程序';
$config['config']['cfg_keywords'] = 'Tblog互联网,SNS,交互设计,界面设计,可用性设计,产品设计,用户体验,HTML,网页结构,WEB标准';
$config['config']['cfg_description'] = 'Tblog专注于互联网潮流，可用性分析，以用户为中心的设计，软件设计，网页设计等的专业博客';
$config['config']['cfg_style'] = 'default';
$config['config']['cfg_indexurl'] = 'http://local.tblog/';
$config['config']['cfg_state'] = '1';
$config['config']['cfg_state_reason'] = '网站维护中';
$config['config']['cfg_powerby'] = '&lt;p&gt;版权：艺程酒店预订网 Copyright 2009-2010 Corporation, All Rights Reserved&lt;/p&gt;&lt;p&gt;备案序号：备案信息提交审查中&lt;/p&gt;';
$config['config']['cfg_rewrite'] = '1';
$config['config']['cfg_rewrite_rule'] = '#ErrorDocument 404 /index.php?m=error
&lt;Files *.tpl&gt;
Order Allow,Deny    
Deny from all
&lt;/Files&gt;
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