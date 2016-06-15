<!doctype html>
<html>

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">
		<meta content="telephone=no" name="format-detection">
		<meta name="apple-touch-fullscreen" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<title>交易评价 - {$config[cfg_webname]}</title>
		<link href="{$BASE_V}css/common/base.css" type="text/css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="{$BASE_V}css/user/judge.css">
	</head>

	<body>
		<section class="main">
		    <header id="common_hd" class="c_txt rel">
		        <a id="hd_back" class="abs comm_p8" href="{$referer_url}">返回</a>
		        <a id="common_hd_logo" class="t_hide abs common_hd_logo">交易评价</a>
		        <h1 class="hd_tle">交易评价</h1>
		        <a id="hd_enterShop" class="hide abs" href="{MOBILE_URL}member/home" style="display: block;"> <span id="hd_enterShop_img" class="abs"> <img class="block" src="" width="32" height="32" style="display: block;"> </span>会员中心</a>
		    </header>

			<div id="item_wrap_loading" class="loading" style="display: none;">&nbsp;</div>
			<div id="productList" class="hide" style="display: block;">
				<h2><p class="time">2015-06-25</p><p>在{$shop_name}购买的商品</p></h2>
				<ul>

					<!--{loop $order_goods_array $v}-->

					<li class="noborder" id="order_goods_{$v->order_goods_id}">
						<p class="info"><img src="$v->goods_image_id"><span>$v->item_name</span></p>
						<p class="rank-num">
							<span class="e1 eclick" mark="1" t="0"></span>
							<span class="e2 eclick" mark="2" t="0"></span>
							<span class="e3 eclick" mark="3" t="0"></span>
							<span class="e4 eclick" mark="4" t="0"></span>
							<span class="e5 eclick markVal" mark="5" t="0"></span>
							<span class="grey"></span>
							<span id="orange0" mark="5" class="orange" data-itemid="1343435386" style="width: 100px;">
								</span>
						</p>
						<p></p>
						<p class="txt">
							<textarea data_order_goodid="$v->order_goods_id" placeholder="这件商品怎么样？点评一下吧 :）" class="commentVal"></textarea>
						</p>
					</li>
					<!--{/loop}-->
				</ul>
				<p class="judgeBtn"><span>请为商品打分或发表评论</span>
					<a href="javascript:void(0)" class="btnok" id="btnok">发表评价</a></p>
			</div>
		</section>
		 <script type="text/javascript" src="{STATIC_URL}common/assets/js/jquery.min.js"></script>
		 <script type="text/javascript" src="{STATIC_URL}js/json2.js"></script>
		 <script type="text/javascript">
			var index_url = '{INDEX_URL}';
			var mobile_url = '{MOBILE_URL}';
			var static_url = '{STATIC_URL}';
			var base_v = '{$BASE_V}';
			var php_self = '{PHP_SELF}';
			var global_order_sn='{$order_sn}';
			
			
		</script>
		<script type="text/javascript" src="{$BASE_V}js/order_comment.js"></script>
		
		
		
	</body>

</html>