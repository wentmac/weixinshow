<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\goods/index.tpl', 'D:\Web\Work\www.090.cn\trunk\admin\application\View\default\goods\index.tpl', 1453397324)
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
排序 : 
    <select name="sort" id="sort">	
    <?php echo $sort_option;?>
    </select>	   
	
    商品分类 : 
    <select name="goods_cat_id" id="goods_cat_id">
	<option value="0">|-全部</option>
    <?php echo $category_tree_list;?>
    </select>
	
	<input type="checkbox" name="just_this_goods_cat_id" value="1"<?php if($just_this_goods_cat_id) { ?> id="just_this_goods_cat_id" checked="true"<?php } ?>><label for="just_this_goods_cat_id">本级分类下的商品<label>
		
云端商品库 : 
    <select name="is_supplier" id="is_supplier">
	<option value="0">|-全部</option>
    <?php echo $is_supplier_option;?>
    </select>	
	
商品来源 : 
    <select name="goods_source" id="goods_source">
	<option value="0">|-全部</option>
    <?php echo $goods_source_option;?>
    </select>	
	
商品来源ID：<input type="text" name="goods_source_id" value="<?php echo $goods_source_id;?>">　    
	
商品品牌 : 
    <select name="brand_id" id="brand_id">
	<option value="0">|-全部</option>
    <?php echo $brand_option;?>
    </select>	

	供应商ID：<input type="text" name="uid" value="<?php echo $uid;?>">　    
	　
	关键词：<input type="text" name="query_string" value="<?php echo $query_string;?>">　
	每页商品数量：<input type="text" size=6 name="pagesize" value="<?php echo $pagesize;?>">
	<input type="hidden" name="m" value="goods/index"/>
	<input type="submit" name="search_btn" value="　搜索　">	
	</td>	
  </tr>
</table>
</form>
    
<form name="list_form" method="POST" action="<?php echo PHP_SELF; ?>?m=goods/index.action_do&page=<?php echo $pageCurrent;?>" onSubmit="return checkDelForm();">
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
        <th width="10%">商品来源</th>        
		<th width="6%">云端商品库</th>                
		<th width="4%">商品排序</th>                
        <th width="10%">时间</th>        
        <th width="17%">管理</th>
      </tr>
	  <?php if($ErrorMsg) { ?>
	  <tr>
	    <td height=23 colspan=8 class=forumRowHigh align=center><?php echo $ErrorMsg;?></td>
	  </tr>
	  <?php } ?>      
      <?php if(is_array($rs)) foreach($rs AS $k => $v) { ?>
      <tr onmouseout="this.style.background='#fff'" onmouseover="this.style.background='#f6f9fd'" style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">
      
      <td><input type="checkbox" value="<?php echo $v->goods_id;?>" name="id_a[]"></td>
      <td><?php echo $v->goods_id;?></td>
      <td class="td_left"><a href="<?php echo MOBILE_URL; ?>market/goods_detail?id=<?php echo $v->goods_id;?>"><?php echo $v->goods_name;?></a></td>      
      <td><img src="<?php echo $v->goods_image_id;?>"></td> 
      <td>
		<div class="cate" style="line-height: 22px;">
			<?php if(is_array($v->goods_cat_id)) foreach($v->goods_cat_id AS $goods_cat_id) { ?>
			<div class="cate-box" title="<?php echo $goods_category_array[$goods_cat_id];?>"><?php echo $goods_category_array[$goods_cat_id];?></div>
			<?php } ?>
		</div>	  	  
      <td><?php echo $v->goods_price;?></td> 
      <td><?php echo $v->commission_fee;?></td> 
      <td><?php echo $v->goods_stock;?></td> 
      <td><?php echo $v->sales_volume;?></td> 
      <td><?php echo $v->goods_source;?>|<?php echo $v->goods_source_id;?></td>
      <td><?php echo $v->is_supplier;?></td>
      <td><?php echo $v->goods_sort;?></td>
      <td><?php echo $v->goods_time;?></td>       
      <td><a href="<?php echo INDEX_URL; ?>manage.php?m=supplier/goods.add&id=<?php echo $v->goods_id;?>&other_uid=<?php echo $v->uid;?>" target="_blank" title="商品在<?php echo $v->uid;?>会员中心中修改">会员中心修改</a> | <a href="<?php echo PHP_SELF; ?>?m=goods/index.detail&id=<?php echo $v->goods_id;?>">修改</a> | <a href="<?php echo PHP_SELF; ?>?m=goods/index.action_do&action=del&id=<?php echo $v->goods_id;?>" onclick="{if(confirm('删除将包括该信息，确定删除吗?')){return true;}return false;}">删除</a></td>
      </tr>
      <?php } ?>      
      <tr style="background: none repeat scroll 0% 0% rgb(248, 248, 248);">
              <td><input type="checkbox" name="select_all_btn" onclick="select_fx();"></td>
              <td>
				<select name="do" id='do'><?php echo $article_do_ary_option;?></select>
				<div style="display:none" id="market_shop_div">
				<select name="market_shop" id='market_shop'><?php echo $market_shop_option;?></select>
				</div>
				<div style="display:none" id="market_goods_brand_div">
				<select name="brand_id" id='brand_id_div'><?php echo $brand_batch_option;?></select>
				</div>
			  </td>
              <td colspan="8">
			  <select name="batch_goods_cat_id" id='batch_goods_cat_id'>
			  <option value="0">=批量把商品加入分类=</value>
			  <?php echo $category_tree_batch_list;?>
			  </select>
			  <input type="radio" name="category_type" value="add" checked="true">商品分类叠加
			  <input type="radio" name="category_type" value="modify">商品分类重置为一个分类
			  <input type="radio" name="category_type" value="del">从分类中删除
			  </td>              
              <td class="td_left" colspan="18">
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