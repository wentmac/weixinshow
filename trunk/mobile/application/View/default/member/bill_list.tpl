<!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">
		<meta content="telephone=no" name="format-detection">
		<meta name="apple-touch-fullscreen" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<title>账单列表 - {$config[cfg_webname]}</title>
		<link href="{$BASE_V}css/common/base.css" type="text/css" rel="stylesheet">
		<link href="{$BASE_V}css/user/user.css" type="text/css" rel="stylesheet">
		<style>						 
			  .orderListType ul li{width:20%;}
.shopList .p-sum {    
    border-bottom: none;    
}			  
.main {
    padding-bottom: 0px;
}
		</style>
	</head>

	<body>
		<section class="main">
		    <header id="common_hd" class="c_txt rel">
		        <a id="hd_back" class="abs comm_p8" href="{$referer_url}">返回</a>
		        <a id="common_hd_logo" class="t_hide abs common_hd_logo">我的账单</a>
		        <h1 class="hd_tle">我的账单</h1>
		        <a id="hd_enterShop" class="hide abs" href="{MOBILE_URL}member/home" style="display: block;"> <span id="hd_enterShop_img" class="abs"> <img class="block" src="" width="32" height="32" style="display: block;"> </span>会员中心</a>
		    </header>

			<div class="orderListTypeHolder">&nbsp;</div>
			<div class="orderListType" id="tabPlus">
				<ul>
					<li><a id="all" class="cur">全部</a></li>
					<li><a id="in">收入</a></li>
					<li><a id="waiting_confirm">待确认</a></li>
					<li><a id="expense_withdrawals_ing">提现中</a></li>
					<li><a id="expense_withdrawals_success">已提现</a></li>
				</ul>
			</div>

<div class="orderList" id="js_bill_list">
	<div style="display:block"> 
		<nav class="shopList">   			
			<nav class="probody maxheight">
			     <a class="product" href="http://dev.yph.weixinshow.com/member/order.detail?sn=2016030421542664775">       
			     <div class="flex">         
			     	<div class="flex-item"><img class="p-img" src="http://img.090.cn/thumb/goods_110/db/f8/a1cd03591659.jpg"></div>         
			     	<div class="flex-auto p-details">           
			     		<div class="flex">             
			     			<div class="flex-auto"><span class="p-name color-dark">张迪[自营收入][退款]申请商品退款</span></div>
			     			<div class="flex-item">
			     			    <div class="color-dark p-desc">￥-0.02</div>               
			     			    <div class="color-grey p-desc">退款成功</div>             
			     			</div>         
			     		</div>
			     	</div>
			     </div>
			     </a>
			</nav>
			<p class="p-sum p-actions clearfix">
				<span>	2015-07-08 17:30:47</span>
				<span class="btn btncancel refundHb"><a href="http://dev.yph.weixinshow.com/member/order.detail?sn=2016030421542664775"><em>查看详细</em></a></span>
			</p>   			
		</nav>

		<nav class="shopList">   			
			<nav class="probody maxheight">
			     <a class="product" href="http://dev.yph.weixinshow.com/member/order.detail?sn=2016030421542664775">       
			     <div class="flex">         
			     	<div class="flex-item"><img class="p-img" src="http://img.090.cn/thumb/goods_110/db/f8/a1cd03591659.jpg"></div>         
			     	<div class="flex-auto p-details">           
			     		<div class="flex">             
			     			<div class="flex-auto"><span class="p-name color-dark">张迪[自营收入][退款]申请商品退款</span></div>
			     			<div class="flex-item">
			     			    <div class="color-dark p-desc">￥-0.02</div>               
			     			    <div class="color-grey p-desc">退款成功</div>             
			     			</div>         
			     		</div>
			     	</div>
			     </div>
			     </a>
			</nav>
			<p class="p-sum p-actions clearfix">
				<span>	2015-07-08 17:30:47</span>
				<span class="btn btncancel refundHb"><a href="http://dev.yph.weixinshow.com/member/order.detail?sn=2016030421542664775"><em>查看详细</em></a></span>
			</p>   			
		</nav>

		<nav class="shopList">   			
			<nav class="probody maxheight">
			     <a class="product" href="http://dev.yph.weixinshow.com/member/order.detail?sn=2016030421542664775">       
			     <div class="flex">         
			     	<div class="flex-item"><img class="p-img" src="http://img.090.cn/thumb/goods_110/db/f8/a1cd03591659.jpg"></div>         
			     	<div class="flex-auto p-details">           
			     		<div class="flex">             
			     			<div class="flex-auto"><span class="p-name color-dark">张迪[自营收入][退款]申请商品退款</span></div>
			     			<div class="flex-item">
			     			    <div class="color-dark p-desc">￥-0.02</div>               
			     			    <div class="color-grey p-desc">退款成功</div>             
			     			</div>         
			     		</div>
			     	</div>
			     </div>
			     </a>
			</nav>
			<p class="p-sum p-actions clearfix">
				<span>	2015-07-08 17:30:47</span>
				<span class="btn btncancel refundHb"><a href="http://dev.yph.weixinshow.com/member/order.detail?sn=2016030421542664775"><em>查看详细</em></a></span>
			</p>   			
		</nav>				

		</div></div>
		</section>
		<p id="scroll_loading_txt" class="loading hide">&nbsp;</p>		
	</body>

</html>

<script type="text/javascript" src="{STATIC_URL}js/jquery/1.11.2/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="{STATIC_URL}js/jquery-plugin/jquery.tmpl.min.js"></script>

<script type="text/javascript">
	var index_url = '{INDEX_URL}';
	var mobile_url = '{MOBILE_URL}';
	var static_url = '{STATIC_URL}';
	var base_v = '{$BASE_V}';
	var php_self = '{PHP_SELF}';
	var global_status='{$status}';
</script>
<script type="text/javascript" src="{$BASE_V}js/bill_list.js"></script>