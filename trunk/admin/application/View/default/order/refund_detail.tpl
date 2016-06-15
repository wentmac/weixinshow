<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="{$BASE_V}layout.css" rel="stylesheet" type="text/css" />
<link href="{STATIC_URL}{APP_MANAGE_NAME}/default/css/base.css" type="text/css" rel="stylesheet">
<link href="{STATIC_URL}{APP_MANAGE_NAME}/default/v1/css/page.css" type="text/css" rel="stylesheet">
<title>TBlog博客系统</title>
<script type="text/javascript" src="{STATIC_URL}js/tools.js"></script>
</head>
<body>

<div style="z-index: 1; right: 20px; top: 30px; color: rgb(255, 255, 255); position: absolute; display: none;" id="loading"><img src="{$BASE_V}images/loader.gif"></div>

<div id="main">
  <div class="main_box">        
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
						供应商：<a href="{INDEX_URL}manage.php?m=order.refund_detail&order_refund_id={$order_refund_info->order_refund_id}&other_uid={$order_refund_info->goods_uid}"  target="_blank" title="在供应商<{$order_refund_info->supplier_mobile}>的管理中心中查看此订单" class="a_underline">{$order_refund_info->supplier_mobile}</a>
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
</div>
</body>
</html>
<script type="text/javascript" src="{STATIC_URL}js/jquery/1.7.2/jquery.min.js"></script>
<script language="javascript">
var mobile_url = '{MOBILE_URL}';
var php_self = '{PHP_SELF}';
jq = jQuery.noConflict(); 

jq(document).ready(function(){		
});
</script>