<!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8">
		<link rel="apple-touch-icon-precomposed" href="/i/app-icon72x72@2x.png">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>用户中心</title>
		<meta name="description" content="用户中心">
		<meta name="keywords" content="index">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<meta name="renderer" content="webkit">
		<meta name="apple-mobile-web-app-title" content="Amaze UI" />
		<link href="{$BASE_V}assets/css/amazeui.css" rel="stylesheet" type="text/css">
		<link href="{$BASE_V}assets/css/admin.css" rel="stylesheet" type="text/css">
		<link href="{$BASE_V}css/base.css" type="text/css" rel="stylesheet">
		<link href="{$BASE_V}css/page.css" type="text/css" rel="stylesheet">
			<style>
				td{ min-width:180px; text-align: left; border-bottom:1px solid #f1f1f1 ;}
				
			</style>
	</head>

	<body>
		<!--{template inc/header_paul}-->
		<div class="am-cf admin-main">
			<!--{template inc/sidebar_paul}-->
			<!-- content start -->
			<div class="admin-content">
				<div class="am-cf am-padding">
					<div class="am-fl"><strong class="am-text-primary am-text-lg">退款详情-{$order_refund_info->service_note}</strong></div>
				</div>
				<hr/>
				<div class="am-g">
					<div class="am-u-sm-6">
						订单编号：{$order_refund_info->order_sn}
						<br> 买家：{$order_refund_info->consignee}
						<br> 申请时间：{$order_refund_info->refund_time}
						<br> 申请类型：{$order_refund_info->refund_service_status}
						<br> 申请原因：{$order_refund_info->refund_service_reason}
						<br> 退款金额：{$order_refund_info->money}
						<br> 描述：{$order_refund_info->refund_note}
						<br> 图片举证：
						<ul data-am-widget="gallery" class="am-gallery am-avg-sm-3 am-gallery-default" data-am-gallery="{ pureview: true }">
							<!--{loop $order_refund_info->refund_images $refund_images_url}-->
							<li>
								<div class="am-gallery-item">
									<a class="">
										<img src="{$refund_images_url}">
									</a>
								</div>
							</li>
							<!--{/loop}-->
						</ul>
					</div>
				</div>
				<hr/>
				<div class="am-g">
					<div class="am-u-sm-6">
						<!--{if $order_refund_info->service_status == 1 && $order_refund_info->refund_status == 1}-->
						<!--{if $order_refund_info->supplier_status == true}-->
						<p><a id="agree_refund" data_refund_id="{$order_refund_info->order_refund_id}" class="am-btn am-btn-success">同意退款</a>
							<a id="refused_refund" data_refund_id="{$order_refund_info->order_refund_id}" class="am-btn am-btn-danger">拒绝退款</a>
						</p>
						<!--{else}-->
						<a>联系供应商:{$order_refund_info->supplier_mobile}</a>
						<!--{/if}-->
						<!--{/if}-->
						<!--{if $order_refund_info->service_status == 1 &&  $order_refund_info->refund_status == 2 &&  $order_refund_info->return_status == 2}-->
						<!--{if $order_refund_info->supplier_status == true}-->
						<p><a id="goods_ok" class="am-btn am-btn-success">收到退货</a><a id="goods_no" class="am-btn am-btn-danger">没有收到退货</a></p>
						<!--{else}-->
						<a>联系供应商:{$order_refund_info->supplier_mobile}</a>
						<!--{/if}-->
						<!--{/if}-->
					</div>
				</div>
				<hr/>
				<div class="am-g" style="min-height: 360px;">
					<div class="am-u-sm-6">
						<p><b>处理记录</b></p>
						<table>
							<!--{loop $order_service_list $order_service_info}-->
							<tr>
								<td  style="min-width: 80px;">
									{$order_service_info->service_username}
								</td>
								<td>
									{$order_service_info->service_time}
								</td>
								<td>
									{$order_service_info->service_note}
								</td>
								
							</tr>
							<!--{/loop}-->
						</table>
					</div>
				</div>

			</div>
		</div>
		<!-- content end -->
		</div>
		<!--{template inc/footer_paul}-->
	</body>
 
</html>
<script type="text/javascript" src="{$BASE_V}assets/js/jquery.min.js"></script>
<script type="text/javascript" src="{$BASE_V}assets/js/amazeui.js"></script>
<script type="text/javascript" src="{$BASE_V}js/common.js"></script>
<script src="{STATIC_URL}js/jquery-plugin/ui/minified/jquery.cookie-min.js" type="text/javascript"></script>
<script type="text/javascript" src="{$BASE_V}js/modal_html.js"></script>
<script type="text/javascript" src="{$BASE_V}js/order/refund_detail.js"></script>
<script>
	var php_self = '{PHP_SELF}';
	var order_refund_id = '{$order_refund_info->supplier_mobile}';	
</script>