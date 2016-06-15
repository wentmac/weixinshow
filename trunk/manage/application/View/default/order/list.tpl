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
		<div class="am-cf admin-main">
			<!--{template inc/sidebar_paul}-->
			<!-- content start -->
			<div class="admin-content">
				<div class="am-cf am-padding">
					<div class="am-fl"><strong class="am-text-primary am-text-lg">订单管理</strong>
					</div>
				</div>
				<hr/>
  

				<div class="am-g" id="condition_list">
					<div class="am-u-sm-7">
						<div class="am-btn-group">
							<a data_status="sort_addtime" class="am-btn am-btn-primary am-radius">全部</a>
							<a data_status="wating_seller_delivery" class="am-btn am-btn-default am-radius">待发货</a>
							<a data_status="waiting_payment" class="am-btn am-btn-default am-radius">待付款</a>
							<a data_status="wating_receiving" class="am-btn am-btn-default am-radius">已发货</a>
							<a data_status="refund" class="am-btn am-btn-default am-radius">退款中</a>
							<a data_status="complete" class="am-btn am-btn-default am-radius">已完成</a>
							<a data_status="close" class="am-btn am-btn-default am-radius">已关闭</a>
						</div>						
					</div>	
					<div class="am-u-sm-5">
						<div class="am-input-group am-input-group-lg">   						
							<span class="am-input-group-label am-radius">从第几页开始</span>
							<input id="export_start_page" name="export_start_page" type="text" class="am-form-field am-radius" value="1" placeholder="从第几页开始">
							<span class="am-input-group-label am-radius">导出的页数</span>
							<input id="export_end_page" name="export_end_page" type="text" class="am-form-field am-radius" value="10" placeholder="最多100页">
							<span class="am-input-group-btn">
        						<button class="am-btn am-btn-danger" type="button" id="export_button">导出excel</button>
     						</span>
						</div>						
					</div>														
				</div>

				<div class="am-g am-margin-top-sm am-form">		
					<div class="am-u-sm-12">									
						<div class="am-input-group">
							<div class="am-form-inline">
							  <div class="am-input-group">    
							    <input type="text" name="start_date" id="start_date" class="am-form-field" placeholder="开始时间" data-am-datepicker readonly required />
							  </div>

							  <div class="am-input-group">
							    <input type="text" name="end_date" id="end_date" class="am-form-field" placeholder="结束时间" data-am-datepicker readonly required />
							  </div>


							<div class="am-input-group am-margin-left-0">							      								  
							      <select id="query_id_type">				
							      	<option value="goods">商品ID</option>			        
							        <option value="member">用户UID</option>							        
							      </select>
							      <span class="am-form-caret"></span>							
							</div>
							<div class="am-input-group">							      
								  <input id="query_id" type="text" class="am-form-field" placeholder="输入UID或商品ID">							  
							</div>


							  <div class="am-input-group">							  								  	
							  	<span class="am-input-group-label am-radius"><i class="am-icon-search am-icon-fw"></i></span>
							    <input id="txt_keyword" type="text" class="am-form-field" placeholder="输入关键词搜索……">							    
							    <span class="am-input-group-btn">
							    	<button class="am-btn am-btn-default" type="button" id="search_button">搜索</button>
							    </span>
							  </div>							  					 
							</div>							
						</div>					
					</div>							
				</div>

			
				<hr>
				<div class="am-g">
					<div class="am-u-lg-12">
						<table class="am-table am-table-striped am-table-hover table-main">
							<thead>
								<tr>
									<th class="am-text-left">产品</th>
									<th width="5%">会员UID</th>
									<th width="8%">收货人</th>
									<th width="10%">订单编号</th>
									<th width="5%">数量</th>
									<th width="5%">佣金</th>
									<th width="5%">邮费</th>
									<th width="5%">总价</th>
									<th width="6%">状态</th>
									<th width="9%">下单时间</th>
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
<script type="text/javascript" src="{$BASE_V}js/order/list.js?v=2"></script>
<script language="javascript">
	$(document).ready(function() {
		search.bindParam();
		search.getOrderList();
	});
</script>