<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\keywords.tpl', 'D:\Web\Site\tblog\trunk\admin\application\View\default\keywords.tpl', 1404242547)
;?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="<?php echo $BASE_V;?>layout.css" rel="stylesheet" type="text/css" />
<title>TBlog博客系统</title>
<script src="<?php echo $BASE;?>js/tools.js" type="text/javascript"></script>
<script src="<?php echo $BASE_V;?>keywords.js" type="text/javascript"></script>
</head>
<body>

<div style="z-index: 1; right: 20px; top: 30px; color: rgb(255, 255, 255); position: absolute; display: none;" id="loading"><img src="<?php echo $BASE_V;?>images/loader.gif"></div>

<div id="main">
  <div class="main_box">    
<?php if($action == 'add' ) { ?>    
<h2>修改页面关键字</h2>
<form name="modform" id="forms" action="<?php echo PHP_SELF; ?>?m=keywords.save" method="post"  onSubmit="return chkForm();">
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
       <tbody><tr>
        <td width="100" class="td_right">页面名：</td>
        <td class="td_left"><input type="text" size="40" value="<?php echo $editinfo['k_pagename']";?> id="pagename" name="pagename">
         </td>
      </tr>
      <tr>
        <td width="100" class="td_right">文件名：</td>
        <td class="td_left"><?php if($editinfo['k_id'] > 0) { echo $editinfo['k_page'];?><?php } else { ?><input type="text" size="40" value="<?php echo $editinfo['k_page']";?> id="page" name="page"><?php } ?>
         </td>
      </tr>
      <tr>
        <td class="td_right">页面标题：</td>
        <td class="td_left"><textarea id="title" rows="5" cols="60" name="title"><?php echo $editinfo['k_title'];?></textarea></td>
      </tr>
  <tr>
        <td class="td_right">页面关键字：</td>
        <td class="td_left"><textarea id="keywords" rows="5" cols="60" name="keywords"><?php echo $editinfo['k_keywords'];?></textarea></td>
      </tr>
  <tr>
        <td class="td_right">页面描述：</td>
        <td class="td_left"><textarea id="description" rows="5" cols="60" name="description"><?php echo $editinfo['k_description'];?></textarea></td>
      </tr>
  <tr>
        <td class="td_right">规则：</td>
        <td class="td_left"><?php if($editinfo['k_id'] > 0) { echo $editinfo['k_rule'];?><?php } else { ?><input type="text" size="78" value="<?php echo $editinfo['k_rule']";?> id="rule" name="rule"><?php } ?>
         </td>        
      </tr>
      <tr>
        <td class="td_right">&nbsp;</td>
        <td class="td_left">
        <input type="hidden" value="<?php echo $editinfo['k_id']";?> name="k_id">
        <input type="submit" value="提交" id="submit" onmouseout="this.className='btn05'" onmouseover="this.className='btn06'" class="btn05" name="submit">
        <input type="reset" class="btn05" value="清除" name="reset_button" onmouseout="this.className='btn05'" onmouseover="this.className='btn06'">
        <input type="button" value="返回" onclick="history.back(1);" class="btn05" id="backbutton" name="backbutton" onmouseout="this.className='btn05'" onmouseover="this.className='btn06'">
        </td>
      </tr>
    
    </tbody>
</table> 
</form>
<?php } ?>
    
<?php if($action == 'index' ) { ?>
    <h2>页面关键字管理</h2>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody>
      <tr>
        <th width="5%">编号</th>
        <th width="32%">页面名称</th>
        <th width="38%">页面地址</th>
        <th width="18%">修改日期</th>
        <th width="10%">管理</th>
      </tr>
	  <?php if($ErrorMsg) { ?>
	  <tr>
	    <td height=23 colspan=8 class=forumRowHigh align=center><?php echo $ErrorMsg;?></td>
	  </tr>
	  <?php } ?>      
      <?php if(is_array($rs)) foreach($rs AS $k => $v) { ?>
      <tr onmouseout="this.style.background='#fff'" onmouseover="this.style.background='#f6f9fd'" style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">
      
      <td><?php echo $v['k_id'];?></td>
      <td class="td_left"><?php echo $v['k_pagename'];?></td>
      <td class="td_left"><a href="?ClassId=3&amp;Pages=0"><?php echo $v['k_page'];?></a></td>
      <td><?php echo $v['time'];?></td>      
      <td><a href="<?php echo PHP_SELF; ?>?m=keywords.add&amp;k_id=<?php echo $v['k_id']">管理;?></a></td>
      </tr>
      <?php } ?>      
      
      </tbody></table>
      <?php echo $page;?>
<?php } ?>     
	</div>
</div>
</body>
</html>