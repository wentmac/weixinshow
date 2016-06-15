<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\user.tpl', 'D:\Web\Work\www.090.cn\trunk\admin\application\View\default\user.tpl', 1453541202)
;?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="<?php echo $BASE_V;?>layout.css" rel="stylesheet" type="text/css" />
<title>TBlog博客系统</title>
<script src="<?php echo $BASE;?>js/tools.js" type="text/javascript"></script>
<script src="<?php echo $BASE_V;?>user.js" type="text/javascript"></script>
</head>
<body>

<div style="z-index: 1; right: 20px; top: 30px; color: rgb(255, 255, 255); position: absolute; display: none;" id="loading"><img src="<?php echo $BASE_V;?>images/loader.gif"></div>

<div id="main">
  <div class="main_box">    
<?php if($action == 'add' ) { ?>    
<h2>添加管理员</h2>
<form name="modform" id="forms" action="<?php echo PHP_SELF; ?>?m=user.save" method="post"  onSubmit="return chkForm();">    
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody><tr>
        <td width="100" class="td_right">登录名：</td>
        <td class="td_left"><input type="text" value="<?php echo $editinfo->username;?>" id="username" name="username"/>
         </td>
      </tr>
      <tr>
        <td class="td_right">真实姓名：</td>
        <td class="td_left"><input type="text" value="<?php echo $editinfo->nicename;?>" id="name" name="nicename"/></td>
      </tr>
      <tr>
        <td class="td_right">密码：</td>
        <td class="td_left"><input type="password" value="" id="password" name="password" style="width:149px"/><?php if($editinfo->uid>0) { ?>&nbsp;&nbsp;不修改就留空<?php } ?>
          </td>
      </tr>
      <tr>
        <td class="td_right">权限：</td>
        <td class="td_left"><select id="rank" name="rank"><?php echo $admin_type_option;?></select></td>
      </tr>
      <tr>
        <td class="td_right">email：</td>
        <td class="td_left"><input type="text" value="<?php echo $editinfo->email;?>" id="email" name="email"/></td>
      </tr>      
      <tr>
        <td class="td_right">&nbsp;</td>
        <td class="td_left">
          <input type="hidden" value="<?php echo $editinfo->uid;?>" name="uid" id='uid'/>
          <input type="submit" value="提交" id="submit" onmouseout="this.className='btn05'" onmouseover="this.className='btn06'" class="btn05" name="submit"/>
          <input type="reset" onmouseout="this.className='btn05'" onmouseover="this.className='btn06'" class="btn05" value="清除" name="reset_button2"/>
          <input type="button" value="返回" onclick="history.back(1);" onmouseout="this.className='btn05'" onmouseover="this.className='btn06'" class="btn05" id="backbutton2" name="backbutton2"/></td>
      </tr>
    
    </tbody>
</table>
</form>    
<?php } ?>
    
<?php if($action == 'index' ) { ?>
<h2>友情链接管理</h2>
<form name="list_form" method="POST" action="<?php echo PHP_SELF; ?>?m=user.user_do&action=del&page=<?php echo $pageCurrent;?>" onSubmit="return checkDelForm();">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody><tr>
      <th>&nbsp;</th>
        <th width="6%">编号</th>
        <th width="26%">用户名</th>
        <th width="26%">真实姓名</th>
        <th width="12%">级别</th>
        <th width="18%">注册时间</th>        
        <th width="10%">管理</th>
      </tr>
	  <?php if($ErrorMsg) { ?>
	  <tr>
	    <td height=23 colspan=7 class=forumRowHigh align=center><?php echo $ErrorMsg;?></td>
	  </tr>
	  <?php } ?>      
      <?php if(is_array($rs)) foreach($rs AS $k => $v) { ?>
      <tr onmouseout="this.style.background='#fff'" onmouseover="this.style.background='#f6f9fd'" style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">
      
      <td><input type="checkbox" value="<?php echo $v->uid;?>" name="id_a[]"></td>
      <td><?php echo $v->uid;?></td>
      <td class="td_left"><a href="<?php echo PHP_SELF; ?>?m=user.add&uid=<?php echo $v->uid;?>"><?php echo $v->username;?></a></td>
      <td class="td_left"><?php echo $v->nicename;?></td> 
      <td><span style="color: rgb(255, 102, 0);"><?php echo $v->typename;?></span></td> 
      <td><?php echo $v->time;?></td> 
      <td><a href="<?php echo PHP_SELF; ?>?m=user.add&uid=<?php echo $v->uid;?>">修改</a><?php if($v->uid!=1) { ?>| <a href="<?php echo PHP_SELF; ?>?m=user.user_do&action=del&uid=<?php echo $v->uid;?>">删除</a><?php } ?></td>
      </tr>
      <?php } ?>      
      <tr style="background: none repeat scroll 0% 0% rgb(248, 248, 248);">
              <td><input type="checkbox" name="select_all_btn" onclick="select_fx();"></td>
              <td class="td_left" colspan="8">
            <input type="submit" onclick="return confirm('确定要执行这次操作吗？')" class="btn02" value="删除所选" name="Submit">
            <input type="hidden" value="del" name="action">
      </td></tr>
      </tbody></table>
      </form>
      <?php echo $page;?>
<?php } ?>
	</div>
</div>
</body>
</html>