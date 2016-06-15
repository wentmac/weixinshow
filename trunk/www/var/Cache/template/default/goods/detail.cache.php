<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\goods/detail.tpl', 'D:\Web\Work\www.090.cn\trunk\admin\application\View\default\goods\detail.tpl', 1453402180)
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
    <h2>修改商品</h2>
<form name="forms" id="forms" action="<?php echo PHP_SELF; ?>?m=goods/index.modify" method="post"  onSubmit="return chkForm();">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody>
      <tr>
        <td class="td_right_f00" width="150">商品标题：</td>
        <td class="td_left" colspan="2"><?php echo $editinfo->goods_name;?>
	  </td>
      </tr>
       <tr>
        <td class="td_right">商品分类：</td>
        <td class="td_left">
		<ul>		
		<?php if(is_array($goods_cat_id_array)) foreach($goods_cat_id_array AS $goods_cat_id) { ?>
		<li>
		<?php echo $category_array[$goods_cat_id];?> 
		</li>
		<?php } ?>
         </ul>   
        </td>
      </tr>

      <tr>
          <td class="td_right">价格</td>
          <td class="td_left"><?php echo $editinfo->goods_price;?>
		  	|
			<?php if(is_array($member_agent_array)) foreach($member_agent_array AS $member_agent_id => $member_agent_name) { ?>
				<?php if($member_agent_id>0) { ?>			
					<?php echo $member_agent_name;?>:<input type="text" name="member_agent[]" size="5" value="<?php echo $goods_agent->goods_agent[$member_agent_id]['price'];?>"/>
				<?php } ?>
			<?php } ?>
		  </td>
      </tr>
      <tr>
          <td class="td_right">库存</td>
          <td class="td_left"><?php echo $editinfo->goods_stock;?></td>
      </tr>	
      <tr>
          <td class="td_right">销量</td>
          <td class="td_left"><?php echo $editinfo->sales_volume;?></td>
      </tr>

      
      <tr>
          <td class="td_right">发布时间</td>
          <td class="td_left" colspan="2"><?php echo $editinfo->goods_time;?></td>
      </tr>	  
      <tr>
          <td class="td_right">评论总数</td>
          <td class="td_left" colspan="2"><?php echo $editinfo->comment_count;?></td>
      </tr>	
	  <tr>
          <td class="td_right">运费</td>
          <td class="td_left" colspan="2"><?php echo $editinfo->shipping_fee;?></td>
      </tr>	
	  
	  <tr>
          <td class="td_right">佣金</td>
          <td class="td_left" colspan="2"><?php echo $editinfo->commission_fee;?></td>
      </tr>	
	  
	<tr>
          <td class="td_right">商品上架云端商品库</td>
          <td class="td_left" colspan="2"><?php echo $editinfo->is_supplier;?></td>
      </tr>	

	<tr>
          <td class="td_right">商品来源</td>
          <td class="td_left" colspan="2"><?php echo $editinfo->goods_source;?></td>
      </tr>		  

	<tr>
          <td class="td_right">商品图片</td>
          <td class="td_left" colspan="2">
		  <?php if(is_array($goods_image_array)) foreach($goods_image_array AS $image_url) { ?>
		  <img src="<?php echo $image_url;?>">
		  <?php } ?>
		  </td>
      </tr>		  

	  <tr>
          <td class="td_right">商品sku</td>
          <td class="td_left" colspan="2">
		  <ul>			
		  <?php if(is_array($goods_sku_array)) foreach($goods_sku_array AS $goods_sku) { ?>
			<li>
			{
			<?php if(is_array($goods_sku->goods_sku_json)) foreach($goods_sku->goods_sku_json AS $goods_sku_info) { ?>
			<?php echo $goods_sku_info['spec_value_name'];?> |
			<?php } ?>
			}
			: 库存（<?php echo $goods_sku->stock;?>） 价格（<?php echo $goods_sku->price;?>）|
			<?php if(is_array($member_agent_array)) foreach($member_agent_array AS $member_agent_id => $member_agent_name) { ?>
				<?php if($member_agent_id>0) { ?>			
					<?php echo $member_agent_name;?>:<input type="text" name="member_agent_<?php echo $goods_sku->goods_sku_id;?>[]"  size="5" <?php if(isset($goods_agent->goods_agent[$member_agent_id]['sku_array'][$goods_sku->goods_sku_id]['price'])) { ?>value="<?php echo $goods_agent->goods_agent[$member_agent_id]['sku_array'][$goods_sku->goods_sku_id]['price'];?><?php } else { ?>value=""<?php } ?> "/>
				<?php } ?>
			<?php } ?>
			</li>
		  <?php } ?>
		  </ul>
		  </td>
      </tr>		  


	<tr>
          <td class="td_right">内容</td>
          <td class="td_left" colspan="2"><?php echo $editinfo->goods_desc;?></td>
      </tr>			  
	
	<tr>
          <td class="td_right">分销商佣金设置</td>
          <td class="td_left" colspan="2">
		  <select name="commission_seller_different" id="commission_seller_different">
		  <?php echo $commission_seller_different_option;?>
		  </select>
		  </td>
      </tr>			  
	  
	<tr <?php if(empty($editinfo->commission_seller_different)) { ?>style="display:none"<?php } ?> id="commission_different_object">
          <td class="td_right">分销商佣金不同级别设置</td>
          <td class="td_left" colspan="2">
			<ul>
				<li>免费分销商：<input type="text" size="3" name="commission_seller_free" id="commission_seller_free" value="<?php echo $editinfo->commission_different_object['commission_seller_free'];?>">%</li>
				<li>vip分销商：<input type="text" size="3" name="commission_seller_vip" id="commission_seller_vip" value="<?php echo $editinfo->commission_different_object['commission_seller_vip'];?>">%</li>
				<li>svip分销商：<input type="text" size="3" name="commission_seller_svip" id="commission_seller_svip" value="<?php echo $editinfo->commission_different_object['commission_seller_svip'];?>">%</li>				
			</ul>
		  </td>
      </tr>			  	  
	
      
	<tr>
          <td class="td_right">商品编辑文案</td>
          <td class="td_left" colspan="2">		  
		  <textarea rows="10" cols="100" id="goods_brief" name="goods_brief"><?php echo $editinfo->goods_brief;?></textarea>
		  </td>
      </tr>	
	  
	<tr>
          <td class="td_right">商品排序</td>
          <td class="td_left" colspan="2">
		  <input type="text" value="<?php echo $editinfo->goods_sort;?>" name="goods_sort" id="goods_sort"/> 越大越靠前
		  </td>
      </tr>	
	  
	<tr>
          <td class="td_right">商品品牌</td>
          <td class="td_left" colspan="2">
		  <select name="brand_id" id="brand_id">
		  <option value="0">-请选择品牌-</option>
		  <?php echo $brand_option;?>
		  </select>
		  </td>
      </tr>		  
	  
      <tr>
        <td class="td_right">&nbsp;</td>
        <td class="td_left">
		
          <input type="hidden" name="goods_id" id="goods_id" value="<?php echo $editinfo->goods_id;?>" />          
          <input name="submit" type="submit" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'" id="submit" value="提交" />
          <input type="reset" name="reset_button" value="清除" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'">
          <input type="button" name="backbutton" id="backbutton" onClick="history.back(1);" value="返回" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'"/></td>		  
      </tr>
    
    </table>
</form>
<script src="<?php echo STATIC_URL; ?>js/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script language="javascript">
jq = jQuery.noConflict(); 


jq(document).ready(function(){
	jq.each( jq('img'), function(i, n) {	
		if ( jq(n).attr('data-lazyload') != '' ) {
			var src = jq(n).attr('data-lazyload');	
			//console.log(src);
			jq(n).attr('src',src);
		}		
	});	
	
	jq('#commission_seller_different').change(function(){
		var commission_seller_different = jq(this).val();
		if ( commission_seller_different == 1 ) {
			jq('#commission_different_object').show();
		} else {
			jq('#commission_different_object').hide();
		}
	});
	
});  
</script>
	</div>
</div>
</body>
</html>