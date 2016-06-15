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
		<header id="search-head" class="c_txt rel"><a id="hd_back" class="abs comm_p8" href="/?userid=260235404">返回</a>
			<div id="search-box" class="rel">
				<div class="search_left"><em id="tb_title" class="abs tb_serch_toggle"></em>
				</div>
				<form id="tb_search_form2" name="tb_search_form" method="get" action="search_result.html">
					<input id="tb_search2" name="tb_search" class="block wrap" type="search" placeholder="搜索本店商品">
					<input id="userid" name="userid" type="hidden" value="260235404"> <a id="search_button" class="abs tb_button">搜索</a>
				</form>
			</div>
		</header>
		<div id="search_result_empty" class="hide c_txt">对不起，没搜索出任何东西</div>
		<section id="search-content" class="hide">
			<div class="i_wrap margin_auto rel hide" id="search_result_list_wrap" style="display: block;">
				<ul class="i_ul rel" id="hot_ul">
					
				</ul>
				<div class="clear"></div>
				<div class="i_list_bottom"></div>
			</div>
		</section>
		<section id="shop_classes_wrap" class="over_hidden hide" style="display: block;">
			<ul id="shop_classes_ul" class="rel">
				<li id="shop_classes_li_hd" class="shop_classes_li">商品分类</li>

			</ul>
		</section>
		<p id="scroll_loading_txt" class="c_txt loading hide">&nbsp;</p>
		<script type="text/javascript" src="{STATIC_URL}common/assets/js/jquery.min.js"></script>
		<script type="text/javascript" src="{STATIC_URL}js/jquery-plugin/jquery.tmpl.min.js"></script>
		<script type="text/javascript" src="{STATIC_URL}js/json2.js"></script>
		<script type="text/javascript">
			var index_url = '{INDEX_URL}';
			var mobile_url = '{MOBILE_URL}';
			var static_url = '{STATIC_URL}';
			var base_v = '{$BASE_V}';
			var gloabl_shop_info = JSON.parse('{$shop_info_json}');
		</script>
		<script type="text/javascript" src="{$BASE_V}v1/js/shop_search.js"></script>
	</body>

</html>