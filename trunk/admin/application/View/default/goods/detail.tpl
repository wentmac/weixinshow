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
    <h2>修改商品</h2>
<form name="forms" id="forms" action="{PHP_SELF}?m=goods/index.modify" method="post"  onSubmit="return chkForm();">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody>
      <tr>
        <td class="td_right_f00" width="150">商品标题：</td>
        <td class="td_left" colspan="2">{$editinfo->goods_name}
	  </td>
      </tr>
       <tr>
        <td class="td_right">商品分类：</td>
        <td class="td_left">
		<ul>		
		<!--{loop $goods_cat_id_array $goods_cat_id}-->
		<li>
		{$category_array[$goods_cat_id]} 
		</li>
		<!--{/loop}-->
         </ul>   
        </td>
      </tr>

      <tr>
          <td class="td_right">价格</td>
          <td class="td_left">{$editinfo->goods_price}</td>
      </tr>
      <tr>
          <td class="td_right">库存</td>
          <td class="td_left">{$editinfo->goods_stock}</td>
      </tr>	
      <tr>
          <td class="td_right">销量</td>
          <td class="td_left">{$editinfo->sales_volume}</td>
      </tr>

      
      <tr>
          <td class="td_right">发布时间</td>
          <td class="td_left" colspan="2">{$editinfo->goods_time}</td>
      </tr>	  
      <tr>
          <td class="td_right">评论总数</td>
          <td class="td_left" colspan="2">{$editinfo->comment_count}</td>
      </tr>	
	  <tr>
          <td class="td_right">运费</td>
          <td class="td_left" colspan="2">{$editinfo->shipping_fee}</td>
      </tr>	
	  
	  <tr>
          <td class="td_right">佣金</td>
          <td class="td_left" colspan="2">{$editinfo->commission_fee}</td>
      </tr>	  

	<tr>
          <td class="td_right">商品图片</td>
          <td class="td_left" colspan="2">
		  {loop $goods_image_array $image_url}
		  <img src="{$image_url}">
		  {/loop}
		  </td>
      </tr>		  

	  <tr>
          <td class="td_right">商品sku</td>
          <td class="td_left" colspan="2">
		  <ul>			
		  {loop $goods_sku_array $goods_sku}
			<li>
			{
			{loop $goods_sku->goods_sku_json $goods_sku_info}
			{$goods_sku_info[spec_value_name]} |
			{/loop}
			}
			: 库存（{$goods_sku->stock}） 价格（{$goods_sku->price}）
			</li>
		  {/loop}
		  </ul>
		  </td>
      </tr>		  


	<tr>
          <td class="td_right">内容</td>
          <td class="td_left" colspan="2">{$editinfo->goods_desc}</td>
      </tr>			  
      
	<tr>
          <td class="td_right">商品编辑文案</td>
          <td class="td_left" colspan="2">		  
		  <textarea rows="10" cols="100" id="goods_brief" name="goods_brief">{$editinfo->goods_brief}</textarea>
		  </td>
      </tr>	
	  
	<tr>
          <td class="td_right">商品排序</td>
          <td class="td_left" colspan="2">
		  <input type="text" value="{$editinfo->goods_sort}" name="goods_sort" id="goods_sort"/> 越大越靠前
		  </td>
      </tr>	
	  
	<tr>
          <td class="td_right">商品品牌</td>
          <td class="td_left" colspan="2">
		  <select name="brand_id" id="brand_id">
		  <option value="0">-请选择品牌-</option>
		  {$brand_option}
		  </select>
		  </td>
      </tr>		  
	  
      <tr>
        <td class="td_right">&nbsp;</td>
        <td class="td_left">
		
          <input type="hidden" name="goods_id" id="goods_id" value="{$editinfo->goods_id}" />          
          <input name="submit" type="submit" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'" id="submit" value="提交" />
          <input type="reset" name="reset_button" value="清除" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'">
          <input type="button" name="backbutton" id="backbutton" onClick="history.back(1);" value="返回" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'"/></td>		  
      </tr>
    
    </table>
</form>
<script type="text/javascript" src="{STATIC_URL}js/jquery/1.7.2/jquery.min.js"></script>
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
});  
</script>
	</div>
</div>
</body>
</html>