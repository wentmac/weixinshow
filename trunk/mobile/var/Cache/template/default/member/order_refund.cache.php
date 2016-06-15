<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\member/order_refund.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\mobile\application\View\default\member\order_refund.tpl', 1461576051)
;?>
<!doctype html>
<html>

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">
		<meta content="telephone=no" name="format-detection">
		<meta name="apple-touch-fullscreen" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<title>退款－<?php echo $config['cfg_webname'];?></title>
		<link href="<?php echo $BASE_V;?>css/common/base.css" type="text/css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="<?php echo $BASE_V;?>css/user/user.css">
	</head>

	<body>
	    <header id="common_hd" class="c_txt rel">
	        <a id="hd_back" class="abs comm_p8" href="<?php echo $referer_url;?>">返回</a>
	        <a id="common_hd_logo" class="t_hide abs common_hd_logo">申请退款</a>
	        <h1 class="hd_tle">申请退款</h1>
	        <a id="hd_enterShop" class="hide abs" href="<?php echo MOBILE_URL; ?>member/home" style="display: block;"> <span id="hd_enterShop_img" class="abs"> <img class="block" src="" width="32" height="32" style="display: block;"> </span>会员中心</a>
	    </header>

		<div class="refund_wrap" style="text-align: center;color:red">
		<?php echo $error_message;?>
		</div>
		<div class="refund_wrap" id="refundMoney">
			<div class="money_main">
				<ul>
					<li><span><em>*</em>是否退货</span>
						<select id="needProduct" name="needProduct" tabindex="1">
							<option class="esp" value="0">请选择是否退货</option>
							<?php echo $refund_service_status_option;?>
						</select>
					</li>
					<li><span><em>*</em>退款原因</span>
						<select id="refundReason" name="refundReason" tabindex="2">
							<option class="esp" value="0">请选择退款原因</option>
							<?php echo $refund_service_reason_option;?>
						</select>
					</li>
					<li><span><em>*</em>退款金额</span>
						<input type="number" placeholder="请输入退款金额" id="priceNeed"> <em id="refundPrice"></em>
					</li>
				</ul>
			</div>
		</div>
		
		<footer class="footer">
			<div class="footerMain">
				<p class="refundAction"><a class="btnok c_txt abs for_gaq" data-for-gaq="申请退款" id="btnOk">提交</a> <a class="btnok c_txt abs for_gaq" style="display:none" data-for-gaq="申请退款" id="refundOK">确认退款</a>
				</p>
			</div>
		</footer>
		<script src="<?php echo STATIC_URL; ?>common/assets/js/jquery.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			var index_url = '<?php echo INDEX_URL; ?>';
			var mobile_url = '<?php echo MOBILE_URL; ?>';
			var static_url = '<?php echo STATIC_URL; ?>';
			var base_v = '<?php echo $BASE_V;?>';
			var php_self = '<?php echo PHP_SELF; ?>';
			var global_order_sn='<?php echo $order_sn;?>';
			var global_order_goods_id='<?php echo $order_goods_id;?>';
		</script>
		<script src="<?php echo $BASE_V;?>js/order_refund.js" type="text/javascript"></script>
	</body>

</html>