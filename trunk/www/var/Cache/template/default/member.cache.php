<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\member.tpl', 'D:\Web\Work\www.090.cn\trunk\admin\application\View\default\member.tpl', 1453397315)
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
<?php if($action == 'detail' ) { ?>    
    <h2>会员详情</h2>
<form name="forms" id="forms" action="<?php echo PHP_SELF; ?>?m=member.save" method="post"  onSubmit="return chkForm();">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody>
      <tr>
        <td class="td_right_f00" width="150">用户uid：</td>
        <td class="td_left" colspan="2"><?php echo $memberInfo->uid;?></td>
      </tr>
	  <tr>
        <td class="td_right_f00" width="150">用户username：</td>
        <td class="td_left" colspan="2"><input type="text" name="username" id="username" value="<?php echo $memberInfo->username;?>"/></td>
      </tr>
	  <tr>
        <td class="td_right_f00" width="150">用户真实姓名：</td>
        <td class="td_left" colspan="2"><input type="text" name="realname" id="realname" value="<?php echo $memberInfo->realname;?>"/></td>
      </tr>
	  <tr>
        <td class="td_right_f00" width="150">用户头像：</td>
        <td class="td_left" colspan="2"><img src="<?php echo $memberInfo->member_image_id;?>"></td>
      </tr>
	  <tr>
        <td class="td_right_f00" width="150">用户注册时间：</td>
        <td class="td_left" colspan="2"><?php echo $memberInfo->reg_time;?></td>
      </tr>
	  <tr>
        <td class="td_right_f00" width="150">用户上次登录时间：</td>
        <td class="td_left" colspan="2"><?php echo $memberInfo->last_login_time;?></td>
      </tr>
	  <tr>
        <td class="td_right_f00" width="150">用户上次登录IP：</td>
        <td class="td_left" colspan="2"><?php echo $memberInfo->last_login_ip;?></td>
      </tr>
	  <tr>
        <td class="td_right_f00" width="150">用户支付宝账户：</td>
        <td class="td_left" colspan="2"><?php echo $memberSettingInfo->alipay_account;?></td>
      </tr>
	  <tr>
        <td class="td_right_f00" width="150">用户支付宝姓名：</td>
        <td class="td_left" colspan="2"><?php echo $memberSettingInfo->alipay_username;?></td>
      </tr>
	  <tr>
        <td class="td_right_f00" width="150">用户开户银行：</td>
        <td class="td_left" colspan="2"><?php echo $memberSettingInfo->bank_id;?></td>
      </tr>
	  <tr>
        <td class="td_right_f00" width="150">用户银行卡号：</td>
        <td class="td_left" colspan="2"><?php echo $memberSettingInfo->bank_cardnum;?></td>
      </tr>
	  <tr>
        <td class="td_right_f00" width="150">用户银行姓名：</td>
        <td class="td_left" colspan="2"><?php echo $memberSettingInfo->bank_account;?></td>
      </tr>
	  <tr>
        <td class="td_right_f00" width="150">店铺名称：</td>
        <td class="td_left" colspan="2"><a href="<?php echo MOBILE_URL; ?>shop/<?php echo $memberInfo->uid;?>" target="_blank"><?php echo $memberSettingInfo->shop_name;?></a></td>
      </tr>
	  <tr>
        <td class="td_right_f00" width="150">店铺简介：</td>
        <td class="td_left" colspan="2"><?php echo $memberSettingInfo->shop_intro;?>"</td>
      </tr>
	  <tr>
        <td class="td_right_f00" width="150">微店实体店地址：</td>
        <td class="td_left" colspan="2"><?php echo $memberSettingInfo->shop_address;?></td>
      </tr>	  
	  <tr>
        <td class="td_right_f00" width="150">店铺头像：</td>
        <td class="td_left" colspan="2"><img src="<?php echo $memberSettingInfo->shop_image_id;?>"></td>
      </tr>	
	  <tr>
        <td class="td_right_f00" width="150">店铺招牌背景图：</td>
        <td class="td_left" colspan="2"><img src="<?php echo $memberSettingInfo->shop_signboard_image_id;?>"></td>
      </tr>		  
	  <tr>
        <td class="td_right_f00" width="150">店铺库存设置：</td>
        <td class="td_left" colspan="2"><?php echo $memberSettingInfo->stock_setting_text;?></td>
      </tr>		
	  <tr>
        <td class="td_right_f00" width="150">微信号：</td>
        <td class="td_left" colspan="2"><?php echo $memberSettingInfo->weixin_id;?></td>
      </tr>	
	  <tr>
        <td class="td_right_f00" width="150">当前可用余额：</td>
        <td class="td_left" colspan="2"><?php echo $memberSettingInfo->current_money;?></td>
      </tr>	
	  <tr>
        <td class="td_right_f00" width="150">历史收入：</td>
        <td class="td_left" colspan="2"><?php echo $memberSettingInfo->history_money;?></td>
      </tr>
	  <tr>
        <td class="td_right_f00" width="150">店铺被收藏总量：</td>
        <td class="td_left" colspan="2"><?php echo $memberSettingInfo->collect_count;?></td>
      </tr>	  
	  <tr>
        <td class="td_right_f00" width="150">身份证号：</td>
        <td class="td_left" colspan="2"><?php echo $memberSettingInfo->idcard;?>"</td>
      </tr>		  
	  <tr>
        <td class="td_right_f00" width="150">身份证图片正面：</td>
        <td class="td_left" colspan="2"><img src="<?php echo $memberSettingInfo->idcard_positive_image_id;?>"></td>
      </tr>		  
	  <tr>
        <td class="td_right_f00" width="150">身份证图片反面：</td>
        <td class="td_left" colspan="2"><img src="<?php echo $memberSettingInfo->idcard_negative_image_id;?>"></td>
      </tr>		  
	  <tr>
        <td class="td_right_f00" width="150">手持有身份证的照片：</td>
        <td class="td_left" colspan="2"><img src="<?php echo $memberSettingInfo->idcard_image_id;?>"></td>
      </tr>		  
       <tr>
        <td class="td_right">身份证审核状态：</td>
        <td class="td_left">
            <select name="idcard_verify" id="idcard_verify">    			
				<?php echo $memberSettingInfo->idcard_verify_option;?>
		    </select>
        </td>
      </tr>

       <tr>
        <td class="td_right">用户类型：</td>
        <td class="td_left">
            <select name="member_type" id="member_type">    			
				<?php echo $memberInfo->member_type_option;?>
		    </select>
        </td>
      </tr>

       <tr>
        <td class="td_right">用户级别：</td>
        <td class="td_left">
            <select name="member_class" id="member_class">    			
				<?php echo $memberInfo->member_class_option;?>
		    </select>
        </td>
      </tr>	 
	  
       <tr <?php if($memberInfo->member_type==1) { ?>style="display:none"<?php } ?> id="security_deposit_tr">
        <td class="td_right">供应商保证金：</td>
        <td class="td_left">
            <input type="text" id="security_deposit" name="security_deposit" value="<?php echo $memberSettingInfo->security_deposit;?>"/>
        </td>
      </tr>
	  
       <tr>
        <td class="td_right">账户锁定：</td>
        <td class="td_left">
            <select name="locked_type" id="locked_type">    			
				<?php echo $memberInfo->locked_type_option;?>
		    </select>
        </td>
      </tr>
	  
       <tr>
        <td class="td_right">直接收款和自营收入手续费开关：</td>
        <td class="td_left">
            <select name="fee_type" id="fee_type">    			
				<?php echo $memberInfo->fee_type_option;?>
		    </select>
        </td>
      </tr>	  
	  
       <tr>
        <td class="td_right">分销商佣金促销推广开关：</td>
        <td class="td_left">
            <select name="promotion_type" id="promotion_type">    			
				<?php echo $memberInfo->promotion_type_option;?>
		    </select>
        </td>
      </tr>	  	  

       <tr>
        <td class="td_right">供应商商品有多少人分销：</td>
        <td class="td_left">
            <input type="text" id="seller_count_variable" name="seller_count_variable" value="<?php echo $memberSettingInfo->seller_count_variable;?>"/>
        </td>
      </tr>

       <tr>
        <td class="td_right">店铺收藏人数：</td>
        <td class="td_left">
            <input type="text" id="collect_count_variable" name="collect_count_variable" value="<?php echo $memberSettingInfo->collect_count_variable;?>"/>
        </td>
      </tr>	  
	  
       <tr>
        <td class="td_right">店铺排序：</td>
        <td class="td_left">
            <input type="text" id="shop_sort" name="shop_sort" value="<?php echo $memberSettingInfo->shop_sort;?>"/>越大热门店铺越靠前
        </td>
      </tr>	  	  

      <tr>
        <td class="td_right">&nbsp;</td>
        <td class="td_left">
          <input type="hidden" name="uid" value="<?php echo $memberInfo->uid;?>" />          
          <input name="submit" type="submit" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'" id="submit" value="提交" />
          <input type="reset" name="reset_button" value="清除" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'">
          <input type="button" name="backbutton" id="backbutton" onClick="history.back(1);" value="返回" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'"/></td>
      </tr>
    
    </table>
</form>
<script src="<?php echo STATIC_URL; ?>js/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script language="javascript">
var member_class_json = <?php echo $member_class_json;?>;
jq = jQuery.noConflict(); 
//以后jquery中的都用jq代替即可。
function chkForm()
{
	/**
	if($('title').value == ""){
	   alert("请填写标题！");   
	   $('title').focus();
	   return(false);
   	}**/
		
}

jq(document).ready(function(){
	jq('#member_type').change(function(){
		var member_class = jq(this).val();
		var member_class_array = member_class_json[member_class];
				
		jq("#member_class").empty();
		jq.each( member_class_array, function(i, n){
			if ( n == '' ) {
				jq("#member_class").append("<option value='0'>-请选择-</option>");   //为Select追加一个Option(下拉项)
			} else {
				jq("#member_class").append("<option value='"+i+"'>"+n+"</option>");   //为Select追加一个Option(下拉项)
			}
		});

		if ( member_class == 2 ) {
			jq('#security_deposit_tr').show();
		} else {
			jq('#security_deposit_tr').hide();
		}
	});	  	
});
</script>
<?php } ?>
    
