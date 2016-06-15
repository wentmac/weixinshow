<!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>统计</title>
		<meta name="description" content="银品惠">
		<meta name="keywords" content="银品惠">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<meta name="renderer" content="webkit">
		<meta http-equiv="Cache-Control" content="no-siteapp" />
		<link rel="apple-touch-icon-precomposed" href="/i/app-icon72x72@2x.png">
		<meta name="apple-mobile-web-app-title" content="银品惠" />
		<link href="{$BASE_V}assets/css/amazeui.css" rel="stylesheet" type="text/css">
		<link href="{$BASE_V}assets/css/admin.css" rel="stylesheet" type="text/css">
		<link href="{$BASE_V}css/base.css" type="text/css" rel="stylesheet">
		<link href="{$BASE_V}css/amazeui.switch.css" rel="stylesheet" type="text/css">
		<link href="{$BASE_V}css/app.css" rel="stylesheet" type="text/css">
		<link href="{$BASE_V}css/shop_set.css?v=0.6" rel="stylesheet" type="text/css">
	</head>

	<body style="background-color: #f6f6f6">
		<!--{template inc/header_paul}-->
		<div class="am-cf admin-main">
			<!--{template inc/sidebar_paul}-->
			<div class="admin-content">
				<div class="am-cf am-padding">
					<div class="am-fl"><strong class="am-text-primary am-text-lg">统计数据</strong>
					</div>
				</div>
				<hr/>
				<div class="am-btn-group am-u-sm-12" id="statistics_group">
					<a href="{MOBILE_URL}{PHP_SELF}?m=statistics.detail&type=order" id="a_order" class="am-btn am-btn-default am-radius">订单统计</a>
					<a href="{MOBILE_URL}{PHP_SELF}?m=statistics.detail&type=bill" id="a_bill" class="am-btn am-btn-default am-radius">收入统计</a>
					<!--{if $is_supplier==false}-->
					<a href="{MOBILE_URL}{PHP_SELF}?m=statistics.detail&type=collect" id="a_collect" class="am-btn am-btn-default am-radius">收藏统计</a>
					<!--{/if}-->
				</div>

			</div>

			<div class="admin-content">
				<hr/>
				<div class="am-container" style="background: #fff;">
					<div class="am-text-center"><font class="f_type"></font></div>
					<div id="echart_main" style="width:98%;height:200px;">

					</div>

				</div>
				<div class="am-container am-margin-top-sm" style="background:#fff">
					<table class="am-table">
						<thead>
							<tr>
								<th>日期</th>
								<th class="am-text-right"><font class="f_type"></font></th>
							</tr>
						</thead>
						<tbody id="tbody_html">

						</tbody>
					</table>
				</div>
			</div>

	</body>

</html>
<script>
	var index_url = '{MOBILE_URL}';
	var static_url = '{STATIC_URL}';
	var base_v = '{$BASE_V}';
	var php_self = '{PHP_SELF}';
	var week_array = $week_array;
	var type = '{$type}';
</script>
<script type="text/javascript" src="{$BASE_V}assets/js/jquery.min.js"></script>
<script type="text/javascript" src="{$BASE_V}assets/js/amazeui.min.js"></script>
<script type="text/javascript" src="{$BASE_V}js/amazeui.switch.min.js"></script>
<script type="text/javascript" src="{$BASE_V}assets/echarts/echarts-all.js"></script>

<script type="text/javascript" src="cordova.js"></script>
<script type="text/javascript" src="{$BASE_V}js/index.js"></script>
<script type="text/javascript" src="{$BASE_V}js/statistics_detail.js"></script>
<script type="text/javascript" src="{$BASE_V}js/common.js"></script>
<script src="{STATIC_URL}js/jquery-plugin/ui/minified/jquery.cookie-min.js" type="text/javascript"></script>