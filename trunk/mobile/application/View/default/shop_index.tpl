<html>

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">
		<meta content="telephone=no" name="format-detection">
		<meta name="apple-touch-fullscreen" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<title>{$shop_info->shop_name}</title>
		<link href="{$BASE_V}css/common/base.css" type="text/css" rel="stylesheet">
		<link href="{$BASE_V}css/index/index.css?v=2" type="text/css" rel="stylesheet">
		<link href="{$BASE_V}css/common/itemListTemplate.css" type="text/css" rel="stylesheet">
		<link href="{$BASE_V}css/others/timeline.css" type="text/css" rel="stylesheet">
		<style>
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

.cate_menu .fold {
    top: 18px;
    margin-left: 10px;
    margin-right: 5px;
    cursor: pointer;
}
.cate_menu .fold img{
	vertical-align: middle;
}
.new-slider-floor{background-color:#fff;margin-bottom:8px;box-shadow:0 1px 1px #ebebeb;}
.slider-wrapper{width:100%;overflow:hidden;background-color:#fff;position:relative;height:auto;}
.new-slide{margin:0;padding:0;position:relative;}
.slide-li{width:100%;visibility:visible;background-color:#fff;float:left;display:-webkit-box;-webkit-box-pack:center;-webkit-box-align:center;}
.slide-li img{display:block;overflow:hidden;width:100%;}
.focus span {
  border-radius:7px;-webkit-border-radius:7px;-ms-border-radius:7px;height:6px;width:6px;border:1px solid #fff;margin:0 4px;display:inline-block;opacity:1;
}
.focus span.current {
  background: #FFF;
}
	</style>
	</head>

	<body>
		<div id="sortLoading" style="display: none; background-color: transparent; background-position: -100px 0px;"></div>
		<div id="wd_show">
			<!--{template inc/header}-->
			<header id="index_hd" class="rel">

		      <div class="viewport-new" id="newviewport">
		        <div class="floor new-slider-floor">
		            <div id="slider" class="slider-wrapper">
		              <ul id="slider_touch" class="new-slide" data-slide-ul="firstUl">
		                <!--{loop $shop_index_banner $value}-->
		                <li data-ul-child="child" class="slide-li">
		                  <a class="J_ping" report-eventid="MHome_FocusPic" report-eventparam="5_0_0_63127" page_name="index" href="{MOBILE_URL}{$value[url]}">
		                    <img alt="{$value[title]}" src="{$value[img_url]}">
		                  </a>
		                </li>
		                <!--{/loop}-->
		              </ul>
		            </div>
		        </div>	        

		      </div>

				<div id="hd_bg" class="over_hidden rel">
					<div id="hd_bg_div" class="abs"></div>
					<div id="favorite" class="favorite for_gaq hide" data-for-gaq="收藏店铺">收藏</div>
				</div>

				<section id="index_hd_info_wrap">
					<div id="index_hd_abs" class="wrap rel">
						<h3 id="vshop_icon" class="hide" style="display: block;"><img width="100%" height="100%" src="{$shop_info->shop_image_url}"> <em id="shop_bindWx" class="abs hide">&nbsp;</em></h3>
						<div id="index_hd_shop_info" class="index_hd_shop_info_line3">
							<h1 id="hd_name" class="over_hidden ellipsis block wrap" data-name="{$shop_info->shop_name}">{$shop_info->shop_name}</h1>
						</div>
					</div>
					<p id="free_postage" class="hide"></p>
					<div id="hd_intro" class="hide hd_note_need_animate" style="display: block;">
						<p id="hd_note" class="hide" style="display: block;">{$shop_info->shop_intro}</p>
					</div>

					<div class="search_goods">						
							<input type="text" class="search_bar" name="search" id="search_query" placeholder="输入商品名称">
							<input type="submit" class="search_btn" id="index_search_btn" value="搜索">						
					</div>	

				</section>

				<nav class="quick-entry-nav position-r">
					<!--{loop $shop_index_button $value}-->
					<a class="quick-entry-link fz12 J_ping" report-eventlevel="1" report-eventid="MHome_BIcons" report-eventparam="{$value[title]}" page_name="index" href="{MOBILE_URL}{$value[url]}"><img width="40" height="40" src="{$value[img_url]}"><span style="color:#000000">{$value[title]}</span></a>
					<!--{/loop}-->			
				</nav>
		      <div class="floor-item">
      			<!--{loop $shop_index_image $value}-->
                <div class="container-col02 padding-r-1">
					<a report-eventid="MHome_BFloor" report-eventlevel="1"  page_name="index" href="{MOBILE_URL}{$value[url]}">
						<img src="{$value[img_url]}" class="opa1" title="{$value[title]}">
					</a>
                </div>
                <!--{/loop}-->                
            </div>
            </header>
				
            <section class="ad-box">
            	<h3 class="i_title self_title"><em>精选分类</em></h3>
            	<div class="floor-items">
            	<!--{loop $shop_index_category $value}-->
                  <div class="container-col03">
                  	<a href="{MOBILE_URL}{$value[url]}">
	                  	<div class="cont-left">
	                  		<h2>{$value[title]}</h2>
	                  		<span>{$value[self_field]}</span>
	                  	</div>
	                  	<div class="cont-right">
							  <img src="{$value[img_url]}" class="opa1">
						</div>
					</a>
                    </div>
                  <!--{/loop}-->                  
				 </div>
            </section>
			<section id="index_sec">
				<div id="shopList">
					<div id="index_loading" class="loading" style="display: none;">&nbsp;</div>
					<div class="i_wrap margin_auto rel" id="recommend_wrap" style="display: none;">
						<h3 class="i_title abs"><p id="top_i_title_p" class="i_title_p over_hidden ellipsis">店长推荐</p></h3>
						<ul class="i_ul rel" id="top_ul">
						</ul>
						<div class="clear"></div>
						<div class="i_list_bottom"></div>
					</div>
					<div id="hot_items">
					</div>
					<p id="scroll_loading_txt" class="loading" style="display: none;">&nbsp;</p>
				</div>
				<div class="timeline_list hide" id="timeLineList">
					<div class="timeline_main">
						<ul id="diaryList"></ul>
						<div id="timelineLoading"></div>
					</div>
				</div>
				<div id="item_empty" class="c_txt hide">小店新开张，正在上新中！
					<br>有什么需要，点“联系卖家”告诉我哟！</div>
					<a href="{MOBILE_URL}" target="_blank" id="iWantAShopIndex" class="c_txt for_gaq rel hide" data-for-gaq="首页－我也要开{$config[cfg_webname]}" style="margin-top: 40px;">&nbsp;
        	<span id="doReport1" style="position: absolute; display: none; right: 0px; top: -40px;">
        	<a class=" hide"  href="">举报该店铺</a></span>
				</a>
			</section>
		</div>
		<div id="noShop" class="hide">
			<header id="common_hd_none" class="rel c_txt"><a id="common_hd_logo" class="t_hide abs common_hd_logo_noBack">{$config[cfg_webname]}</a>
				<h1 class="hd_tle bold">{$config[cfg_webname]}</h1></header>
			<div id="noShopShow" class="c_txt">服务器开个小差，请稍后重试。</div>
		</div>

		<footer class="index_nav">
			<ul class="wd_nav">
				<li class="classify for_gaq" data-for-gaq="点击分类;店铺页">分类</li>
				<li class="footer_cart for_gaq" data-for-gaq="点击联系卖家;店铺页">购物车</li>
				<li class="footer_order for_gaq" data-for-gaq="我的订单">我的订单</li>
			</ul>
		</footer>
		<div id="classifyPanel" style="display:none">
			<div class="wrap">
				<div class="search_wrap">
					<div class="search_input_wrap"><em class="search_icon"></em>
						<input class="search" type="search" name="query" id="query" placeholder="请输入商品名称"> <em class="search_clear"></em></div><span class="search_btn" id="search_btn">搜索</span>
					<div class="clear"></div>
				</div>
				<div class="class_list">
					<h3 style="display: block;">商品分类</h3>
					<div id="classListScrollWrapper" style="position:relative;overflow:hidden">
						<ul id="cate_menu" class="cate_menu"></ul>
					</div>
				</div>
			</div>
			<div class="classify_arrow"></div>
			<div class="mask_area"></div>
		</div>
		<div class="cart_btn for_gaq" data-for-gaq="进入购物车;店铺页" style="right: 651px;"><span></span></div>
	</body>

</html>

<script type="text/javascript" src="{STATIC_URL}common/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="{STATIC_URL}js/jquery-plugin/jquery.tmpl.min.js"></script>
<script type="text/javascript" src="{STATIC_URL}js/jquery-plugin/ui/minified/jquery.cookie-min.js"></script>
<script type="text/javascript" src="{$BASE_V}js/common.js?v=201634"></script>
<script type="text/javascript">
var index_url = '{INDEX_URL}';
var mobile_url = '{MOBILE_URL}';
var static_url = '{STATIC_URL}';
var base_v = '{$BASE_V}';
var php_self = '{PHP_SELF}';
var gloabl_shop_info = {$shop_info_json};
var pagesize = '{$pagesize}';
var p = '{$p}';
var y = '{$y}';
</script>
<script type="text/javascript" src="{$BASE_V}js/shop_index.js?v=8"></script>
<script type="text/javascript" src="{$BASE_V}js/mobile_slider.js"></script>
<script type="text/javascript">
  $(function(){
    $("#slider").yxMobileSlider({
        width: 640,
        height: 311,
        during: 3000
      });
  })
</script>