<?php if($action == 'add' ) { ?>    
    <h2>会员详情</h2>
<form name="forms" id="forms" action="<?php echo PHP_SELF; ?>?m=member.create_save" method="post"  onSubmit="return chkForm();">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody>
      <tr>
        <td class="td_right_f00" width="150">用户手机号：</td>
        <td class="td_left" colspan="2"><input type="text" value="" name="mobile" id="mobile"/></td>
      </tr>
	  
	  <tr>
        <td class="td_right_f00" width="150">用户密码：</td>
        <td class="td_left" colspan="2"><input type="text" value="" name="pwd" id="pwd"/></td>
      </tr>
	  
	  <tr>
        <td class="td_right_f00" width="150">用户呢称：</td>
        <td class="td_left" colspan="2"><input type="text" value="" name="username" id="username"/></td>
      </tr>
	  
	  <tr>
        <td class="td_right_f00" width="150">推荐人手机号：</td>
        <td class="td_left" colspan="2"><input type="text" value="" name="agent_mobile" id="agent_mobile"/></td>
      </tr>


      <tr>
        <td class="td_right">&nbsp;</td>
        <td class="td_left">          
          <input name="submit" type="submit" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'" id="submit" value="提交" />
          <input type="reset" name="reset_button" value="清除" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'">
          <input type="button" name="backbutton" id="backbutton" onClick="history.back(1);" value="返回" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'"/></td>
      </tr>
    
    </table>
</form>
<script src="<?php echo STATIC_URL; ?>js/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script language="javascript">
jq = jQuery.noConflict(); 
//以后jquery中的都用jq代替即可。
function chkForm()
{	
	if($('mobile').value == ""){
	   alert("请填写手机号！");   
	   $('mobile').focus();
	   return(false);
   	}
	if($('pwd').value == ""){
	   alert("请填写密码！");   
	   $('pwd').focus();
	   return(false);
   	}
		
}

jq(document).ready(function(){
	
});
</script>
<?php } ?>
	
