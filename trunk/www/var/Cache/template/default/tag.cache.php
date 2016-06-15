<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\tag.tpl', 'D:\Web\Site\tblog\trunk\admin\application\View\default\tag.tpl', 1404152143)
;?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="<?php echo $BASE_V;?>layout.css" rel="stylesheet" type="text/css" />
<title>TBlog博客系统</title>
<script src="<?php echo $BASE;?>js/tools.js" type="text/javascript"></script>
</head>
<body>

<div style="z-index: 1; right: 20px; top: 30px; color: rgb(255, 255, 255); position: absolute; display: none;" id="loading"><img src="<?php echo $BASE_V;?>images/loader.gif"></div>

<div id="main">
  <div class="main_box">    
<?php if($action == 'add' ) { ?>    
    <h2>添加Tags</h2>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
	<form name="forms" id="forms" action="<?php echo PHP_SELF; ?>?m=tag.save" method="post"  onSubmit="return chkForm();">
      <tbody>
      <tr>
        <td class="td_right_f00" width="150">名称：</td>
        <td class="td_left" colspan="2"><input type="text" size="100" value="<?php echo $editinfo->tag_name;?>" id="tag_name" name="tag_name">
	  </td>
      </tr>
      
      
      <tr>
        <td class="td_right">&nbsp;</td>
        <td class="td_left">
          <input type="hidden" name="tag_id" value="<?php echo $editinfo->tag_id;?>" />
          <input name="submit" type="submit" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'" id="submit" value="提交" />
          <input type="reset" name="reset_button" value="清除" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'">
          <input type="button" name="backbutton" id="backbutton" onClick="history.back(1);" value="返回" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'"/></td>
      </tr>
    
    </table>
    </form>
<script language="javascript">
function chkForm()
{
	if($('tag_name').value == ""){
	   alert("请填写标题！");   
	   $('tag_name').focus();
	   return(false);
   	}
}
</script>
<?php } ?>
    
<?php if($action == 'index' ) { ?>
<h2>Tags列表</h2>
<form method="GET" action="<?php echo PHP_SELF; ?>" onSubmit="return checkSearchForm();">
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
   <tr>
	<td align="center">         	　
	关键词：<input type="text" name="search_keyword" value="<?php echo $search_keyword;?>">　
    <input type="hidden" name="m" value="tag"/>
	<input type="submit" name="search_btn" value="　搜索　">
	</td>
  </tr>
</table>
</form>
    
<form name="list_form" method="POST" action="<?php echo PHP_SELF; ?>?m=tag.tag_do&page=<?php echo $pageCurrent;?>" onSubmit="return checkDelForm();">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody><tr>
      <th>&nbsp;</th>
        <th width="10%">编号</th>
        <th width="50%">Tags名称</th>        
        <th width="40%">管理</th>
      </tr>
	  <?php if($ErrorMsg) { ?>
	  <tr>
	    <td height=23 colspan=4 class=forumRowHigh align=center><?php echo $ErrorMsg;?></td>
	  </tr>
	  <?php } ?>      
      <?php if(is_array($rs)) foreach($rs AS $k => $v) { ?>
      <tr onmouseout="this.style.background='#fff'" onmouseover="this.style.background='#f6f9fd'" style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">
      
      <td><input type="checkbox" value="<?php echo $v->tag_id;?>" name="id_a[]"></td>
      <td><?php echo $v->tag_id;?></td>
      <td><a href="<?php echo PHP_SELF; ?>?m=tag.add&tagid=<?php echo $v->tag_id;?>"><?php echo $v->tag_name;?></a></td>
      <td><a href="<?php echo PHP_SELF; ?>?m=tag.add&tagid=<?php echo $v->tag_id;?>">修改</a> | <a href="<?php echo PHP_SELF; ?>?m=tag.tag_do&action=del&tagid=<?php echo $v->tag_id;?>" onclick="{if(confirm('删除将包括该信息，确定删除吗?')){return true;}return false;}">删除</a></td>
      </tr>
      <?php } ?>      
      <tr style="background: none repeat scroll 0% 0% rgb(248, 248, 248);">
              <td><input type="checkbox" name="select_all_btn" onclick="select_fx();"></td>
              <td><select name="do" id='do'><?php echo $tag_do_ary_option;?></select></td>              
              <td class="td_left" colspan="4">                            
            <input type="submit" onclick="return confirm('确定要执行这次操作吗？')" class="btn02" value="确定" name="Submit">
      </td></tr>
      </tbody></table>
      </form>
      <?php echo $page;?>
      
<script language="javascript">
function checkDelForm(){
	var check = GetCheckboxValue('id_a[]');
	var article_do = $('do').value;

	if( article_do == '0' )
	{
		alert("好像您没有选择任何管理操作吧?:-(");	
		document.getElementById('do').focus();
		return false;		
	}
	if( check == '')
	{
		alert("好像您没有选择任何要操作的评论吧?:-(");	
		return false;
	}
}
</script>      
<?php } ?>     


	</div>
</div>
</body>
</html>