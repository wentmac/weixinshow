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
					<div class="am-fl"><strong class="am-text-primary am-text-lg">广告位管理</strong></div>
				</div>

				<div class="am-u-md-12">
					<div class="am-panel am-panel-default">
						<div class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-2'}"><font class="bill_type_info">商城广告位</font><span class="am-icon-chevron-down am-fr"></span></div>
						<div id="collapse-panel-2" class="am-in">
							<table class="am-table am-table-bd am-table-bdrs am-table-striped am-table-hover">
								<thead>
									<tr>
										<th>广告位</th>
										<th>自定义状态</th>										
										<th>操作</th>
									</tr>
								</thead>
								<tbody style="display: none;" id="bill_list_loading">
									<tr>
										<td colspan="3" class="am-text-center">
												<div class="am-modal-hd"><img  src="{$BASE_V}img/loading.gif">正在载入...</div>
										</td>
									</tr>

								</tbody>
								<tbody style="display:none">
									<tr>
										<td class="am-text-center" colspan="3">
												<div class="am-modal-hd">很抱歉，没有找到结果...</div>
										</td>
									</tr>
								</tbody>
								<tbody id="tbody_html">
								<!--{loop $poster_custom_array $poster_name $poster_title}-->
								  <tr>
									<td class="am-text-left">{$poster_title}</td>
									<td class="am-text-middle" id="{$poster_name}_show">
									<!--{if !empty($poster_array[$poster_name])}-->
									已经设置自定义广告位
									<!--{else}-->
									未设置自定义广告位
									<!--{/if}-->
									</td>		
									<td class="am-text-middle"><a href="{PHP_SELF}?m=poster.add&poster_name={$poster_name}">修改/设置</a> | <a class="delete_poster" href="javascript:void(0)" data-id="{$poster_name}">删除自定义广告</a></td>
								  </tr>
								<!--{/loop}-->
								</tbody>
								<tfoot>
								<tr>
									<td colspan="3" class="am-text-center page pagination"></td>
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
<script language="javascript">
$(document).ready(function(){
	$('#tbody_html .delete_poster').click(function(){
		var poster_title = $(this).attr('data-id');
		$.ajax({
			type: "POST",
			url: index_url + php_self + '?m=poster.batch',
			dataType: "json",
			data: {
				action: 'del',
				id: poster_title
			},
			cache: false,
			success: function(data) {
				//console.log(data);
				if (data.success == true) {
					$('#'+poster_title+'_show').text('未设置自定义广告位');
					M._alert('删除广告位成功');							
				} else {					
					M._alert(data.message);					
				}
			}
		});
	});
});
</script>