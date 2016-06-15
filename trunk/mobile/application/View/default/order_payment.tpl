<!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8">
		<title>收银台</title>
		<link rel="apple-touch-icon" href="favicon.png">
		<meta name="format-detection" content="telephone=no">
		<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="default">
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Cache-Control" content="no-cache">
		<meta http-equiv="Expires" content="0">
		<link href="{$BASE_V}css/order/payment.css" type="text/css" rel="stylesheet">
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
		<!--主页面-->
		<header>
			<div class="page-title">
				<h1>支付中心</h1></div>
			<div class="row">
				<div class="row-fluid">
					<div class="row-p">商品名称：<span class="green">{$order_subject}</span></div>
					<div class="row-p">付款金额：<span class="green">{$total_amount}元</span> </div>
				</div>
			</div>
		</header>
		<ul class="pay-list">
			<!--第三方支付-->
			<li class="cart threepay" data-code="WXPAY" data-id="1102" id="WXPAY">
				<img src="{$BASE_V}image/WXPAY.png">微信支付
			</li>
			<li class="cart threepay" data-code="ALIPAY" data-id="1309" id="ALIPAY">
				<img src="{$BASE_V}image/ALIPAY.png">支付宝
			</li>
		</ul>
		<!--第三方支付表单-->
		<div id="bankForm"></div>
		<footer><span style="font-size:12px;"><a href="/cashier/faq/list.html">支付常见问题</a><br>版权所有©2014-2015 {$config[cfg_webname]}</span></footer>
		<div class="tip-box" style="display:none;">
			<div class="alert">
				<span id="timeSpan">跳转中...</span>
			</div>
		</div>
		<div class="guide-box" style="display:none;">
			
		</div>
		<form id="form_alipayment" name=alipayment action="{MOBILE_URL}pay/alipay.alipayto?sn={$order_sn}" method="post" style="display:none">
		<span class="new-btn-login-sp">		
		<button class="new-btn-login" type="submit" style="text-align:center;">付 款</button>
		</span>
		</form>		
	</body>

</html>
<script type="text/javascript" src="{STATIC_URL}common/assets/js/jquery.min.js"></script>
<script type="text/javascript">	
	$(function(){
		if(is_weixin()){
			$("#WXPAY").show();
			$("#ALIPAY").hide();
		}
		else{
			$("#WXPAY").hide();
		}
	})	
	 //是否使用微信支付
	function is_weixin() {
		var ua = navigator.userAgent.toLowerCase();
		if (ua.match(/MicroMessenger/i) == "micromessenger") {
			return true;
		} else {
			return false;
		}
	}
	$(function() {	
		$("#ALIPAY").click(function() {
			$("#form_alipayment").submit();
		});		
		$('#WXPAY').click(function(){		
			window.location.href = '{MOBILE_URL}pay/wechatpay.unifiedorder?sn={$order_sn}';
		});		
	});
</script>