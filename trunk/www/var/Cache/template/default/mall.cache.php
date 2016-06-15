<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\mall.tpl', 'D:\Web\Work\www.090.cn\trunk\admin\application\View\default\mall.tpl', 1453990112)
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
<?php if($action == 'add' ) { ?>    
    <h2>添加聚店商城</h2>
<form name="forms" id="forms" action="<?php echo PHP_SELF; ?>?m=mall.save" method="post"  onSubmit="return chkForm();">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody>
      <tr>
		<td class="td_right_f00" width="150">商城名称：</td>
		<td class="td_left" colspan="2">
			<input type="text" size="100" value="<?php echo $editinfo->mall_name;?>" id="mall_name" name="mall_name">
		</td>
      </tr>

      <tr>
		<td class="td_right_f00" width="150">商城域名：</td>
		<td class="td_left" colspan="2">
			<input type="text" size="100" value="<?php echo $editinfo->mall_domain;?>" id="mall_domain" name="mall_domain"> 不带http://
		</td>
      </tr>
	  
      <tr>
		<td class="td_right_f00" width="150">分销商手机号：</td>
		<td class="td_left" colspan="2">
			<input type="text" size="20" value="<?php echo $editinfo->mobile;?>" id="mobile" name="mobile">
		</td>
      </tr>	  
	  
       <tr>
        <td class="td_right">商城运行状态：</td>
        <td class="td_left">
            <select name="mall_run_status" id="mall_run_status">    			
				<?php echo $mall_run_status_option;?>
		    </select>
        </td>
      </tr>

       <tr>
        <td class="td_right">商城模板：</td>
        <td class="td_left">
            <select name="mall_template" id="mall_template">    			
				<?php echo $mall_template_option;?>
		    </select>
        </td>
      </tr>      
	  
       <tr>
        <td class="td_right">商城评论系统：</td>
        <td class="td_left">
            <select name="mall_comment_status" id="mall_comment_status">    			
				<?php echo $mall_comment_status_option;?>
		    </select>
        </td>
      </tr>

       <tr>
        <td class="td_right">商城类别：</td>
        <td class="td_left">
            <select name="mall_type" id="mall_type">    			
				<?php echo $mall_type_option;?>
		    </select>
        </td>
      </tr>	 

       <tr id="mall_goods_cat_id_div" <?php if($editinfo->mall_type>2) { ?> style="display:none"<?php } ?>>
        <td class="td_right">商城商品分类：</td>
        <td class="td_left">
            <?php if(is_array($goods_category_array)) foreach($goods_category_array AS $goods_category) { ?>
			<input id="cblboxlist_<?php echo $goods_category->goods_cat_id;?>" type="checkbox" name="mall_goods_cat_id[]" value="<?php echo $goods_category->goods_cat_id;?>" 
			<?php if(is_array($mall_goods_cat_id_array)) foreach($mall_goods_cat_id_array AS $mall_goods_cat_id) { ?>
			<?php if($mall_goods_cat_id==$goods_category->goods_cat_id) { ?>checked="checked"<?php } ?>
			<?php } ?>>
			<label for="cblboxlist_<?php echo $goods_category->goods_cat_id;?>"><?php echo $goods_category->cat_name;?></label>
			<?php } ?>
        </td>
      </tr>	 	  
		
		<tr>
			<td class="td_right">网站标题：</td>
			<td class="td_left"><input type="text" value="<?php echo $editinfo->mall_title;?>" id="mall_title" name="mall_title"> title标签中的</td>
		</tr>
		
      <tr>
          <td class="td_right">网站关键字：</td>
          <td class="td_left" colspan="2"><textarea id="mall_keywords" rows="4" style="height:50px" cols="70" name="mall_keywords"><?php echo $editinfo->mall_keywords;?></textarea> keywords标签中的</td>
      </tr> 

      <tr>
          <td class="td_right">网站描述：</td>
          <td class="td_left" colspan="2"><textarea id="mall_description" rows="4" style="height:50px" cols="70" name="mall_description"><?php echo $editinfo->mall_description;?></textarea> description标签中的</td>
      </tr> 	  
	
		
		<tr>
			<td class="td_right">网站ICP信息：</td>
			<td class="td_left"><input type="text" value="<?php echo $editinfo->mall_icp;?>" id="mall_icp" name="mall_icp" size=100></td>
		</tr>		
		
		<tr>
			<td class="td_right">商城金额：</td>
			<td class="td_left"><input type="text" value="<?php echo $editinfo->mall_price;?>" id="mall_price" name="mall_price"></td>
		</tr>	

		
		<tr>
			<td class="td_right">商城代金券金额：</td>
			<td class="td_left"><input type="text" value="<?php echo $editinfo->mall_coupon;?>" id="mall_coupon" name="mall_coupon"></td>
		</tr>			
		
      <tr>
        <td class="td_right">商城LOGO：</td>
        <td class="td_left">
        <span id="thumb_preview"><?php if($editinfo->photo_url != '') { ?><img src="<?php echo $editinfo->photo_url;?>" width="150" height="120" style="margin-bottom:6px"><?php } ?></span>
    <br>
    地址：<input size="80" value="<?php echo $editinfo->photo_url;?>" id="thumb" name="thumb"><br>
    上传：<input type="file" onchange="image_preview('thumb',this.value,1)" style="width: 400px;" id="thumb_upload" name="thumb_upload">
    &nbsp;&nbsp;<input type="button" value="上传" onclick="return ajaxFileUpload('thumb_upload','<?php echo PHP_SELF; ?>?m=tool.uploadImageByAjax&filename=thumb_upload&action=logo','#thumb_loading', 'thumb', 'thumb_preview', 'mall_image_id');" id="thumbupload" name="thumbupload">
    <img style="display:none;" src="<?php echo STATIC_URL; ?>js/loading.gif" id="thumb_loading">
    <input type="hidden" name="mall_image_id" id="mall_image_id" value="<?php echo $editinfo->mall_image_id;?>"/>
    </td>
      </tr>

		

      <tr>
          <td class="td_right">商城统计代码：</td>
          <td class="td_left" colspan="2">
			<textarea id="mall_statistics_code" rows="4" style="height:50px" cols="70" name="mall_statistics_code"><?php echo $editinfo->mall_statistics_code;?></textarea>
		  </td>
      </tr>

      <tr>
          <td class="td_right">商城公告：</td>
          <td class="td_left" colspan="2"><textarea id="mall_placard" rows="4" style="height:50px" cols="70" name="mall_placard"><?php echo $editinfo->mall_placard;?></textarea></td>
      </tr>        
                   
            
		<tr>
			<td class="td_right">业务员名字：</td>
			<td class="td_left"><input type="text" value="<?php echo $editinfo->mall_salesman_name;?>" id="mall_salesman_name" name="mall_salesman_name"></td>
		</tr>
		
		<tr>
			<td class="td_right">佣金调整：</td>
			<td class="td_left">
			<select name="commission_type" id="commission_type">
			<?php echo $commission_type_option;?>
			</select>
			</td>
		</tr>	

		<tbody id="commission_type_div" style="display:none">
		<tr id="cat_id_div" style="display:none">
			<td class="td_right">佣金需要调整的分类：</td>
			<td class="td_left">
			<?php if(is_array($category_array)) foreach($category_array AS $category) { ?>
			<?php if($category->goods_cat_id==$global_cat_id) { continue; } ?>
			<input type="checkbox" name="cat_id[]" value="<?php echo $category->goods_cat_id;?>" <?php if(in_array($category->goods_cat_id,$commission_cat_id_array)) { ?> checked="checked"<?php } ?>/><?php echo $category->cat_name;?>
			<?php } ?>
			</td>
		</tr>			
		
		<tr>
			<td class="td_right">佣金调整方法：</td>
			<td class="td_left">
			<select name="price_class" id="price_class">
			<?php echo $price_class_option;?>
			</select>
			</td>
		</tr>	

		<tr>
			<td class="td_right">佣金调整值：</td>
			<td class="td_left">
			<input type="text" name="price_value" id="price_value" value="<?php echo $member_mall_commission->price_value;?>"/> 固定金额的佣金/总价百分比的佣金
			</td>
		</tr>		
		</tbody>
		
		<tr>
			<td class="td_right">&nbsp;</td>
			<td class="td_left">
			<input type="hidden" name="id" value="<?php echo $editinfo->member_mall_id;?>" />          
			<input name="submit" type="submit" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'" id="submit" value="提交" />
			<input type="reset" name="reset_button" value="清除" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'">
			<input type="button" name="backbutton" id="backbutton" onClick="history.back(1);" value="返回" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'"/></td>
		</tr>
    
    </table>
</form>
<script src="<?php echo STATIC_URL; ?>js/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo STATIC_URL; ?>js/ajaxfileupload.js" type="text/javascript"></script>
<script src="<?php echo STATIC_URL; ?>js/ThumbAjaxFileUpload.js" type="text/javascript"></script>
<script language="javascript">
jq = jQuery.noConflict(); 
//以后jquery中的都用jq代替即可
function chkForm()
{			
	if($('mall_name').value == ""){
	   alert("请填写商城标题！");   
	   $('mall_name').focus();
	   return(false);
   	}

	if(jq('#mall_domain').val() == ""){
	   alert("请填写商城域名！");   
	   $('mall_domain').focus();	   
	   return(false);
   	}
	
	if(jq('#mobile').val() == ""){
	   alert("请填写商城分销商的手机号！");   
	   $('mobile').focus();	   
	   return(false);
   	}
	
	if(jq('#mall_salesman_name').val() == ""){
	   alert("请填写商城业务员名称！");   
	   $('mall_salesman_name').focus();	   
	   return(false);
   	}
	
	if(jq('#mall_image_id').val() == ""){
	   alert("请上传商城LOGO");   
	   $('mall_image_id').focus();	   
	   return(false);
   	}	
	
	var id_array=new Array();
	jq('input[name="mall_goods_cat_id[]"]:checked').each(function(){		
		id_array.push(jq(this).val());//向数组中添加元素
	});
	
	var id_array_length = id_array.length;		
	var mall_type = jq('#mall_type').val();
	if ( mall_type == 1 && id_array_length!=1 ) {		
		alert('单一商城只能选择一个商品分类');
		return false;					
	} else if ( mall_type == 2 && id_array_length<=1 ) {
		alert('组合商城请选择大于一个商品分类');
		return false;	
	}
		
}

jq(document).ready(function(){
  	//商城类别联动选择
	jq('#mall_type').change(function(){
		var mall_type = jq(this).val();		
		if ( mall_type == 1 || mall_type == 2 ) {
			jq('#mall_goods_cat_id_div').show();			
		} else {
			jq('#mall_goods_cat_id_div').hide();
		}
	});
	//商城佣金选择
	var commission_type_value = jq('#commission_type').val();
	change_commission_type_value( commission_type_value );
	
	jq('#commission_type').change(function(){
		var commission_type_value = jq(this).val();
		change_commission_type_value( commission_type_value );
	});
});

function change_commission_type_value ( commission_type_value ) {
	if ( commission_type_value > 0 ) {
		jq('#commission_type_div').show();
	} else {
		jq('#commission_type_div').hide();
	}
	if ( commission_type_value == 2 ) {		
		jq('#cat_id_div').show();
	} else {
		jq('#cat_id_div').hide();
	}
}
</script>
<?php } ?>
    
