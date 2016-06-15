<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\supplier.tpl', 'D:\Web\Work\www.090.cn\trunk\admin\application\View\default\supplier.tpl', 1445241465)
;?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="<?php echo $BASE_V;?>layout.css" rel="stylesheet" type="text/css" />
<title>TBlog博客系统</title>
<script src="<?php echo STATIC_URL; ?>js/tools.js" type="text/javascript"></script>
</head>
<body>

<div style="z-index: 1; right: 20px; top: 30px; color: rgb(255, 255, 255); position: absolute; display: none;" id="loading"><img src="<?php echo $BASE_V;?>images/loader.gif"></div>

<div id="main">
<div class="main_box">	
<?php if($action == 'index' ) { ?>
<link rel="stylesheet" href="<?php echo STATIC_URL; ?>js/ui.datepicker.css"/>
<h2>供应商申请入驻列表</h2>
<form id="forms" method="GET" action="<?php echo PHP_SELF; ?>" onSubmit="return checkSearchForm();">
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
   <tr>
	<td align="center">		
	导出类型：
	<select name="member_export" id="member_export">		
		<option value=0>-导出选项-</option>
		<?php echo $member_export_option;?>
	</select>
	
	<input type="hidden" name="m" value="supplier"/>	
	<input type="hidden" name="page" value="<?php echo $pages;?>"/>
	<input type="submit" name="search_btn" value="　搜索　">	
	</td>	
  </tr>
</table>
</form>
    
<form name="list_form" method="POST" action="<?php echo PHP_SELF; ?>?m=supplier.batch_do&page=<?php echo $pageCurrent;?>" onSubmit="return checkDelForm();">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody><tr>
      <th>&nbsp;</th>
        <th width="5%">编号</th>
        <th width="25%">公司名称</th>
        <th width="10%">行业类别</th>
        <th width="10%">联系人</th>
        <th width="10%">联系手机号</th>        
        <th width="20%">地址</th>        
        <th width="10%">IP</th>        
        <th width="10%">时间</th>                
      </tr>
	  <?php if($ErrorMsg) { ?>
	  <tr>
	    <td height=23 colspan=8 class=forumRowHigh align=center><?php echo $ErrorMsg;?></td>
	  </tr>
	  <?php } ?>      
      <?php if(is_array($rs)) foreach($rs AS $k => $v) { ?>
      <tr onmouseout="this.style.background='#fff'" onmouseover="this.style.background='#f6f9fd'" style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">
      
      <td><input type="checkbox" value="<?php echo $v->supplier_id;?>" name="id_a[]"></td>
      <td><?php echo $v->supplier_id;?></td>
	  <td class="td_left"><?php echo $v->company_name;?></td>
      <td><?php echo $v->industry_category;?></td>
      <td><?php echo $v->company_contacts;?></td>
      <td><?php echo $v->mobile;?></td>
      <td class="td_left"><?php echo $v->full_address;?></td> 
      <td><?php echo $v->ip;?></td> 
      <td><?php echo $v->supplier_time;?></td>      
      </tr>
      <?php } ?>      
      <tr style="background: none repeat scroll 0% 0% rgb(248, 248, 248);">
              <td><input type="checkbox" name="select_all_btn" onclick="select_fx();"></td>
              <!--<td><select name="do" id='do'><?php echo $article_do_ary_option;?></select></td>-->
              <td class="td_left" colspan="12">
            <input type="submit" onclick="return confirm('确定要执行这次操作吗？')" class="btn02" value="确定" name="Submit">
      </td></tr>
      </tbody></table>
      </form>
      <?php echo $page;?>
<?php } ?>     


	</div>
</div>
</body>
</html>
<script src="<?php echo STATIC_URL; ?>js/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
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


jq = jQuery.noConflict(); 
jq(document).ready(function(){

});
</script>