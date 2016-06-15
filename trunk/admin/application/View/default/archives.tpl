<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="{$BASE_V}layout.css" rel="stylesheet" type="text/css" />
<title>TBlog博客系统</title>
<script type="text/javascript" src="{$BASE}js/tools.js"></script>
<script type="text/javascript" src="{$BASE_V}article.js"></script>
</head>
<body>

<div style="z-index: 1; right: 20px; top: 30px; color: rgb(255, 255, 255); position: absolute; display: none;" id="loading"><img src="{$BASE_V}images/loader.gif"></div>

<div id="main">
  <div class="main_box">    
<!--{if $action == 'add' }-->    
    <h2>内容发布向导</h2>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody>
      <tr>
        <td class="td_left" width="7%">频道ID</td>
        <td class="td_left" width="30%">频道内容模型</td>
        <td class="td_left" width="50%">操作选项</td>        
      </tr>
      <!--{loop $channeltype $k $v}-->
       <tr>
        <td class="td_left">$k</td>
        <td class="td_left">$v</td>
        <td class="td_left"><a href="{PHP_SELF}?m=category">管理栏目</a> | <a href="{PHP_SELF}?m=archives.arclist&channelid=$k">管理内容</a> | <a href="{PHP_SELF}?m=archives.catgoto&channelid=$k">发布内容</a></td>        
      </tr>
      <!--{/loop}-->
    </table>
<!--{/if}-->   
	</div>
</div>
</body>
</html>