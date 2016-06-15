<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\order/refund_detail.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\manage\application\View\default\order\refund_detail.tpl', 1461999302)
|| self::check('default\order/refund_detail.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\manage\application\View\default\inc/header_paul.tpl', 1461999302)
|| self::check('default\order/refund_detail.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\manage\application\View\default\inc/sidebar_paul.tpl', 1461999302)
|| self::check('default\order/refund_detail.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\manage\application\View\default\inc/footer_paul.tpl', 1461999302)
;?>
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
		<link href="<?php echo $BASE_V;?>assets/css/amazeui.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $BASE_V;?>assets/css/admin.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $BASE_V;?>css/base.css" type="text/css" rel="stylesheet">
		<link href="<?php echo $BASE_V;?>css/page.css" type="text/css" rel="stylesheet">
			<style>
				td{ min-width:180px; text-align: left; border-bottom:1px solid #f1f1f1 ;}
				
			</style>
	</head>

	<body>
<!-- header start -->
  <header class="am-topbar admin-header">
    <div class="am-topbar-brand">
      <img src="<?php echo $BASE_V;?>image/logo-blue.png">用户中心
    </div>
    <button class="am-topbar-btn am-topbar-toggle am-btn am-btn-sm am-btn-success am-show-sm-only" data-am-collapse="{target: '#topbar-collapse'}"><span class="am-sr-only">导航切换</span> <span class="am-icon-bars"></span></button>
    <div class="am-collapse am-topbar-collapse" id="topbar-collapse">
      <ul class="am-nav am-nav-pills am-topbar-nav am-topbar-right admin-header-list">
       <!-- <li><a href="javascript:;"><span class="am-icon-envelope-o"></span> 收件箱 <span class="am-badge am-badge-warning">5</span></a></li>-->
        <li class="am-dropdown" data-am-dropdown>
          <a class="am-dropdown-toggle" data-am-dropdown-toggle href="javascript:;">
            <span class="am-icon-users"></span> [<?php echo $memberInfo->mobile;?>]我的银品惠 <span class="am-icon-caret-down"><?php if($memberSettingInfo->security_deposit>0 && $memberInfo->member_type==2) { ?>保证金：￥<?php echo $memberSettingInfo->security_deposit;?><?php } else { echo $member_type_class_text;?><?php } ?></span>
          </a>
          <ul class="am-dropdown-content">
            <!--<li><a href="#"><span class="am-icon-user"></span> 资料</a></li>
            <li><a href="#"><span class="am-icon-cog"></span> 设置</a></li>-->
            <li><a href="<?php echo MOBILE_URL; echo PHP_SELF; ?>?m=account.loginout"><span class="am-icon-power-off"></span> 退出</a></li>
          </ul>
        </li>
        <li class="am-hide-sm-only"><a href="javascript:;" id="admin-fullscreen"><span class="am-icon-arrows-alt"></span> <span class="admin-fullText">开启全屏</span></a></li>
      </ul>
    </div>
  </header>
  <!-- header end --><div class="am-cf admin-main">    <style type="text/css">
    	.closed{
    		width: auto;
    	} 	
    	.closed #slider_bar{
    		display: inline;
    	}
    	#open_menu{display: none;}
    </style>
    
    	
    <!-- sidebar start -->
    <div class="admin-sidebar am-offcanvas closed" id="admin-offcanvas">
    	<div id="open_menu"  style="background-color: #fff;">
    		<a class="am-btn am-padding-sm am-btn-success" href="#" title="展开菜单">
    			<i class="am-icon-fw am-icon-angle-double-right"></i></a>
    	</div>
      <div id="slider_bar" class="am-offcanvas-bar admin-offcanvas-bar">
        <ul class="am-list admin-sidebar-list">
          <li><a href="#" id="close_menu"><span class="am-icon-fw am-icon-list-ul am-text-warning"></span><span class="am-text-warning">收起菜单</span><span class="am-icon-angle-double-left am-text-warning am-icon-fw am-fr am-margin-right-sm"></span></li></a>
          <li class="admin-parent">
            <a class="am-cf" data-am-collapse="{target: '#collapse-nav'}"><span class="am-icon-home am-icon-sm am-icon-fw"></span> 我的银品惠 <span class="am-icon-angle-right am-fr am-margin-right"></span></a>
            <ul class="am-list am-collapse admin-sidebar-sub am-in" id="collapse-nav">
			  <li><a href="<?php echo MOBILE_URL; echo PHP_SELF; ?>?m=bill.home" class="am-cf"><span class="am-icon-rmb am-icon-fw"></span> 账户中心</a></li>
			  <li><a href="<?php echo MOBILE_URL; echo PHP_SELF; ?>?m=settle.apply"><span class="am-icon-credit-card am-icon-fw"></span> 我要提现</a></li>
			  <li><a href="<?php echo MOBILE_URL; echo PHP_SELF; ?>?m=shop.detail" class="am-cf"><span class="am-icon-gears am-icon-fw"></span> 店铺设置</a></li>			  			   
        <li><a href="<?php echo MOBILE_URL; echo PHP_SELF; ?>?m=goods.index" class="am-cf"><span class="am-icon-list am-icon-fw"></span> 商品管理<span class="am-badge am-badge-secondary am-margin-right am-fr"> </span></a></li>			  			  			  			  
        <li><a href="<?php echo MOBILE_URL; echo PHP_SELF; ?>?m=poster"><span class="am-icon-puzzle-piece am-icon-fw"></span> 广告位管理</a></li>
            </ul>
          </li>
          <li><a href="<?php echo MOBILE_URL; echo PHP_SELF; ?>?m=order.index"><span class="am-icon-table am-icon-fw"></span> 订单管理</a></li>
          <li><a href="<?php echo MOBILE_URL; echo PHP_SELF; ?>?m=order.refund"><span class="am-icon-table am-icon-area-chart"></span> 维权订单</a></li>          
		  
		  <li><a href="<?php echo MOBILE_URL; echo PHP_SELF; ?>?m=statistics.detail&type=order"><span class="am-icon-table am-icon-pie-chart"></span> 统计  </a></li>
										
		  
			<li><a href="<?php echo MOBILE_URL; echo PHP_SELF; ?>?m=account.loginout"><span class="am-icon-sign-out am-icon-fw"></span> 注销</a></li>
        </ul>        
        <div class="am-panel am-panel-default admin-sidebar-panel">
          <div class="am-panel-bd">
            <p><span class="am-icon-tag"></span> 最新消息</p>
            <p>云端产品库已经发布！
              <br></p>
          </div>
        </div>
      </div>
    </div>
    <!-- sidebar end --><!-- content start -->
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
						<?php if($order_refund_info->service_status == 1 && $order_refund_info->refund_status == 1) { ?>
						<?php if($order_refund_info->supplier_status == true) { ?>
						<p><a id="agree_refund" data_refund_id="<?php echo $order_refund_info->order_refund_id;?>" class="am-btn am-btn-success">同意退款</a>
							<a id="refused_refund" data_refund_id="<?php echo $order_refund_info->order_refund_id;?>" class="am-btn am-btn-danger">拒绝退款</a>
						</p>
						<?php } else { ?>
						<a>联系供应商:<?php echo $order_refund_info->supplier_mobile;?></a>
						<?php } ?>
						<?php } ?>
						<?php if($order_refund_info->service_status == 1 &&  $order_refund_info->refund_status == 2 &&  $order_refund_info->return_status == 2) { ?>
						<?php if($order_refund_info->supplier_status == true) { ?>
						<p><a id="goods_ok" class="am-btn am-btn-success">收到退货</a><a id="goods_no" class="am-btn am-btn-danger">没有收到退货</a></p>
						<?php } else { ?>
						<a>联系供应商:<?php echo $order_refund_info->supplier_mobile;?></a>
						<?php } ?>
						<?php } ?>
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
		<!-- content end -->
		</div><footer>
	<div class="am-modal am-modal-prompt" tabindex="-1" id="my-prompt">
		<div class="am-modal-dialog">
			<div class="am-modal-hd">银品惠提醒</div>
			<div class="am-modal-bd">
				来来来，吐槽点啥吧
			</div>
			<input type="text" class="am-modal-prompt-input">
			<hr>	
			<div class="am-modal-footer">
				
				<span class="am-modal-btn" data-am-modal-cancel>取消</span>
				<span class="am-modal-btn" data-am-modal-confirm>提交</span>
			</div>
		</div>
	</div>

	<div class="am-modal am-modal-confirm" tabindex="-1" id="my-confirm">
		<div class="am-modal-dialog">
			<div class="am-modal-hd">银品惠提醒</div>
			<div class="am-modal-bd">
				你，确定要删除这条记录吗？
			</div>
			<div class="am-modal-footer">
				<span class="am-modal-btn" data-am-modal-cancel>取消</span>
				<span class="am-modal-btn" data-am-modal-confirm>确定</span>
			</div>
		</div>
	</div>
	<hr>
	<p class="am-padding-left am-text-center">© 2015 银品惠, Inc.</p>
</footer></body>
 
</html>
<script src="<?php echo $BASE_V;?>assets/js/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo $BASE_V;?>assets/js/amazeui.js" type="text/javascript"></script>
<script src="<?php echo $BASE_V;?>js/common.js" type="text/javascript"></script>
<script src="<?php echo STATIC_URL; ?>js/jquery-plugin/ui/minified/jquery.cookie-min.js" type="text/javascript"></script>
<script src="<?php echo $BASE_V;?>js/modal_html.js" type="text/javascript"></script>
<script src="<?php echo $BASE_V;?>js/order/refund_detail.js" type="text/javascript"></script>
<script>
	var php_self = '<?php echo PHP_SELF; ?>';
	var order_refund_id = '<?php echo $order_refund_info->supplier_mobile;?>';	
</script>