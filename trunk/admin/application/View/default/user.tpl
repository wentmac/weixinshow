<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="{$BASE_V}layout.css" rel="stylesheet" type="text/css" />
<title>TBlog博客系统</title>
<script type="text/javascript" src="{$BASE}js/tools.js"></script>
<script type="text/javascript" src="{$BASE_V}user.js"></script>
</head>
<body>

<div style="z-index: 1; right: 20px; top: 30px; color: rgb(255, 255, 255); position: absolute; display: none;" id="loading"><img src="{$BASE_V}images/loader.gif"></div>

<div id="main">
  <div class="main_box">    
<!--{if $action == 'add' }-->    
<h2>添加管理员</h2>
<form name="modform" id="forms" action="{PHP_SELF}?m=user.save" method="post"  onSubmit="return chkForm();">    
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody><tr>
        <td width="100" class="td_right">登录名：</td>
        <td class="td_left"><input type="text" value="{$editinfo->username}" id="username" name="username"/>
         </td>
      </tr>
      <tr>
        <td class="td_right">真实姓名：</td>
        <td class="td_left"><input type="text" value="{$editinfo->nicename}" id="name" name="nicename"/></td>
      </tr>
      <tr>
        <td class="td_right">密码：</td>
        <td class="td_left"><input type="password" value="" id="password" name="password" style="width:149px"/>{if $editinfo->uid>0}&nbsp;&nbsp;不修改就留空{/if}
          </td>
      </tr>
      <tr>
        <td class="td_right">权限：</td>
        <td class="td_left"><select id="rank" name="rank">$admin_type_option</select></td>
      </tr>
      <tr>
        <td class="td_right">email：</td>
        <td class="td_left"><input type="text" value="{$editinfo->email}" id="email" name="email"/></td>
      </tr>      
      <tr>
        <td class="td_right">&nbsp;</td>
        <td class="td_left">
          <input type="hidden" value="{$editinfo->uid}" name="uid" id='uid'/>
          <input type="submit" value="提交" id="submit" onmouseout="this.className='btn05'" onmouseover="this.className='btn06'" class="btn05" name="submit"/>
          <input type="reset" onmouseout="this.className='btn05'" onmouseover="this.className='btn06'" class="btn05" value="清除" name="reset_button2"/>
          <input type="button" value="返回" onclick="history.back(1);" onmouseout="this.className='btn05'" onmouseover="this.className='btn06'" class="btn05" id="backbutton2" name="backbutton2"/></td>
      </tr>
    
    </tbody>
</table>
</form>    
<!--{/if}-->
    
<!--{if $action == 'index' }-->
<h2>友情链接管理</h2>
<form name="list_form" method="POST" action="{PHP_SELF}?m=user.user_do&action=del&page={$pageCurrent}" onSubmit="return checkDelForm();">
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
	  <!--{if $ErrorMsg}-->
	  <tr>
	    <td height=23 colspan=7 class=forumRowHigh align=center>$ErrorMsg</td>
	  </tr>
	  <!--{/if}-->      
      <!--{loop $rs $k $v}-->
      <tr onmouseout="this.style.background='#fff'" onmouseover="this.style.background='#f6f9fd'" style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">
      
      <td><input type="checkbox" value="{$v->uid}" name="id_a[]"></td>
      <td>$v->uid</td>
      <td class="td_left"><a href="{PHP_SELF}?m=user.add&uid={$v->uid}">$v->username</a></td>
      <td class="td_left">$v->nicename</td> 
      <td><span style="color: rgb(255, 102, 0);">$v->typename</span></td> 
      <td>$v->time</td> 
      <td><a href="{PHP_SELF}?m=user.add&uid={$v->uid}">修改</a>{if $v->uid!=1}| <a href="{PHP_SELF}?m=user.user_do&action=del&uid={$v->uid}">删除</a>{/if}</td>
      </tr>
      <!--{/loop}-->      
      <tr style="background: none repeat scroll 0% 0% rgb(248, 248, 248);">
              <td><input type="checkbox" name="select_all_btn" onclick="select_fx();"></td>
              <td class="td_left" colspan="8">
            <input type="submit" onclick="return confirm('确定要执行这次操作吗？')" class="btn02" value="删除所选" name="Submit">
            <input type="hidden" value="del" name="action">
      </td></tr>
      </tbody></table>
      </form>
      {$page}
<!--{/if}-->
	</div>
</div>
</body>
</html>