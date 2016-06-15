<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\settle/index.tpl', 'D:\Web\Work\www.090.cn\trunk\admin\application\View\default\settle\index.tpl', 1453541200)
;?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="<?php echo $BASE_V;?>layout.css" rel="stylesheet" type="text/css" />
<title>TBlog博客系统</title>
<script src="<?php echo STATIC_URL; ?>js/tools.js" type="text/javascript"></script>
<script src="<?php echo $BASE_V;?>article.js" type="text/javascript"></script>
</head>
<body>

<div style="z-index: 1; right: 20px; top: 30px; color: rgb(255, 255, 255); position: absolute; display: none;" id="loading"><img src="<?php echo $BASE_V;?>images/loader.gif"></div>

<div id="main">
  <div class="main_box">    

<h2>内容列表</h2>
<form method="GET" action="<?php echo PHP_SELF; ?>" onSubmit="return checkSearchForm();">
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
   <tr>   
	<td align="center">
    提现状态 : 
    <select name="status" id="status">	
	<option value=0>全部</option>
    <?php echo $settle_status_option;?>
    </select>
		　
	关键词：<input type="text" name="query_string" value="<?php echo $query_string;?>" placeholder='输入：用户id/手机号/用户名 搜索' size=30>　
	<input type="hidden" name="m" value="settle.index"/>
	<input type="submit" name="search_btn" value="　搜索　">	
	</td>	
  </tr>
</table>
</form>
    
<form name="list_form" method="POST" action="<?php echo PHP_SELF; ?>?m=settle.index&page=<?php echo $pageCurrent;?>" onSubmit="return checkDelForm();">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody><tr>
      <th>&nbsp;</th>
        <th width="5%">编号</th>
        <th width="5%">用户姓名</th>
        <th width="5%">用户手机</th>
        <th width="8%">店铺名称</th>
        <th width="5%">账户类型</th>
        <th width="20%">账号内容</th>
        <th width="10%">提现备注</th>
        <th width="5%">提现金额</th>        
        <th width="8%">申请时间</th>        
		<th width="8%">打款时间</th>                
        <th width="5%">操作人姓名</th>        
        <th width="10%">状态</th>        
        <th width="10%">管理</th>
      </tr>
	  <?php if($ErrorMsg) { ?>
	  <tr>
	    <td height=23 colspan=8 class=forumRowHigh align=center><?php echo $ErrorMsg;?></td>
	  </tr>
	  <?php } ?>      
      <?php if(is_array($rs)) foreach($rs AS $k => $v) { ?>
      <tr onmouseout="this.style.background='#fff'" onmouseover="this.style.background='#f6f9fd'" style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">
      
      <td><input type="checkbox" value="<?php echo $v->settle_id;?>" name="id_a[]"></td>
      <td><?php echo $v->settle_id;?></td>
      <td class="td_left"><a href="<?php echo PHP_SELF; ?>?m=settle.member_bill_list&uid=<?php echo $v->uid;?>"><?php echo $v->realname;?></a></td>      
      <td><?php echo $v->mobile;?></td> 
      <td class="td_left"><a href="<?php echo MOBILE_URL; ?>shop/<?php echo $v->uid;?>" target="_blank"><?php echo $v->shop_name;?></a></td>
      <td><?php echo $v->account_type_text;?></td> 
      <td class="td_left">
	  <?php if($v->account_type==2) { ?>
	  支付宝账号：<?php echo $v->alipay_account;?>
	  <?php } else { ?>
	  <?php echo $v->bank_name;?>:<?php echo $v->bank_cardnum;?>
	  <?php } ?>
	  </td> 
      <td><?php echo $v->settle_note;?></td> 
      <td>￥：<?php echo $v->money;?></td> 
      <td><?php echo $v->settle_apply_time;?></td> 
      <td><?php echo $v->settle_execute_time;?></td> 
      <td><?php echo $v->admin_username;?></td>       
      <td><?php echo $v->settle_status;?></td>       
      <td><a href="<?php echo PHP_SELF; ?>?m=settle.detail&id=<?php echo $v->settle_id;?>">详细/操作</a></td>
      </tr>
      <?php } ?>      
      <tr style="background: none repeat scroll 0% 0% rgb(248, 248, 248);">
              <td><input type="checkbox" name="select_all_btn" onclick="select_fx();"></td>
              <td>
			  <select name="do" id='do'><?php echo $article_do_ary_option;?></select></td>              
              <td class="td_left" colspan="12">
            <input type="submit" onclick="return confirm('确定要执行这次操作吗？会同步删除所有分销商的该商品哟')" class="btn02" value="确定" name="Submit">
      </td></tr>
      </tbody></table>
      </form>
      <?php echo $page;?>
	</div>
</div>
</body>
</html>
<script src="<?php echo STATIC_URL; ?>js/jquery/1.11.2/jquery-1.11.2.min.js" type="text/javascript"></script>
<script>
jq = jQuery.noConflict(); 
jq(document).ready(function() {

});
</script>