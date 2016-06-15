<!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">
		<meta content="telephone=no" name="format-detection">
		<meta name="apple-touch-fullscreen" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<title>{$config[cfg_webname]}</title>
		<link href="{$BASE_V}v1/css/common/base.css" type="text/css" rel="stylesheet">
		<link href="{$BASE_V}v1/css/index/index.css" type="text/css" rel="stylesheet">
		<link href="{$BASE_V}v1/css/search/search_result.css" type="text/css" rel="stylesheet">
		<link href="{$BASE_V}v1/css/common/itemListTemplate.css" type="text/css" rel="stylesheet">
		<link href="http://s.koudai.com/css/search/search_result.css?v=2015052700133" type="text/css" rel="stylesheet">
		<link href="http://s.koudai.com/css/common/itemListTemplate.css?v=2015052700133" type="text/css" rel="stylesheet">
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
		<header id="common_hd" class="c_txt rel ellipsis_common_hd"><a id="hd_back" class="abs comm_p8 hide" onclick="window.history.go(-1)" style="display: block;">返回</a> 
			<a id="common_hd_logo" class="hd_logo t_hide abs" href="#">{$config[cfg_webname]}</a>
			<h1 class="hd_tle bold" id="item_classes_des">{$item_category_info->cat_name}</h1>
			<a id="hd_enterShop" class="hide abs" href="/shop/{$shop_info->shop_id}" style="display: block;">
				<span id="hd_enterShop_img" class="abs">
					<img class="block" src="{$shop_info->shop_image_url}" width="32" height="32" style="display: block;">
				</span>进入店铺
			</a>
		</header>
		<div id="index_loading" class="loading" style="display: none;">&nbsp;</div>
		<section id="search-content">
			<div class="i_wrap margin_auto rel hide" id="item_classes_list_wrap" style="display: block;">
				<ul class="i_ul rel" id="hot_ul">
				</ul>
				<div class="clear"></div>
			</div>
		</section>
		<p id="scroll_loading_txt" class="loading hide">&nbsp;</p>
		<div id="item_empty" class="hide c_txt">对不起，该分类下暂无商品</div>
		<a href="{INDEX_URL}" target="_blank" id="iWantAShopIndex" class="block c_txt for_gaq rel" data-for-gaq="商品分类页－我也要开{$config[cfg_webname]}">&nbsp;</a>
		<section id="index_hd_info_wrap" class="hide">
			<div id="index_hd_abs" class="wrap rel">
				<h3 id="vshop_icon" class="hide">
					<img width="100%" height="100%"> 
					<em id="shop_bindWx" class="abs hide">&nbsp;</em>
				</h3>
				<div id="index_hd_shop_info">
					<h1 id="hd_name" class="over_hidden ellipsis block wrap"></h1>
					<div id="hd_weixin" class="rel hide over_hidden ellipsis"></div>
					<p id="hd_fav_count" class="over_hidden ellipsis hide">
						<em id="hd_fav_count_em">0</em> 人收藏</p>
					<div id="hd_level_cert" class="hide">
						<a id="wd_level_wrap" class="left hide">&nbsp;</a> 
						<a id="index_cert_box" class="left hide">&nbsp;</a>
					</div>
				</div>
			</div>
			<p id="free_postage" class="hide"></p>
			<div id="hd_intro" class="hide">
				<p id="hd_note" class="hide"></p>
			</div>
			<a class="enter-shop" id="bottomEnterShop">
				<span>进入店铺</span> <span class="right-arrow"></span>
			</a>
		</section>
		<script type="text/javascript" src="{STATIC_URL}common/assets/js/jquery.min.js"></script>
		<script type="text/javascript" src="{STATIC_URL}js/jquery-plugin/jquery.tmpl.min.js"></script>
		<script type="text/javascript" src="{STATIC_URL}js/json2.js"></script>
		<script type="text/javascript">
			var index_url = '{INDEX_URL}';
			var mobile_url = '{MOBILE_URL}';
			var static_url = '{STATIC_URL}';
			var base_v = '{$BASE_V}';
			var php_self = '{PHP_SELF}';
			var global_shop_info = JSON.parse('{$shop_info_json}');
			var global_item_cat_id = {$item_category_info->item_cat_id};
			var global_item_count = {$item_category_info->item_count};
		</script>
		<script type="text/javascript" src="{$BASE_V}js/itemlist.js"></script>
	</body>

</html>