<?php if($action == 'index' ) { ?>
    <h2>内容列表</h2>
<form method="GET" action="<?php echo PHP_SELF; ?>" onSubmit="return checkSearchForm();">
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
   <tr>
	<td align="center">         　
	关键词：<input type="text" name="query_string" value="<?php echo $query_string;?>">　
	<input type="hidden" name="m" value="mall"/>
	<input type="submit" name="search_btn" value="　搜索　" placeholder="输入手机号或uid或商城名称">	
	</td>	
  </tr>
</table>
</form>
    
<form name="list_form" method="POST" action="<?php echo PHP_SELF; ?>?m=mall.action_do&page=<?php echo $pageCurrent;?>" onSubmit="return checkDelForm();">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody><tr>
      <th>&nbsp;</th>
        <th width="5%">编号</th>        
        <th width="20%">商城名称</th>
        <th width="10%">域名</th>        
        <th width="6%">商城LOGO</th>        
        <th width="8%">手机号</th>        
        <th width="5%">运行状态</th>        
        <th width="8%">业务员</th>        
        <th width="10%">商城类型</th>        
        <th width="10%">商城金额</th>        
        <th width="8%">添加时间</th>        
        <th width="10%">管理</th>
      </tr>
	  <?php if($ErrorMsg) { ?>
	  <tr>
	    <td height=23 colspan=8 class=forumRowHigh align=center><?php echo $ErrorMsg;?></td>
	  </tr>
	  <?php } ?>      
      <?php if(is_array($rs)) foreach($rs AS $k => $v) { ?>
		<tr onmouseout="this.style.background='#fff'" onmouseover="this.style.background='#f6f9fd'" style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">      
			<td><input type="checkbox" value="<?php echo $v->member_mall_id;?>" name="id_a[]"></td>
			<td><?php echo $v->member_mall_id;?></td>
			<td class="td_left"><?php echo $v->mall_name;?></td>
			<td><a href="http://<?php echo $v->mall_domain;?>" target="_blank" title="<?php echo $v->mall_name;?>"><?php echo $v->mall_domain;?></a></td>      
			<td><img src="<?php echo $v->mall_image_id;?>"></td>      
			<td><a href="<?php echo PHP_SELF; ?>?m=member.detail&uid=<?php echo $v->uid;?>"><?php echo $v->mobile;?></a></td>      
			<td><?php echo $v->mall_run_status_text;?></td>   
			<td><?php echo $v->mall_salesman_name;?></td>   
			<td><?php echo $v->mall_type_text;?></td>   
			<td><?php echo $v->mall_price;?></td>   
			<td><?php echo $v->mall_time;?></td>   
			<td><a href="<?php echo PHP_SELF; ?>?m=mall.add&id=<?php echo $v->member_mall_id;?>">修改</a></td>
		</tr>
      <?php } ?>      
		<tr style="background: none repeat scroll 0% 0% rgb(248, 248, 248);">              
			<td class="td_left" colspan="17"></td>
		</tr>
      </tbody></table>
      </form>
      <?php echo $page;?>
<?php } ?>     


	</div>
</div>
</body>
</html>