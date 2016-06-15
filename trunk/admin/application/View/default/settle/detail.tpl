<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="{$BASE_V}layout.css" rel="stylesheet" type="text/css" />
<title>TBlog博客系统</title>
<script type="text/javascript" src="{STATIC_URL}js/tools.js"></script>
</head>
<body>

<div style="z-index: 1; right: 20px; top: 30px; color: rgb(255, 255, 255); position: absolute; display: none;" id="loading"><img src="{$BASE_V}images/loader.gif"></div>

<div id="main">
  <div class="main_box">    
    <h2>提现详细:{$editinfo->uid}</h2>
<form name="forms" id="forms" action="{PHP_SELF}?m=settle.save" method="post" >
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody>
	  
		<tr>
		<td class="td_right_f00" width="150">申请提现用户微信昵称：</td>
		<td class="td_left" colspan="2"><a href="{PHP_SELF}?m=settle.member_bill_list&uid={$editinfo->uid}" title="点击查看用户{$editinfo->realname}的账单详情" target="_blank">{$editinfo->realname}<font color="red">[账户余额：{$current_money}]</font></a></td>
		</tr>


		<tr>
		  <td class="td_right">提现金额</td>
		  <td class="td_left"><font color="red">{$editinfo->money}</a></td>
		</tr>

		<tbody style="display:none">
		<tr>
		  <td class="td_right">用户手机号</td>
		  <td class="td_left">{$editinfo->mobile}</td>
		</tr>
		<tr>
		  <td class="td_right">店铺名称</td>
		  <td class="td_left"><a href="{MOBILE_URL}shop/{$editinfo->uid}" target="_blank">{$editinfo->shop_name}</a></td>
		</tr>	
			  
		<tr>
		  <td class="td_right">提现平台</td>
		  <td class="td_left">{$editinfo->account_type_text}</td>
		</tr>
		</tbody>		
     
		<tr>
          <td class="td_right">申请提现时间</td>
          <td class="td_left">{$editinfo->settle_apply_time}</td>
		</tr>	
		
		<tr>
          <td class="td_right">执行提现打款时间</td>
          <td class="td_left">{$editinfo->settle_execute_time}</td>
		</tr>

		<tr>
          <td class="td_right">提现状态</td>
          <td class="td_left">
		  <select name="settle_status" id="settle_status">
		  {$editinfo->settle_status_option}
		  </select>
		  </td>
		</tr>
		
		
		<tr>
          <td class="td_right">提现备注（给用户看的）</td>
          <td class="td_left">
		    <textarea rows="3" cols="40" id="settle_note" name="settle_note">{$editinfo->settle_note}</textarea>
		  </td>
		</tr>

		<tbody style="display:none">
		<tr>
          <td class="td_right">打款平台</td>
          <td class="td_left">
		  <select name="settle_bank_id" id="settle_bank_id">
		  $editinfo->settle_bank_id_option
		  </select>
		  </td>
		</tr>
		
		<tr>
          <td class="td_right">打款账号</td>
          <td class="td_left"><input type="text" name="settle_bank_cardnum" id="settle_bank_cardnum" value="{$editinfo->settle_bank_cardnum}"></td>
		</tr>
		
		<tr>
          <td class="td_right">打款账户姓名</td>
          <td class="td_left"><input type="text" name="settle_bank_account" id="settle_bank_account" value="{$editinfo->settle_bank_account}"></td>
		</tr>
		

      <tr>
        <td class="td_right">打款截图</td>
        <td class="td_left">
        <span id="thumb_preview">{if $editinfo->settle_image_url != ''}<img src="{$editinfo->settle_image_url}" style="margin-bottom:6px">{/if}</span>
		<br>
		地址：<input size="96" value="{$editinfo->settle_image_url}" id="thumb" name="thumb"><br>
		上传：<input type="file" onchange="image_preview('thumb',this.value,1)" style="width: 400px;" id="thumb_upload" name="thumb_upload">
		&nbsp;&nbsp;<input type="button" value="上传" onclick="return ajaxFileUpload('thumb_upload','{PHP_SELF}?m=tool.uploadImageByAjax&filename=thumb_upload&action=settle&size=800x0','#thumb_loading', 'thumb', 'thumb_preview', 'settle_image_id');" id="thumbupload" name="thumbupload">    
		<img style="display:none;" src="{STATIC_URL}js/loading.gif" id="thumb_loading">
		<input type="hidden" name="settle_image_id" id="settle_image_id" value="{$editinfo->settle_image_id}"/>		
		</td>
      </tr>   
	  </tbody>

		<tr>
          <td class="td_right">操作管理员</td>
          <td class="td_left">{$editinfo->admin_username}</td>
		</tr>		
      
      <tr>
        <td class="td_right">&nbsp;</td>
        <td class="td_left">
		<!--{if empty($error)}-->
          <input type="hidden" name="settle_id" id="settle_id" value="{$editinfo->settle_id}" />          
          <input name="submit" type="submit" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'" id="botton" value="提交" />
          <input type="reset" name="reset_button" value="清除" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'">
          <input type="button" name="backbutton" id="backbutton" onClick="history.back(1);" value="返回" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'"/>
		  <!--{else}-->
		  <font color="red">$error</font>
		  <!--{/if}-->
		  </td>		  
      </tr>
    
    </table>
</form>
<script type="text/javascript" src="{STATIC_URL}js/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="{STATIC_URL}js/ajaxfileupload.js"></script>
<script type="text/javascript" src="{STATIC_URL}js/ThumbAjaxFileUpload.js"></script>
<script language="javascript">
jq = jQuery.noConflict(); 

jq(document).ready(function(){
	jq('#botton').click(function(){
		if ( jq('#settle_status').val() == 0 ) {
			alert('请更新提现状态');
			jq('#settle_status').focus();
			return false;
		}
		/**
		if ( jq('#settle_status').val() == 1 && jq('#settle_bank_cardnum').val() == '' ) {
			alert('请输入打款的账号');
			jq('#settle_bank_cardnum').focus();
			return false;
		}
		if ( jq('#settle_status').val() == 0 && jq('#settle_bank_account').val() == '' ) {
			alert('请输入打款账户姓名');
			jq('#settle_bank_account').focus();
			return false;
		}*/	
	});
	
});  
</script>
	</div>
</div>
</body>
</html>