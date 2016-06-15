<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\order/refund_detail.tpl', 'D:\Web\Work\www.090.cn\trunk\admin\application\View\default\order\refund_detail.tpl', 1444390384)
;?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="<?php echo $BASE_V;?>layout.css" rel="stylesheet" type="text/css" />
<link href="<?php echo STATIC_URL; echo APP_MANAGE_NAME; ?>/default/css/base.css" type="text/css" rel="stylesheet">
<link href="<?php echo STATIC_URL; echo APP_MANAGE_NAME; ?>/default/v1/css/page.css" type="text/css" rel="stylesheet">
<title>TBlog博客系统</title>
<script src="<?php echo STATIC_URL; ?>js/tools.js" type="text/javascript"></script>
</head>
<body>

<div style="z-index: 1; right: 20px; top: 30px; color: rgb(255, 255, 255); position: absolute; display: none;" id="loading"><img src="<?php echo $BASE_V;?>images/loader.gif"></div>

<div id="main">
  <div class="main_box">        
			<div class="admin-content">
				<div class="am-cf am-padding">
					<div class="am-fl"><strong class="am-text-primary am-text-lg">退款详情-<?php echo $order_refund_info->service_note;?></strong></div>
				</div>
				<hr/>
				<div class="am-g">
					<div class="am-u-sm-6">
						订单编号：<?php echo $order_refund_info->order_sn;?>
						<br> 买家：<?php echo $order_refund_info->consignee;?>
						<br> 申请时间：<?php echo $order_refund_info->refund_time;?>
						<br> 申请类型：<?php echo $order_refund_info->refund_service_status;?>
						<br> 申请原因：<?php echo $order_refund_info->refund_service_reason;?>
						<br> 退款金额：<?php echo $order_refund_info->money;?>
						<br> 描述：<?php echo $order_refund_info->refund_note;?>
						<br> 图片举证：
						<ul data-am-widget="gallery" class="am-gallery am-avg-sm-3 am-gallery-default" data-am-gallery="{ pureview: true }">
							<?php if(is_array($order_refund_info->refund_images)) foreach($order_refund_info->refund_images AS $refund_images_url) { ?>
							<li>
								<div class="am-gallery-item">
									<a class="">
										<img src="<?php echo $refund_images_url;?>">
									</a>
								</div>
							</li>
							<?php } ?>
						</ul>
					</div>
				</div>
				<hr/>
				<div class="am-g">
					<div class="am-u-sm-6">
						供应商：<a href="<?php echo INDEX_URL; ?>manage.php?m=order.refund_detail&order_refund_id=<?php echo $order_refund_info->order_refund_id;?>&other_uid=<?php echo $order_refund_info->goods_uid;?>"  target="_blank" title="在供应商<<?php echo $order_refund_info->supplier_mobile;?>>的管理中心中查看此订单" class="a_underline"><?php echo $order_refund_info->supplier_mobile;?></a>
					</div>
				</div>
				<hr/>
				<div class="am-g" style="min-height: 360px;">
					<div class="am-u-sm-6">
						<p><b>处理记录</b></p>
						<table>
							<?php if(is_array($order_service_list)) foreach($order_service_list AS $order_service_info) { ?>
							<tr>
								<td  style="min-width: 80px;">
									<?php echo $order_service_info->service_username;?>
								</td>
								<td>
									<?php echo $order_service_info->service_time;?>
								</td>
								<td>
									<?php echo $order_service_info->service_note;?>
								</td>
								
							</tr>
							<?php } ?>
						</table>
					</div>
				</div>

			</div>
	</div>
</div>
</body>
</html>
<script src="<?php echo STATIC_URL; ?>js/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script language="javascript">
var mobile_url = '<?php echo MOBILE_URL; ?>';
var php_self = '<?php echo PHP_SELF; ?>';
jq = jQuery.noConflict(); 

jq(document).ready(function(){		
});
</script>