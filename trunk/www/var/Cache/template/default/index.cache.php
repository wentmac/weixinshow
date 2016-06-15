<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\index.tpl', 'D:\Web\Work\www.090.cn\trunk\admin\application\View\default\index.tpl', 1456159584)
;?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="<?php echo $BASE_V;?>layout.css" rel="stylesheet" type="text/css" />
<script src="<?php echo STATIC_URL; ?>js/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script language="javascript">
jq = jQuery.noConflict(); 
/*
	以后jquery中的都用jq代替即可。 
*/
jq(document).ready(function(){
	jq('#menu li a').click(function(){
		jq("#menu li a").each(function(){
			jq(this).removeClass();
		});			
		jq(this).addClass("menu_li_a_hover");
	});
});
</script>
<title><?php echo $admin_title;?></title>
</head>
<body>

<div id="header">
  <div id="logo"><img src="<?php echo $BASE_V;?>images/logo.gif" /></div>
  <div id="header_r">欢迎您<?php echo $username;?>登录 | <a href="<?php echo PHP_SELF; ?>?m=index.body" target="main">管理主页</a> | <a href="http://www.t-mac.org" target="main">技术支持</a> | <a href="<?php echo PHP_SELF; ?>?m=cache" target="main">清理硬盘缓存</a> | <a href="<?php echo $indexurl;?>" target="_blank">网站主页</a> | <a href="<?php echo PHP_SELF; ?>?m=login.out" target="_top">退出登录</a></div>
</div>

<div class="menu" id="menu_box">
    <div id="menu">
	<?php if(is_array($menua)) foreach($menua AS $k => $sec1) { ?>
    <h4 class="menu_title" id="mt_<?php echo $k;?>" onclick="do_menu(<?php echo $k;?>)"><?php echo $sec1['title'];?></h4>
    <ul id="mb_<?php echo $k;?>" class="menu_body" style="display:none">
      <?php if(is_array($sec1['subname'])) foreach($sec1['subname'] AS $kk => $sec2) { ?>
      <li><?php echo $sec2;?></li>
      <?php } ?>
    </ul>
    <?php } ?>        
    </div>
</div>

<div class="main" id="body_box">
    <iframe id="main" name="main" frameborder="0" src="<?php echo PHP_SELF; ?>?m=index.body"></iframe>
</div>
<script type="text/javascript">
<!--
var winHeight=0;
function findDimensions() //函数：获取尺寸
{
	winHeight=document.documentElement.clientHeight;
	winWidth=document.documentElement.clientWidth;	
	var height = winHeight - 60;
	var width = winWidth - document.getElementById("menu_box").offsetWidth-20;	
	document.getElementById("menu_box").style.height = height+"px";
	document.getElementById("menu_box").style.background = 'url(<?php echo $BASE_V;?>images/left_bg.gif) right repeat-y';	
	document.getElementById("body_box").style.width = width+"px";	
	document.getElementById("body_box").style.height = height+"px";
}
findDimensions();
window.onresize=findDimensions;
//-->

function do_menu(id)
{
	var mb = document.getElementById("mb_"+id);		
	if (mb.style.display == "none") {
//		mb.style.display = 'block';
		jq("#mb_"+id).show("fast");
		document.getElementById("mt_"+id).className="menu_title_dis"
	} else {
		jq("#mb_"+id).hide("fast");
		document.getElementById("mt_"+id).className="menu_title"
	}
}
</script>


</body>
</html>