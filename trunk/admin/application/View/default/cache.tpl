<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="{$BASE_V}layout.css" rel="stylesheet" type="text/css" />
<title>TBlog博客系统</title>
<script type="text/javascript" src="{$BASE}js/tools.js"></script>
</head>
<body>

<div style="z-index: 1; left: 20px; top: 30px; color: rgb(255, 255, 255); position: absolute; display: none;" id="loading"><img src="{$BASE_V}images/loader.gif"></div>

<div id="main">
  <div class="main_box">
    <h2>缓存清理</h2>
    <div style="padding:10px;">
    <!--{loop $cache_array $k $v}-->
    <!--{loop $v $kk $vv}-->
<li>
<div class="indiv">[<b style="color:#F00">{$vv[dir_allname]}</b>]{$kk}: {$vv[dir_name]}{$vv[dir_dir]}<b>文件总数:{$vv[dir_count]}&nbsp;&nbsp;文件总大小: {$vv[dir_size]}MB</b></div>
<!--{if $vv[dir_count]>0}-->
<div class="outdiv"><input type="button" style="margin-left:25px;" value="清理" class="btn02" onClick="{if $vv[dir_size]>10}if(confirm('确实要删除吗?缓存文件比较多，需要一段时间！')){/if}changval('${echo addslashes($vv[dir_name].$vv[dir_dir])}');"></div>
<!--{/if}-->
</li>
	<!--{/loop}-->
	<!--{/loop}-->    
<span style="background:#666;color:#fff">缓存文件总数：<b>{$filesize_count}个</b>，缓存文件所占空间大小<b>{$filesize_size}MB</b></span></div>
<li style="padding-left:10px; color:#F00; font-weight:bold; font-family:'微软雅黑'; font-size:16px">缓存的存在减少了数据库的查询连接负担和对住哪联盟API的请求次数, 将会及大的提高网站打开速度, 所以建议大家除非网站的虚拟硬盘空间不足或想更新前台的数据调用为最新请不要没事就清理它!</li>
</div></div>

<style>
li{ list-style:none; height:40px;}
.indiv{ width:690px; float:left}
.outdiv{ float:left; top:-3px; position:relative}
</style>
<script>
function changval(val)
{
	$('loading').style.display='block';
	window.location="{PHP_SELF}?m=cache.del&&folder="+val;
}
</script>
</body>
</html>