<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="{$BASE_V}layout.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="{STATIC_URL}js/jquery/1.7.2/jquery.min.js"></script>
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
<title>$admin_title</title>
</head>
<body>

<div id="header">
  <div id="logo"><img src="{$BASE_V}images/logo.gif" /></div>
  <div id="header_r">欢迎您{$username}登录 | <a href="{PHP_SELF}?m=index.body" target="main">管理主页</a> | <a href="http://www.t-mac.org" target="main">技术支持</a> | <a href="{PHP_SELF}?m=cache" target="main">清理硬盘缓存</a> | <a href="{$indexurl}" target="_blank">网站主页</a> | <a href="{PHP_SELF}?m=login.out" target="_top">退出登录</a></div>
</div>

<div class="menu" id="menu_box">
    <div id="menu">
	<!--{loop $menua $k $sec1}-->
    <h4 class="menu_title" id="mt_$k" onclick="do_menu($k)">{$sec1[title]}</h4>
    <ul id="mb_$k" class="menu_body" style="display:none">
      <!--{loop $sec1[subname] $kk $sec2}-->
      <li>{$sec2}</li>
      <!--{/loop}-->
    </ul>
    <!--{/loop}-->        
    </div>
</div>

<div class="main" id="body_box">
    <iframe id="main" name="main" frameborder="0" src="{PHP_SELF}?m=index.body"></iframe>
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
	document.getElementById("menu_box").style.background = 'url({$BASE_V}images/left_bg.gif) right repeat-y';	
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