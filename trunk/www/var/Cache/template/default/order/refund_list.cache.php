<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\order/refund_list.tpl', 'D:\Web\Work\www.090.cn\trunk\admin\application\View\default\order\refund_list.tpl', 1444390378)
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

<h2>维权订单</h2>
<form method="GET" action="<?php echo PHP_SELF; ?>" onSubmit="return checkSearchForm();">
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
   <tr>   
	<td align="center">	
订单状态 : 
    <select name="status" id="status">
	<option value="0">|-全部</option>
    <?php echo $order_refund_status_option;?>
    </select>	

	用户UID：<input type="text" name="uid" value="<?php echo $uid;?>">　    
	　
	关键词：<input type="text" name="query_string" value="<?php echo $query_string;?>" placeholder="输入手机号或订单号或收货人姓名">	
	<input type="hidden" name="m" value="order.refund_list"/>
	<input type="submit" name="search_btn" value="　搜索　">	
	</td>	
  </tr>
</table>
</form>
    
<form name="list_form" method="POST" action="<?php echo PHP_SELF; ?>?m=order.refund_list&page=<?php echo $pageCurrent;?>" onSubmit="return checkDelForm();">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
      <tbody><tr>
      <th>&nbsp;</th>
        <th width="5%">编号</th>
        <th width="15%">商品信息</th>		
        <th width="5%">订单号</th>                               
        <th width="5%">佣金</th>
        <th width="6%">聚店佣金</th>
		<th width="6%">退款金额</th>                
		<th width="8%">买家用户</th>
        <th width="8%">分销商</th>
        <th width="8%">供应商</th>        		
		<th width="8%">退款时间</th>        		
		<th width="8%">售后方式</th>                        
		<th width="10%">状态</th>                        
        <th width="18%">管理</th>
      </tr>
	  <?php if($ErrorMsg) { ?>
	  <tr>
	    <td height=23 colspan=16 class=forumRowHigh align=center><?php echo $ErrorMsg;?></td>
	  </tr>
	  <?php } ?>      
      <?php if(is_array($rs)) foreach($rs AS $k => $v) { ?>
      <tr onmouseout="this.style.background='#fff'" onmouseover="this.style.background='#f6f9fd'" style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">
      
      <td><input type="checkbox" value="<?php echo $v->order_refund_id;?>" name="id_a[]"></td>
      <td><?php echo $v->order_refund_id;?></td>
      <td class="td_left">
	  		<?php if(is_array($v->order_goods_array)) foreach($v->order_goods_array AS $order_goods) { ?>
			<div class="cate" style="line-height: 22px;">
				<ul>
					<li><img src="<?php echo $order_goods->goods_image_url;?>" width="80" height="80"></li>
					<li><a target="_blank" href="<?php echo MOBILE_URL; ?>/item/<?php echo $order_goods->item_id;?>.html"><?php echo $order_goods->item_name;?></a></li>
					<li>数量：<?php echo $order_goods->item_number;?>x￥<?php echo $order_goods->item_price;?></li>
					<?php if($order_goods->goods_sku_name<>'') { ?>
					<li><?php echo $order_goods->goods_sku_name;?></li>
					<?php } ?>
				</ul>
			</div>	  
			<?php } ?>			
	  </td>	  
      <td><?php echo $v->order_sn;?></td>        	       
      <td>￥<?php echo $v->commission_fee;?></td> 
      <td>￥<?php echo $v->commission_fee_rank;?></td> 
      <td>￥<?php echo $v->money;?></td> 
	  <td><a href="<?php echo PHP_SELF; ?>?m=order&order_list_type=1&uid=<?php echo $v->uid;?>" title="查看买家<<?php echo $v->consignee;?>/<?php echo $v->mobile;?>>所有的订单"><?php echo $v->consignee;?>/<?php echo $v->mobile;?></a></td>             
      <td><a href="<?php echo PHP_SELF; ?>?m=order.refund_list&uid=<?php echo $v->item_uid;?>" title="查看店铺<<?php echo $v->shop_name;?>>的所有维权订单"><?php echo $v->shop_name;?></a>/<?php echo $v->item_mobile;?></td>
      <td><a href="<?php echo PHP_SELF; ?>?m=order.refund_list&uid=<?php echo $v->goods_uid;?>" title="查看供应商<<?php echo $v->supplier_mobile;?>>的所有维权订单"><?php echo $v->supplier_mobile;?></a></td>      
      <td><?php echo $v->refund_time;?></td>      
      <td><?php echo $v->refund_service_status_text;?></td>       
      <td class="f_f00"><?php echo $v->status_text;?></td>       
      <td><a href="<?php echo PHP_SELF; ?>?m=order.detail&order_id=<?php echo $v->order_id;?>">订单详情</a></td>
      </tr>
      <?php } ?>      
      <tr style="background: none repeat scroll 0% 0% rgb(248, 248, 248);">
              <td><input type="checkbox" name="select_all_btn" onclick="select_fx();"></td>
              <td>
				
			  </td>              
            </tr>
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