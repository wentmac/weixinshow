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
		<meta name="save" content="history">
		<link href="{STATIC_URL}common/assets/css/amazeui.css" rel="stylesheet" type="text/css">
		<link href="{STATIC_URL}common/assets/css/admin.css" rel="stylesheet" type="text/css">
		<link href="{$BASE_V}css/base.css" type="text/css" rel="stylesheet">
		<link href="{$BASE_V}css/page.css" type="text/css" rel="stylesheet">
		<link href="{$BASE_V}css/form_list.css" type="text/css" rel="stylesheet">
	</head>

	<body>
		<!--{template inc/header_paul}-->
		<div class="am-cf admin-main" id="condition_list">
			<!--{template inc/sidebar_paul}-->
			<!-- content start -->
			<div class="admin-content">
				<div class="am-cf am-padding">
					<div class="am-fl"><strong class="am-text-primary am-text-lg">待发货订单商品管理</strong>
					</div>
				</div>			
				<hr>

				<div class="am-g">
					<div class="am-u-lg-12">
						<table class="am-table am-table-striped am-table-hover table-main">
							<thead>
								<tr>									
									<th width="5%">商品ID</th>									
									<th width="50%">商品名称</th>
									<th width="25%">商品规格</th>									
									<th width="15%">商品类型</th>																											
									<th width="10%">待发货总数</th>
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
							<tbody id="tbody_order_list">

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
	var mobile_url = '{MOBILE_URL}';
	var static_url = '{STATIC_URL}';
	var base_v = '{$BASE_V}';
	var php_self = '{PHP_SELF}';
	var searchParameter = $searchParameter;
</script>
<script type="text/javascript" src="{STATIC_URL}common/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="{STATIC_URL}common/assets/js/amazeui.js"></script>
<script type="text/javascript" src="{STATIC_URL}js/jquery-plugin/jquery.pagination-min.js"></script>
<script type="text/javascript" src="{STATIC_URL}common/assets/js/app.js"></script>
<script src="{STATIC_URL}js/jquery-plugin/ui/minified/jquery.cookie-min.js" type="text/javascript"></script>
<script type="text/javascript" src="{$BASE_V}js/common.js"></script>
<script type="text/javascript" src="{$BASE_V}js/modal_html.js"></script>
<script type="text/javascript" src="{$BASE_V}js/order/un_shipped_list.js?v=2"></script>
<script language="javascript">
	$(document).ready(function() {
		//search.bindParam();
		search.getOrderList();
	});
</script>