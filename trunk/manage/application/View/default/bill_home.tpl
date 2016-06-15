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
					<div class="am-fl"><strong class="am-text-primary am-text-lg">收入明细</strong></div>
					<div class="am-dropdown am-fr" data-am-dropdown="">
						<button class="am-btn am-btn-primary am-btn-xs am-dropdown-toggle" data-am-dropdown-toggle="">
							<span class="am-icon-navicon am-margin-right-xs"></span><font class="bill_type_info">查看账单</font> <span class="am-icon-caret-down"></span>
						</button>
						<ul class="am-dropdown-content" id="nav_show">
							<!--{loop $bill_type_array $bill_type}-->
							<li><a data_key="{$bill_type[key]}">{$bill_type[value]}</a></li>
							<!--{/loop}-->
						</ul>
					</div>
				</div>
				<ul class="am-avg-sm-1 am-avg-md-4 am-margin am-padding am-text-center admin-content-list ">
					<li><a href="#" class="am-text-secondary"><span class="am-icon-btn am-icon-credit-card"></span><br>账户余额<br>￥{$current_money}</a></li>
					<li><a href="#" class="am-text-success"><span class="am-icon-btn am-icon-bar-chart"></span><br>待确认<br>￥{$waiting_confirm}</a></li>
					<li><a href="#" class="am-text-warning"><span class="am-icon-btn am-icon-bookmark"></span><br>提现中<br>￥{$expense_withdrawals_ing}</a></li>
					<li><a href="#" class="am-text-danger"><span class="am-icon-btn am-icon-cubes"></span><br>已提现<br>￥{$expense_withdrawals_success}</a></li>
				</ul>
				<ul class="am-avg-sm-1 am-avg-md-4 am-margin am-padding am-text-center admin-content-list ">
					<li><a href="#" class="am-text-secondary"><span class="am-icon-btn am-icon-history"></span><br>累计收入<br>￥{$history_money}</a></li>
					<li><a href="#" class="am-text-success"><span class="am-icon-btn am-icon-database"></span><br>自营收入<br>￥{$income_business}</a></li>
					<li><a href="#" class="am-text-warning"><span class="am-icon-btn am-icon-briefcase"></span><br>代销收入<br>￥{$income_wholesale}</a></li>
					<li><a href="#" class="am-text-danger"><span class="am-icon-btn am-icon-fax"></span><br>直接收款<br>￥{$income_receivable}</a></li>
				</ul>

				<div class="am-u-md-12">
					<div class="am-panel am-panel-default">
						<div class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-2'}"><font class="bill_type_info">全部账单</font><span class="am-icon-chevron-down am-fr"></span></div>
						<div id="collapse-panel-2" class="am-in">
							<table class="am-table am-table-bd am-table-bdrs am-table-striped am-table-hover">
								<thead>
									<tr>
										<th>商品图片</th>
										<th>描述</th>
										<th>时间</th>
										<th>价格</th>
										<th>状态</th>
										<th>操作</th>
									</tr>
								</thead>
								<tbody style="display: none;" id="bill_list_loading">
									<tr>
										<td colspan="6" class="am-text-center">
												<div class="am-modal-hd"><img  src="{$BASE_V}image/loading.gif">正在载入...</div>
										</td>
									</tr>

								</tbody>
								<tbody  style="display: none;" id="bill_list_nofund">
									<tr>
										<td class="am-text-center" colspan="6">
												<div class="am-modal-hd">很抱歉，没有找到结果...</div>
										</td>
									</tr>
								</tbody>
								<tbody id="tbody_html">

								</tbody>
								<tfoot>
								<tr>
									<td colspan="6" id="roomListPages" class="am-text-center page pagination"></td>
								</tr>
							</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>

		</div>
		<a href="#" class="am-icon-btn am-icon-th-list am-show-sm-only admin-menu" data-am-offcanvas="{target: '#admin-offcanvas'}"></a>
		<!-- content end -->
		<!--{template inc/footer_paul}-->
	</body>

</html>
<script>
	var index_url = '{MOBILE_URL}';
	var static_url = '{STATIC_URL}';
	var base_v = '{$BASE_V}';
	var php_self = '{PHP_SELF}';
	var param = {
		status: '',
		pagesize: 6,
		page: 1,
		totalput: 0
	}
</script>
<script type="text/javascript" src="{$BASE_V}assets/js/jquery.min.js"></script>
<script type="text/javascript" src="{$BASE_V}assets/js/amazeui.js"></script>
<script type="text/javascript" src="{STATIC_URL}js/jquery-plugin/jquery.pagination-min.js"></script>
<script type="text/javascript" src="{$BASE_V}js/common.js"></script>
<script src="{STATIC_URL}js/jquery-plugin/ui/minified/jquery.cookie-min.js" type="text/javascript"></script>
<script type="text/javascript" src="{$BASE_V}js/modal_html.js"></script>

<script type="text/javascript" src="{$BASE_V}js/bill.js"></script>