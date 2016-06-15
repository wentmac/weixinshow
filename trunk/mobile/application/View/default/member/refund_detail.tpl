<!doctype html>
<html>

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">
		<meta content="telephone=no" name="format-detection">
		<meta name="apple-touch-fullscreen" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<title>退款－{$config[cfg_webname]}</title>
		<link href="{$BASE_V}css/common/base.css" type="text/css" rel="stylesheet">
		<link href="{$BASE_V}css/order/kd.css" type="text/css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="{$BASE_V}css/user/user.css">
		<link rel="stylesheet" type="text/css" href="{$BASE_V}css/user/refundprogress.css">
		<link rel="stylesheet" type="text/css" href="{$BASE_V}css/user/refund.css">
		<style>
			#o_ul {
				background-color: #fff;
				padding: 20px;
			}
		</style>

	</head>

	<body>
	    <header id="common_hd" class="c_txt rel">
	        <a id="hd_back" class="abs comm_p8" href="{$referer_url}">返回</a>
	        <a id="common_hd_logo" class="t_hide abs common_hd_logo">退款进度</a>
	        <h1 class="hd_tle">退款进度</h1>
	        <a id="hd_enterShop" class="hide abs" href="{MOBILE_URL}member/home" style="display: block;"> <span id="hd_enterShop_img" class="abs"> <img class="block" src="" width="32" height="32" style="display: block;"> </span>会员中心</a>
	    </header>

		<div id="index_loading" class="loading" style="display: none;">&nbsp;</div>
		<div class="refundProgress" id="refundProgress" style="display: block;">
			<div class="progressInfo" id="progressInfo">
				<h2 id="refund_status"><span>!</span>{$order_refund_info->status_text}</h2>
				<p class="refundContact">
					<a href="tel:{$order_refund_info->supplier_mobile}" target="_blank">
						<em>联系商家</em><span></span></a>
				</p>
			</div>
			<div class="refund_wrap hide" id="logistics">
				<div class="returnG" id="yesreturn">
					<h2>填写退货物流信息</h2>
					<ul><a href="javascript:void(0)" class="e1" id="expressNum">退货单号</a>
						<li>
							<input type="text" placeholder="请填写退货时的快递单号" id="fedexNum"><span class="hide"><em></em></span>
						</li>
					</ul>
					<ul class="esp" id="fedex">
						<a href="javascript:void(0)" class="e2">快递公司</a>
					</ul>
					<input type="hidden" id="hid_express" data_id="" data_name="" />
					<h2 style=" background-color:#d40000; "><a id="btn_express" style="color:#fff;">提 交</a></h2>
				</div>
			</div>
			<div class="progress hide" id="progress">
				<h2><span></span>退款成功</h2>
				<p class="refundS" id="refundS">退款至你的<span>微信</span></p>
				<ul>
					<li><span class="step">1</span> <span class="text checktext">{$config[cfg_webname]}退款</span> <span class="time" id="wdTime">2015-06-20 17:00:25</span>
						<a class="jiantou"></a>
					</li>
					<li><span class="step">2</span> <span class="text checktext" id="centerName">微信处理中</span> <span class="time" id="centerTime">2015-06-20 17:00:25</span>
						<a class="jiantou"></a>
					</li>
					<li><span class="step">3</span> <span class="text checktext" id="thirdName">微信处理成功</span> <span class="time" id="thirdTime">预计2015-06-27 17:00:25之前到账</span></li>
				</ul>
				<p id="refundDate">● 若你使用微信余额付款，则退款至你的微信余额；若通过微信用银行卡付款，则退款至对应的银行卡；预计7个工作日内到账；</p>
				<p class="borderBottom">● 如果超时未收到退款，请联系客服 4008-456-090</p>
			</div>

			<div id="kd_loaded_items" class="hide" style="display: block;">
				<ul id="o_ul">
					<!--{loop $order_refund_info->order_goods_detail $v}-->
					<li class="o_li">
						<a href="/item/{$v->item_id}.html" class="o_a">
							<img src="{$v->goods_image_url}" width="55" height="55" class="left o_img">
							<p class="o_name over_hidden ellipsis ">{$v->item_name}</p>
							<p></p>
							<p>单价:{$v->item_price}</p>
							<p>数量:{$v->item_number}</p>
							<p>总计:￥ ${echo $v->item_number*$v->item_price} </p>
						</a>
						<p class="o_pri r_txt">
							<span class="left" id="refundTime">退款时间&nbsp;:&nbsp;{$order_refund_info->refund_time}</span>
						</p>
						<p class="refundBtn" id="refundBtn">
							<a id="refund" class="hide" style="margin-left:1%" data-for-gaq="退货">退货</a>
							<a id="cancle_request" class="hide"  data-for-gaq="取消申请">取消申请</a>
							<a id="customer_service" class="hide"  data-for-gaq="客服介入">客服介入</a>
							<a id="edit_return_goods" class="hide" data-for-gaq="修改退货信息"><span>修改退货信息</span></a>
							<a id="goods_customer" class="hide" data-for-gaq="{$config[cfg_webname]}客服"><span>{$config[cfg_webname]}客服</span></a>
						</p>
						<input class="hid_status_all" type="hidden" data_order_goods_id="191" data_service_status="0" data_service_status_text="" data_order_refund_id="0" data_return_service_status="">
					</li>
					<!--{/loop}-->
				</ul>
			</div>
			<div class="progressRecord">
				<a style="display: none;" id="refundHistory">查看详细记录<span></span>
					
				</a>
				<ul>
					<li><span class="left">● 退款类型</span> <span class="right" id="refundStyle">{$order_refund_info->refund_service_status}</span></li>
					<li><span class="left">● 退款原因</span> <span class="right" id="refundreason">{$order_refund_info->refund_service_reason}</span></li>
					<li><span class="left">● 退款金额</span> <span class="right" id="refundMoney">￥{$order_refund_info->money}元</span></li>

				</ul>
			</div>
		</div>
		<div class="closeOrderFloat hide" id="closeOrderFloat">
			<div class="fixBottom">
				<div class="refuse">
					<form>
						<p>
							<textarea placeholder="请输入拒绝理由（300字以内）" id="refundReason"></textarea>
						</p>
					</form>
					<p><span class="btncancel" id="refundNo">取消</span><span class="btncancel right" id="refundOk">拒绝</span></p>
				</div>
			</div>
		</div>
		<div class="commentList">
			<ul id="comment_ul" style="display: block;">
			<!--{loop $order_service_list $v}-->
				<li class="first"><span class="first abs"><em></em></span>
					<div class="comtent">
						<p class="user">{$v->service_title}</p>
						<p>{$v->service_username}：{$v->service_note}；</p>
						<p class="time">{$v->service_time}</p>
					</div>
				</li>
				<!--{/loop}-->
			</ul>
			<span class="btnAll hide" id="btnAll" style="display: none;">点击显示全部</span>
		</div>
		<script type="text/javascript" src="{STATIC_URL}common/assets/js/jquery.min.js"></script>
		<script type="text/javascript">
			var index_url = '{INDEX_URL}';
			var mobile_url = '{MOBILE_URL}';
			var static_url = '{STATIC_URL}';
			var base_v = '{$BASE_V}';
			var php_self = '{PHP_SELF}';
			var global_refund_status = '{$order_refund_info->refund_status}';
			var global_service_status = '{$order_refund_info->service_status}';
			var global_return_status = '{$order_refund_info->return_status}';
			var global_order_refund_id = '{$order_refund_info->order_refund_id}';
		</script>
		<script type="text/javascript" src="{$BASE_V}js/refund_detail.js"></script>
	</body>

</html>