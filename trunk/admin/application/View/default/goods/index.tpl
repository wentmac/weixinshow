<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="{$BASE_V}layout.css" rel="stylesheet" type="text/css" />
<title>TBlog博客系统</title>
<script type="text/javascript" src="{STATIC_URL}js/tools.js"></script>
<script type="text/javascript" src="{$BASE_V}article.js"></script>
</head>
<body>

<div style="z-index: 1; right: 20px; top: 30px; color: rgb(255, 255, 255); position: absolute; display: none;" id="loading"><img src="{$BASE_V}images/loader.gif"></div>

<div id="main">
  <div class="main_box">    

<h2>内容列表</h2>
<form method="GET" action="{PHP_SELF}" onSubmit="return checkSearchForm();">
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
   <tr>   
	<td align="center">
排序 : 
    <select name="sort" id="sort">	
    $sort_option
    </select>	   
	
    商品分类 : 
    <select name="goods_cat_id" id="goods_cat_id">
	<option value="0">|-全部</option>
    $category_tree_list
    </select>
	
	<input type="checkbox" name="just_this_goods_cat_id" value="1"{if $just_this_goods_cat_id} id="just_this_goods_cat_id" checked="true"{/if}><label for="just_this_goods_cat_id">本级分类下的商品<label>
		
商品品牌 : 
    <select name="brand_id" id="brand_id">
	<option value="0">|-全部</option>
    $brand_option
    </select>	

	供应商ID：<input type="text" name="uid" value="{$uid}">　    
	　
	关键词：<input type="text" name="query_string" value="{$query_string}">　
	每页商品数量：<input type="text" size=6 name="pagesize" value="{$pagesize}">
	<input type="hidden" name="m" value="goods/index"/>
	<input type="submit" name="search_btn" value="　搜索　">	
	</td>	
  </tr>
</table>
</form>
    
<form name="list_form" method="POST" action="{PHP_SELF}?m=goods/index.action_do&page={$pageCurrent}" onSubmit="return checkDelForm();">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody><tr>
      <th>&nbsp;</th>
        <th width="5%">编号</th>
        <th width="16%">商品名称</th>
        <th width="9%">图片</th>
        <th width="8%">分类</th>
        <th width="5%">价格</th>
        <th width="5%">佣金</th>
        <th width="5%">库存</th>
        <th width="5%">销量</th>        		
		<th width="4%">商品排序</th>                
        <th width="10%">时间</th>        
        <th width="17%">管理</th>
      </tr>
	  <!--{if $ErrorMsg}-->
	  <tr>
	    <td height=23 colspan=8 class=forumRowHigh align=center>$ErrorMsg</td>
	  </tr>
	  <!--{/if}-->      
      <!--{loop $rs $k $v}-->
      <tr onmouseout="this.style.background='#fff'" onmouseover="this.style.background='#f6f9fd'" style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">
      
      <td><input type="checkbox" value="{$v->goods_id}" name="id_a[]"></td>
      <td>$v->goods_id</td>
      <td class="td_left"><a href="{MOBILE_URL}market/goods_detail?id={$v->goods_id}">$v->goods_name</a></td>      
      <td><img src="{$v->goods_image_id}"></td> 
      <td>
		<div class="cate" style="line-height: 22px;">
			<!--{loop $v->goods_cat_id $goods_cat_id}-->
			${$cat_name = empty($goods_category_array[$goods_cat_id]) ? '' : $goods_category_array[$goods_cat_id];}
			<div class="cate-box" title="{$cat_name}">{$cat_name}</div>
			<!--{/loop}-->
		</div>	  	  
      <td>$v->goods_price</td> 
      <td>$v->commission_fee</td> 
      <td>$v->goods_stock</td> 
      <td>$v->sales_volume</td>       
      <td>$v->goods_sort</td>
      <td>$v->goods_time</td>       
      <td><a href="{INDEX_URL}manage.php?m=goods.add&id={$v->goods_id}&other_uid={$v->uid}" target="_blank" title="商品在{$v->uid}会员中心中修改">会员中心修改</a> | <a href="{PHP_SELF}?m=goods/index.detail&id={$v->goods_id}">修改</a> | <a href="{PHP_SELF}?m=goods/index.action_do&action=del&id={$v->goods_id}" onclick="{if(confirm('删除将包括该信息，确定删除吗?')){return true;}return false;}">删除</a></td>
      </tr>
      <!--{/loop}-->      
      <tr style="background: none repeat scroll 0% 0% rgb(248, 248, 248);">
              <td><input type="checkbox" name="select_all_btn" onclick="select_fx();"></td>
              <td>
				<select name="do" id='do'>$article_do_ary_option</select>
				<div style="display:none" id="market_shop_div">
				<select name="market_shop" id='market_shop'>$market_shop_option</select>
				</div>
				<div style="display:none" id="market_goods_brand_div">
				<select name="brand_id" id='brand_id_div'>$brand_batch_option</select>
				</div>
			  </td>              
              <td class="td_left" colspan="18">
            <input type="submit" onclick="return confirm('确定要执行这次操作吗？会同步删除所有分销商的该商品哟')" class="btn02" value="确定" name="Submit">
      </td></tr>
      </tbody></table>
      </form>
      {$page}
	</div>
</div>
</body>
</html>
<script src="{STATIC_URL}js/jquery/1.11.2/jquery-1.11.2.min.js" type="text/javascript"></script>
<script>
jq = jQuery.noConflict(); 
jq(document).ready(function() {
	jq('#do').change(function(){
		var do_string = jq(this).val();
		
		if ( do_string == 'market_goods_create' ) {
			jq('#market_shop_div').show();
		} else {
			jq('#market_shop_div').hide();			
		}
		
		if ( do_string == 'market_goods_brand' ) {
			jq('#market_goods_brand_div').show();
		} else {
			jq('#market_goods_brand_div').hide();			
		}
	});
});
function checkDelForm(){
	var batch_value = jq('#do').val();
	var batch_goods_cat_id = jq('#batch_goods_cat_id').val();
	
	var id_a = jq("input[name='id_a[]']:checked").val();  

	if ( id_a == undefined ) {
		alert('亲，没有选择任何商品哟');
		return false;			
	}
	if ( batch_value == 0 && batch_goods_cat_id == 0 ) {
		alert('请选择要批量操作类型哟');
		return false;	
	}
	return true;
}
</script>