<?php if($action == 'index' ) { ?>
<link rel="stylesheet" href="<?php echo STATIC_URL; ?>js/ui.datepicker.css"/>
<h2>会员列表</h2>
<form id="forms" method="GET" action="<?php echo PHP_SELF; ?>" onSubmit="return checkSearchForm();">
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
   <tr>
	<td align="center">
    用户类型：
	<select name="member_type" id="member_type">
		<option value="0">-选择-</option>
		<?php echo $member_type_option;?>
	</select>
	
	用户级别：
	<select name="member_class" id="member_class">		
		<?php echo $member_class_option;?>
	</select>
         	　
	关键词：<input type="text" name="query_string" value="<?php echo $query_string;?>" placeholder="请输入用户UID或手机号">　
	
		注册开始日期：<input type="text" id="start_date" name="start_date" value="<?php echo $start_date;?>" size=5 readonly/>
        &nbsp;&nbsp;&nbsp;注册结束日期：
        <input type="text" id="end_date" name="end_date" value="<?php echo $end_date;?>" size=5 readonly/>	
		
	导出类型：
	<select name="member_export" id="member_export">		
		<option value=0>-导出选项-</option>
		<?php echo $member_export_option;?>
	</select>
	
	<input type="hidden" name="m" value="member"/>
	<input type="hidden" name="page" value="<?php echo $pages;?>"/>
	<input type="submit" name="search_btn" value="　搜索　">	
	</td>	
  </tr>
</table>
</form>
    
<form name="list_form" method="POST" action="<?php echo PHP_SELF; ?>?m=member.batch_do&page=<?php echo $pageCurrent;?>" onSubmit="return checkDelForm();">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody><tr>
      <th>&nbsp;</th>
        <th width="5%">编号</th>
        <th width="10%">手机号</th>
        <th width="10%">用户头像</th>
        <th width="10%">注册时间</th>
        <th width="10%">上次时间</th>        
        <th width="10%">用户类型</th>        
        <th width="10%">用户级别</th>        
        <th width="5%">当前余额</th>        
        <th width="5%">历史余额</th>        
        <th width="15%">店铺名称</th>  
        <th width="10%">管理</th>
      </tr>
	  <?php if($ErrorMsg) { ?>
	  <tr>
	    <td height=23 colspan=8 class=forumRowHigh align=center><?php echo $ErrorMsg;?></td>
	  </tr>
	  <?php } ?>      
      <?php if(is_array($rs)) foreach($rs AS $k => $v) { ?>
      <tr onmouseout="this.style.background='#fff'" onmouseover="this.style.background='#f6f9fd'" style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">
      
      <td><input type="checkbox" value="<?php echo $v->uid;?>" name="id_a[]"></td>
      <td><a href="<?php echo INDEX_URL; ?>manage.php?m=bill.home&other_uid=<?php echo $v->uid;?>" target="_blank" title="点击进入用户<?php echo $v->uid;?>的管理后台"><?php echo $v->uid;?></a></td>
      <td><?php echo $v->mobile;?></td>
      <td><img src="<?php echo $v->member_image_id;?>"></td>
      <td><?php echo $v->reg_time;?></td>
      <td><?php echo $v->last_login_time;?></td> 
      <td><?php echo $v->member_type_text;?></td> 
      <td><?php echo $v->member_class_text;?></td> 
      <td><?php echo $v->current_money;?></td>      
      <td><?php echo $v->history_money;?></td>      
      <td><a href="<?php echo MOBILE_URL; ?>shop/<?php echo $v->uid;?>" target="_blank"><?php echo $v->shop_name;?></a></td>      
      <td><a href="<?php echo PHP_SELF; ?>?m=member.detail&uid=<?php echo $v->uid;?>">详细</a></td>
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
<script src="<?php echo STATIC_URL; ?>js/jq.date.js" type="text/javascript"></script>
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

var member_class_json = <?php echo $member_class_json;?>;
jq = jQuery.noConflict(); 
jq(document).ready(function(){
	jq('#member_type').change(function(){
		var member_class = jq(this).val();
		var member_class_array = member_class_json[member_class];
		
		jq("#member_class").empty();
		jq.each( member_class_array, function(i, n){			
			if ( n == -1 ) {
				jq("#member_class").append("<option value='"+i+"'>"+n+"</option>");   //为Select追加一个Option(下拉项)
			} else {
				jq("#member_class").append("<option value='"+i+"' selected='selected'>"+n+"</option>");   //为Select追加一个Option(下拉项)
			}
		});

	});	
});

//以后jquery中的都用jq代替即可。 
jq(document).ready(function() {
	jq('#forms input#start_date').datepicker({ dateFormat: 'yy-mm-dd', showOn: 'button', buttonImage: '<?php echo STATIC_URL; ?>js/calendar.gif', buttonImageOnly: true });
});

jq(document).ready(function() {
	jq('#forms input#end_date').datepicker({ dateFormat: 'yy-mm-dd', showOn: 'button', buttonImage: '<?php echo STATIC_URL; ?>js/calendar.gif', buttonImageOnly: true });
});
</script>