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
		<link href="{$BASE_V}css/form_list.css" type="text/css" rel="stylesheet">
	</head>
	<body>
		<!--{template inc/header_paul}-->
		<div class="am-cf admin-main">
			<!--{template inc/sidebar_paul}-->
			<!-- content start -->
			<div class="admin-content">
				<div class="am-cf am-padding">
					<div class="am-fl"><strong class="am-text-primary am-text-lg">订单管理</strong>
					</div>
				</div>
				<hr/>
				<div class="am-g">
					<div class="am-u-sm-7">
						<div class="am-btn-group" id="btns_list">
							<button id="all" data_id="all" type="button" class="am-btn am-btn-primary am-radius">全部 
							</button>
							<button id="seller_confirm" data_id="seller_confirm" type="button" class="am-btn am-btn-default am-radius">等待卖家处理</button>
							<button id="buyer_confirm" data_id="buyer_confirm" type="button" class="am-btn am-btn-default am-radius">等待买家处理</button>
							<button id="customer_confirm" data_id="customer_confirm" type="button" class="am-btn am-btn-default am-radius">等待银品惠客服介入</button>
							<button id="complete" data_id="complete" type="button" class="am-btn am-btn-default am-radius">同意退款</button>
							<button id="close" data_id="close" type="button" class="am-btn am-btn-default am-radius">撤销维权</button>
							
						</div>
					</div>
					<div class="am-u-sm-5">
						<div class="am-input-group">
							<span class="am-input-group-label am-radius"><i class="am-icon-search am-icon-fw"></i></span>
							<input id="txt_keyword" type="text" class="am-form-field am-radius" placeholder="输入手机号、订单号或买家姓名……">
							<span class="am-input-group-btn"><button class="am-btn am-btn-default" type="button">搜索</button></span>
						</div>
					</div>
				</div>
				<hr>
				<div class="am-g">
					<div class="am-u-lg-12">
						<table class="am-table am-table-striped am-table-hover table-main">
							<thead>
								<tr>
									<th>产品信息</th>
									<th width="5%">会员UID</th>
									<th width="6%">收货人</th>
									<th width="10%">订单编号</th>
									<th width="5%">数量</th>
									<th width="5%">佣金</th>
									<th width="8%">退款金额</th>
									<th width="12%">状态</th>
									<th width="8%">操作</th>
								</tr>
							</thead>
							<tbody  id="order_list_loading">
								<tr>
									<td colspan="9" class="am-text-center">
										<div class="am-modal-hd am-text-center"><img  src="{$BASE_V}image/loading.gif">正在载入...</div>
									</td>
								</tr>

							</tbody>
							<tbody style="display: none;" id="order_list_nofund">
								<tr>
									<td class="am-text-center" colspan="8">
										<div class="am-modal-hd">很抱歉，没有找到结果...</div>
									</td>
								</tr>
							</tbody>				
							<tbody id="tbody_refund_order_list">
								
							</tbody>
							<tfoot>
								<tr>
									<td colspan="11" id="roomListPages" class="am-text-center page pagination"></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
				<hr/>
			</div>
			
			<!-- content end -->
		</div>
		<!--{template inc/footer_paul}-->
	</body>
</html>
<script type="text/javascript">
			var index_url = '{MOBILE_URL}';
			var static_url = '{STATIC_URL}';
			var base_v = '{$BASE_V}';
			var php_self = '{PHP_SELF}';
			var mobile_url = '{MOBILE_URL}';
			var param={
				status:'{$status}',
				pagesize:'{$status}',
				query:'{$query}'
				};
			
		</script>
		<script type="text/javascript" src="{$BASE_V}assets/js/jquery.min.js"></script>
		<script type="text/javascript" src="{$BASE_V}assets/js/amazeui.js"></script>
		<script type="text/javascript" src="{STATIC_URL}js/jquery-plugin/jquery.pagination-min.js"></script>
		<script type="text/javascript" src="{$BASE_V}assets/js/app.js"></script>
		<script type="text/javascript" src="{$BASE_V}js/common.js"></script>
		<script src="{STATIC_URL}js/jquery-plugin/ui/minified/jquery.cookie-min.js" type="text/javascript"></script>
		<script type="text/javascript" src="{$BASE_V}js/modal_html.js"></script>
		<script type="text/javascript" src="{$BASE_V}js/order/refund_list.js"></script>
<script language="javascript">
	$(document).ready(function() {
		data_builder.bindParam();
		data_builder.get_order_list(param);
	});
</script>