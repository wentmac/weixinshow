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
				<h1>微信支付</h1></div>
			<div class="row">
				<div class="row-fluid">
					<div class="row-p">商品名称：<span class="green">{$order_subject}</span></div>
					<div class="row-p">付款金额：<span class="green">{$total_amount}元</span> </div>
				</div>
			</div>
		</header>		
		<div id="bankForm"></div>
		<footer><span style="font-size:12px;">版权所有©2014-2015 {$config[cfg_webname]}</span></footer>			
	</body>

</html>
<script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
				'getBrandWCPayRequest',
				{$jsApiParameters},
				function (res) {
					if ( res.err_msg == "get_brand_wcpay_request:ok" ) {
					// 使用以上方式判断前端返回,微信团队郑重提示：res.err_msg将在用户支付成功后返回    ok，但并不保证它绝对可靠。 
						window.location.href = '{MOBILE_URL}order/success?sn={$order_sn}';
					}  else {
						//WeixinJSBridge.log(res.err_msg);
						alert(res.err_code + res.err_desc + res.err_msg);
						window.location.href = '{MOBILE_URL}order/fail?sn={$order_sn}&message='+res.err_code + res.err_desc + res.err_msg;
					}
				}
		);
	}
	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined") {
			if (document.addEventListener) {
				document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
			} else if (document.attachEvent) {
				document.attachEvent('WeixinJSBridgeReady', jsApiCall);
				document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
			}
		} else {
			jsApiCall();
		}
	}
	window.onload=callpay();
</script>