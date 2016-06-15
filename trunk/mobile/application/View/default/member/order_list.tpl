<!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">
		<meta content="telephone=no" name="format-detection">
		<meta name="apple-touch-fullscreen" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<title>订单列表 - {$config[cfg_webname]}</title>
		<link href="{$BASE_V}css/common/base.css" type="text/css" rel="stylesheet">
		<link href="{$BASE_V}css/user/user.css" type="text/css" rel="stylesheet">
		<style id="style-1-cropbar-clipper">
			/* Copyright 2014 Evernote Corporation. All rights reserved. */
			  
			  .en-markup-crop-options {
			    top: 18px !important;
			    left: 50% !important;
			    margin-left: -100px !important;
			    width: 200px !important;
			    border: 2px rgba(255, 255, 255, .38) solid !important;
			    border-radius: 4px !important;
			  }
			  
			  .en-markup-crop-options div div:first-of-type {
			    margin-left: 0px !important;
			  }
		</style>
	</head>

	<body>
		<section class="main">
		    <header id="common_hd" class="c_txt rel">
		        <a id="hd_back" class="abs comm_p8" href="{$referer_url}">返回</a>
		        <a id="common_hd_logo" class="t_hide abs common_hd_logo">我的订单</a>
		        <h1 class="hd_tle">我的订单</h1>
		        <a id="hd_enterShop" class="hide abs" href="{MOBILE_URL}member/home" style="display: block;"> <span id="hd_enterShop_img" class="abs"> <img class="block" src="" width="32" height="32" style="display: block;"> </span>会员中心</a>
		    </header>

			<div class="orderListTypeHolder">&nbsp;</div>
			<div class="orderListType" id="tabPlus">
				<ul>
					<li><a href="{MOBILE_URL}member/order" class="cur">全部</a>
					</li>
					<li><a id="waiting_payment" style="position:relative; display: block;">待付款<!--<span class="color-red">(2)</span>--></a>
					</li>
					<li><a id="wating_seller_delivery">待发货</a>
					</li>
					<li><a id="wating_receiving" >待收货</a>
					</li>
					<li><a id="wating_comment" >待评价</a>
					</li>
					<li><a id="complete" >已完成</a>
					</li>
					<li><a id="close" >已关闭</a>
					</li>
				</ul>
			</div>
			<div class="orderList" id="js_orderList" style="margin-bottom:2rem">

			</div>
		</section>
		<p id="scroll_loading_txt" class="loading hide">&nbsp;</p>
		<script type="text/javascript" src="{STATIC_URL}common/assets/js/jquery.min.js"></script>
		<script type="text/javascript" src="{STATIC_URL}js/jquery-plugin/jquery.tmpl.min.js"></script>
		
		<script type="text/javascript">
			var index_url = '{INDEX_URL}';
			var mobile_url = '{MOBILE_URL}';
			var static_url = '{STATIC_URL}';
			var base_v = '{$BASE_V}';
			var php_self = '{PHP_SELF}';
			var global_status='{$status}';
			
			
		</script>
		<script type="text/javascript" src="{$BASE_V}js/order_list.js"></script>
	</body>

</html>