<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\order/detail.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\manage\application\View\default\order\detail.tpl', 1465815929)
|| self::check('default\order/detail.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\manage\application\View\default\inc/header_paul.tpl', 1465815929)
|| self::check('default\order/detail.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\manage\application\View\default\inc/sidebar_paul.tpl', 1465815929)
|| self::check('default\order/detail.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\manage\application\View\default\inc/footer_paul.tpl', 1465815929)
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
		<link href="<?php echo $BASE_V;?>css/order_detail.css" type="text/css" rel="stylesheet">
			
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
          <li><a href="<?php echo MOBILE_URL; echo PHP_SELF; ?>?m=order.un_shipped"><span class="am-icon-table am-icon-area-chart"></span> 待发货订单商品</a></li> 

          <li class="admin-parent">
            <a class="am-cf" data-am-collapse="{target: '#article-nav'}">
              <span class="am-icon-home am-icon-sm am-icon-fw"></span> 文章管理 <span class="am-icon-angle-right am-fr am-margin-right"></span>
            </a>
            
            <ul class="am-list am-collapse admin-sidebar-sub am-in" id="article-nav">
                <li><a href="<?php echo MOBILE_URL; echo PHP_SELF; ?>?m=archives.arclist" class="am-cf"><span class="am-icon-rmb am-icon-fw"></span> 文章列表</a></li>
                <li><a href="<?php echo MOBILE_URL; echo PHP_SELF; ?>?m=archives.add"><span class="am-icon-credit-card am-icon-fw"></span> 文章发布</a></li>                
            </ul>
           </li>
		  
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
					<div class="am-fl"><strong class="am-text-primary am-text-lg">订单详情</strong></div>
				</div>
				<hr/>
				<div class="am-g">
					<div class="am-u-sm-6">
						订单编号：<?php echo $order_info->order_sn;?>
						<br> 下单帐号：<?php echo $order_info->mobile;?>
						<br>
					</div>
				</div>
				<hr/>
				<div class="am-g">
					<div class="am-u-sm-6">
						<p><b>收货人信息</b></p>
						<br> ID：<?php echo $order_info->uid;?>
						<br> 昵称：<?php echo $order_info->nickname;?>
						<br> 姓名：<?php echo $order_info->consignee;?>
						<br> 手机：<?php echo $order_info->mobile;?>
						<br> 地址：<?php echo $order_info->full_address;?>
						<br> 备注：<?php echo $order_info->postscript;?>
						<br>
					</div>
				</div>
				<hr/>
				<div class="am-g">
					<div class="am-u-sm-6">
						<?php if($order_info->have_return_service == 0 && $order_info->refund_status > 0) { ?>
						<?php if($order_info->refund_status==1) { ?>
						<a class="am-btn am-btn-danger am-btn-xs am-round" href="<?php echo PHP_SELF; ?>?m=order.refund_detail&order_refund_id=<?php echo $order_info->order_refund_id;?>"><i class="am-icon-gears">退款中，点击处理退款</i></a>
						<?php } else { ?>
						<a class="am-btn am-btn-danger am-btn-xs am-round" href="<?php echo PHP_SELF; ?>?m=order.refund_detail&order_refund_id=<?php echo $order_info->order_refund_id;?>"><i class="am-icon-gears">有退款，点击查看退款详情</i></a>
						<?php } ?>
						<?php } ?>

						<?php if($order_info->order_status==2) { ?>
						<?php if($order_info->supplier_status==true) { ?>
						<a id="delivery" class="am-btn am-btn-danger am-btn-xs am-round"><em class="am-icon-truck"></em>  发    货     </a>
						<?php } else { ?>
						<a><i class="am-icon-gears">联系供应商:<?php echo $order_info->supplier_mobile;?></i></a>
						<?php } ?>
						<?php } ?>
					</div>
				</div>
				<div class="am-g">
					<div id="div_delivery_list" class="div_delivery_list" style="display: none;">
						<div>
							<h2><b>请填写发货信息</b><a class="close" id="close"><em class="am-icon-close"></em></a></h2>
							<p>
								<a id="need_delivery" >需要发货 <em class="am-icon-check-circle red"></em></a>
								<!--<a id="need_delivery_no">无需发货<em class="am-icon-circle-thin red"></em></a>-->
							</p>
							<ul>
								<li>
									<input type="text" placeholder="请填写快递单号" id="fedexNum"><span class="hide"><em></em></span>
								</li>
							</ul>
							<ul id="delivery_list" class="delivery_list">

							</ul>
							
							<input type="text" placeholder="自己填写快递公司" id="fedexName"><span class="hide"><em></em></span>
							<input type="hidden" id="hid_express" data_id="" data_name="" />
							<br>
								<br>
							<h2><a id="btn_express" style="color:#fff;"> 提 交 </a></h2>
						</div>
					</div>
				</div>
				<div class="am-g">
					<div id="order_delivery_list" class="am-u-sm-11 am-u-sm-centered div_delivery_list" style="display: none;">
						<div>
							<h2>同一地址的【待发货】订单[合并发货]<a class="close" id="order_close"><em class="am-icon-close"></em></a></h2>		
							<table class="am-table am-table-striped am-table-hover table-main">
								<thead>
									<tr>
										<th class="am-text-left" width="50%">产品</th>									
										<th width="8%">收货人</th>
										<th width="20%">地址</th>
										<th width="5%">订单编号</th>
										<th width="5%">数量</th>																																							
										<th width="8%">操作</th>
									</tr>
								</thead>
								<tbody  id="order_list_loading">
									<tr>
										<td colspan="11" class="am-text-center">
											<div class="am-modal-hd am-text-center"><img  src="<?php echo $BASE_V;?>image/loading.gif">正在载入...</div>
										</td>
									</tr>

								</tbody>
								<tbody style="display: none;" id="order_list_nofund">
									<tr>
										<td class="am-text-center" colspan="10">
													<div class="am-modal-hd">很抱歉，没有找到结果...</div>
											</td>
										
									</tr>

								</tbody>							

								<tbody id="tbody_order_list"></tbody>	

						</table>					
							<ul>
								<li>
									  <div class="am-text-right"><input id="checkAll" type="checkbox"/><label for="checkAll">全选</label></div>									
								</li>
							</ul>													
							<h2><a id="merge_delivery" style="color:#fff;"> 合并发货 </a></h2>
						</div>
					</div>

					<div class="am-modal am-modal-confirm" tabindex="-1" id="my-confirm">
					  <div class="am-modal-dialog">
					    <div class="am-modal-hd">订单合并发货确认</div>
					    <div class="am-modal-bd" id="merge_delivery_text">
					      
					    </div>
					    <div class="am-modal-footer">
					      <span class="am-modal-btn" data-am-modal-cancel>取消</span>
					      <span class="am-modal-btn" data-am-modal-confirm>确定</span>
					    </div>
					  </div>
					</div>
				</div>		
				<div class="am-g">
					<div class="am-u-sm-6">
						<p><b>商品信息</b></p>
						<?php if(is_array($order_info->order_goods_array)) foreach($order_info->order_goods_array AS $v) { ?>
						<div>
							
							<p style="height: 110px; line-height: 30px;">
								
								<img src="<?php echo $v->goods_image_url;?>" style="float: left;">
								
								<a>&nbsp;&nbsp;<?php echo $v->item_name;?></a>
								<a>&nbsp;<?php echo $v->goods_sku_name;?></a>
								<br> &nbsp;&nbsp;价格：￥<?php echo $v->item_price;?>*<?php echo $v->item_number;?><?php if(!empty($v->item_total_price) && $v->item_price<>$v->item_total_price) { ?><del>￥<?php echo $v->item_total_price;?></del><?php } ?>
								<?php if($v->service_status > 0) { ?>
								<br>
									<a style="color:red; text-decoration: underline; margin-left: 10px;" href="<?php echo PHP_SELF; ?>?m=order.refund_detail&order_refund_id=<?php echo $v->order_refund_id;?>"><?php echo $v->service_status_text;?><font style="color: #434343;">点击查看详细</font></a>
									
								<?php } ?>
							</p>
							<hr>
						</div>
						<?php } ?>
						<br> 商品总数：<?php echo $order_info->order_item_count;?>
						<br> 邮费：￥<?php echo $order_info->shipping_fee;?>								
						<?php if(!empty($order_info->coupon_code)) { ?>						
						<br> 订单总价：￥<?php echo $order_info->order_payable_amount+$order_info->coupon_money; if(!empty($order_info->order_integral_amount)) { ?>						
						<br> 积分付款：￥<?php echo $order_info->order_payable_amount;?>
						<?php } ?>
						<br> 实际付款：￥<?php echo $order_info->order_amount;?>
						<br> <font color="red">代金券付款：￥<?php echo $order_info->coupon_money;?></font>
						<br> <font color="red">代金券：<?php echo $order_info->coupon_code;?></font>
						<?php } else { ?>
						<br> 订单总价：<del>￥<?php echo $order_info->order_total_price;?></del>
						<br> 实际付款：￥<?php echo $order_info->order_amount;?>
						<?php } ?>
						<?php if(!empty($order_info->agent_uid)) { ?>
						<br> 直推佣金：-￥<?php echo $order_info->commission_fee;?> 给了【<?php echo $order_info->agent_uid;?>】								
						<?php } ?>
						<?php if(!empty($order_info->rank_uid)) { ?>
						<br> 排位佣金：-￥<?php echo $order_info->commission_fee_rank;?> 给了【<?php echo $order_info->rank_uid;?>】
						<?php } ?>
						<br> 供应商收入：<font color="red">￥<?php echo $order_info->supplier_amount;?></font>
						<br>
					</div>
				</div>
				<hr/>
				<div class="am-g">
					<div class="am-u-sm-6">
						<p><b>订单信息</b></p>
						订单编号：<?php echo $order_info->order_sn;?>
						<br> 下单时间：<?php echo $order_info->create_time;?>
						<br> 付款时间：<?php echo $order_info->pay_time;?>
						<br> 确认收货：<?php echo $order_info->confirm_time;?>
						<br>
					</div>
				</div>
				<hr/>
				<div class="am-g">
					<div class="am-u-sm-6">
						<p><b>物流信息</b></p>
						物流公司：<a id="a_express_name" data-id="<?php echo $order_info->express_id;?>"><?php echo $order_info->express_name;?></a>
						<br> 物流单号：<a id="a_express_no" data-no="<?php echo $order_info->express_no;?>"><?php echo $order_info->express_no;?></a>
						<br> 物流跟踪：<a id="a_express" style="color:red; text-decoration: underline;" class="am-icon-paper-plane-o">点击查看</a>
						<br>
					</div>
				</div>
				<hr/>
				<div class="am-g">
					<ul id="div_express" class="am-u-sm-6 div_express">

					</ul>
				</div>

				<hr/>
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
			<div class="am-modal-bd" id="confirm_content">
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

<script>
	var php_self = '<?php echo PHP_SELF; ?>';
	var order_id='<?php echo $order_info->order_id;?>';
	var address_id='<?php echo $order_info->address_id;?>';
	var mobile_url = '<?php echo MOBILE_URL; ?>';
	var order_id_string = '';
</script>
<script src="<?php echo $BASE_V;?>js/order/detail.js?v=20151201" type="text/javascript"></script>