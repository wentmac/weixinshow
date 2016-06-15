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
		<link href="{$BASE_V}css/order_detail.css" type="text/css" rel="stylesheet">
			
	</head>

	<body>
		<!--{template inc/header_paul}-->
		<div class="am-cf admin-main">
			<!--{template inc/sidebar_paul}-->
			<!-- content start -->
			<div class="admin-content">
				<div class="am-cf am-padding">
					<div class="am-fl"><strong class="am-text-primary am-text-lg">订单详情</strong></div>
				</div>
				<hr/>
				<div class="am-g">
					<div class="am-u-sm-6">
						订单编号：{$order_info->order_sn}
						<br> 下单帐号：{$order_info->mobile}
						<br>
					</div>
				</div>
				<hr/>
				<div class="am-g">
					<div class="am-u-sm-6">
						<p><b>收货人信息</b></p>
						<br> ID：{$order_info->uid}
						<br> 昵称：{$order_info->nickname}
						<br> 姓名：{$order_info->consignee}
						<br> 手机：{$order_info->mobile}
						<br> 地址：{$order_info->full_address}
						<br> 备注：{$order_info->postscript}
						<br>
					</div>
				</div>
				<hr/>
				<div class="am-g">
					<div class="am-u-sm-6">
						<!--{if $order_info->have_return_service == 0 && $order_info->refund_status > 0}-->
						<!--{if $order_info->refund_status==1}-->
						<a class="am-btn am-btn-danger am-btn-xs am-round" href="{PHP_SELF}?m=order.refund_detail&order_refund_id={$order_info->order_refund_id}"><i class="am-icon-gears">退款中，点击处理退款</i></a>
						<!--{else}-->
						<a class="am-btn am-btn-danger am-btn-xs am-round" href="{PHP_SELF}?m=order.refund_detail&order_refund_id={$order_info->order_refund_id}"><i class="am-icon-gears">有退款，点击查看退款详情</i></a>
						<!--{/if}-->
						<!--{/if}-->

						<!--{if $order_info->order_status==2}-->
						<!--{if $order_info->supplier_status==true}-->
						<a id="delivery" class="am-btn am-btn-danger am-btn-xs am-round"><em class="am-icon-truck"></em>  发    货     </a>
						<!--{else}-->
						<a><i class="am-icon-gears">联系供应商:{$order_info->supplier_mobile}</i></a>
						<!--{/if}-->
						<!--{/if}-->
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
											<div class="am-modal-hd am-text-center"><img  src="{$BASE_V}image/loading.gif">正在载入...</div>
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
						<!--{loop $order_info->order_goods_array $v}-->
						<div>
							
							<p style="height: 110px; line-height: 30px;">
								
								<img src="{$v->goods_image_url}" style="float: left;">
								
								<a>&nbsp;&nbsp;{$v->item_name}</a>
								<a>&nbsp;{$v->goods_sku_name}</a>
								<br> &nbsp;&nbsp;价格：￥{$v->item_price}*{$v->item_number}{if !empty($v->item_total_price) && $v->item_price<>$v->item_total_price}<del>￥{$v->item_total_price}</del>{/if}
								<!--{if $v->service_status > 0}-->
								<br>
									<a style="color:red; text-decoration: underline; margin-left: 10px;" href="{PHP_SELF}?m=order.refund_detail&order_refund_id={$v->order_refund_id}">{$v->service_status_text}<font style="color: #434343;">点击查看详细</font></a>
									
								<!--{/if}-->
							</p>
							<hr>
						</div>
						<!--{/loop}-->
						<br> 商品总数：{$order_info->order_item_count}
						<br> 邮费：￥{$order_info->shipping_fee}								
						<!--{if !empty($order_info->coupon_code)}-->						
						<br> 订单总价：￥${echo $order_info->order_payable_amount+$order_info->coupon_money;}
						<!--{if !empty($order_info->order_integral_amount)}-->						
						<br> 积分付款：￥{$order_info->order_payable_amount}
						<!--{/if}-->
						<br> 实际付款：￥{$order_info->order_amount}
						<br> <font color="red">代金券付款：￥{$order_info->coupon_money}</font>
						<br> <font color="red">代金券：{$order_info->coupon_code}</font>
						<!--{else}-->
						<br> 订单总价：<del>￥{$order_info->order_total_price}</del>
						<br> 实际付款：￥{$order_info->order_amount}
						<!--{/if}-->
						<!--{if !empty($order_info->agent_uid)}-->
						<br> 直推佣金：-￥{$order_info->commission_fee} 给了【{$order_info->agent_uid}】								
						<!--{/if}-->
						<!--{if !empty($order_info->rank_uid)}-->
						<br> 排位佣金：-￥{$order_info->commission_fee_rank} 给了【{$order_info->rank_uid}】
						<!--{/if}-->
						<br> 供应商收入：<font color="red">￥{$order_info->supplier_amount}</font>
						<br>
					</div>
				</div>
				<hr/>
				<div class="am-g">
					<div class="am-u-sm-6">
						<p><b>订单信息</b></p>
						订单编号：{$order_info->order_sn}
						<br> 下单时间：{$order_info->create_time}
						<br> 付款时间：{$order_info->pay_time}
						<br> 确认收货：{$order_info->confirm_time}
						<br>
					</div>
				</div>
				<hr/>
				<div class="am-g">
					<div class="am-u-sm-6">
						<p><b>物流信息</b></p>
						物流公司：<a id="a_express_name" data-id="{$order_info->express_id}">{$order_info->express_name}</a>
						<br> 物流单号：<a id="a_express_no" data-no="{$order_info->express_no}">{$order_info->express_no}</a>
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
		</div>
		<!--{template inc/footer_paul}-->
	</body>

</html>
<script type="text/javascript" src="{$BASE_V}assets/js/jquery.min.js"></script>
<script type="text/javascript" src="{$BASE_V}assets/js/amazeui.js"></script>
<script type="text/javascript" src="{$BASE_V}js/common.js"></script>
<script src="{STATIC_URL}js/jquery-plugin/ui/minified/jquery.cookie-min.js" type="text/javascript"></script>
<script type="text/javascript" src="{$BASE_V}js/modal_html.js"></script>

<script>
	var php_self = '{PHP_SELF}';
	var order_id='{$order_info->order_id}';
	var address_id='{$order_info->address_id}';
	var mobile_url = '{MOBILE_URL}';
	var order_id_string = '';
</script>
<script type="text/javascript" src="{$BASE_V}js/order/detail.js?v=20151201"></script>