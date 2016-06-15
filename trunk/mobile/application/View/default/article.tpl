<!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">
		<meta content="telephone=no" name="format-detection">
		<meta name="apple-touch-fullscreen" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<title>{$article_info->title}</title>
		<link href="{STATIC_URL}common/assets/css/amazeui.css" type="text/css" rel="stylesheet">
		<link href="{$BASE_V}css/common/base.css" type="text/css" rel="stylesheet">
		<link href="{$BASE_V}css/index/index.css" type="text/css" rel="stylesheet">
		<link href="{$BASE_V}css/item/item.css" type="text/css" rel="stylesheet">
		<link href="{$BASE_V}css/common/itemListTemplate.css" type="text/css" rel="stylesheet">
		<link href="{$BASE_V}css/market/recommend.css" type="text/css" rel="stylesheet">
		<style>
			#item_name{ text-align: center; line-height: 30px; padding-bottom: 10px}		
		</style>
<style>img{border:0;}ul,li{padding:0;margin:0;}.box_lanrenzhijia {z-index:99;right:0;width:128px;height:195px;position:absolute;}.box_lanrenzhijia .press{right:0;width:36px;cursor:pointer;position:absolute;height:128px;}.box_lanrenzhijia .zzjs_net_list{left:0;width:131px;position:absolute;height:195px;background:url(images/20120321lanrenzhijia_1.gif) no-repeat left center;}.box_lanrenzhijia .zzjs_net_list ul{padding:37px 0 0 21px;}.box_lanrenzhijia .zzjs_net_list li{height:26px;margin-bottom:3px;_margin-bottom:3px; list-style-type:none;}</style>		
	</head>

	<body style="padding-bottom: 60px;">
		<div id="item_show_wrap" style="height: auto; overflow: visible;">
			<!--{template inc/header}-->
			<div id="item_wrap_loading" class="loading" style="display: none;">&nbsp;</div>
			<div id="item_info_for_show_wrap" class="hide" style="display: block;">
				
				<section id="item_info" class="rel">
					<h2 id="item_name"><strong>{$article_info->title}</strong><br></h2>
					<div class="itemrank hide" id="itemrank"></div>
				</section>			
				<section id="item_detail">
					<div id="detail_wrap" style="padding:10px">				
						{$article_info->content}
					</div>
				</section>
			</div>
			<footer id="item_fix_btn" class="fix hide wrap" style="-webkit-transition: opacity 200ms ease; transition: opacity 200ms ease; opacity: 1; display: block;">
			</footer>
		</div>
	</body>
</html>
<script type="text/javascript">
	var index_url = '{INDEX_URL}';
	var mobile_url = '{MOBILE_URL}';
	var static_url = '{STATIC_URL}';
	var base_v = '{$BASE_V}';
	var php_self = '{PHP_SELF}';	
</script>

<!--{template inc/js}-->
<script type="text/javascript" src="{$BASE_V}js/mobile_slider.js"></script>
<script type="text/javascript" src="{STATIC_URL}common/assets/js/amazeui.min.js"></script>
<script type="text/javascript" src="{STATIC_URL}js/modal_html.js"></script>
<script type="text/javascript" src="{$BASE_V}js/jquery.lazyload.js"></script>
<script>
$(".slider").yxMobileSlider({
	width: 640,
	height: 640,
	during: 3000
});
</script>