<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\order/list.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\manage\application\View\default\order\list.tpl', 1465815922)
|| self::check('default\order/list.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\manage\application\View\default\inc/header_paul.tpl', 1465815922)
|| self::check('default\order/list.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\manage\application\View\default\inc/sidebar_paul.tpl', 1465815922)
|| self::check('default\order/list.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\manage\application\View\default\inc/footer_paul.tpl', 1465815922)
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
		<meta name="save" content="history">
		<link href="<?php echo STATIC_URL; ?>common/assets/css/amazeui.css" rel="stylesheet" type="text/css">
		<link href="<?php echo STATIC_URL; ?>common/assets/css/admin.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $BASE_V;?>css/base.css" type="text/css" rel="stylesheet">
		<link href="<?php echo $BASE_V;?>css/page.css" type="text/css" rel="stylesheet">
		<link href="<?php echo $BASE_V;?>css/form_list.css" type="text/css" rel="stylesheet">
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
<script type="text/javascript">
	var index_url = '<?php echo MOBILE_URL; ?>';
	var mobile_url = '<?php echo MOBILE_URL; ?>';
	var static_url = '<?php echo STATIC_URL; ?>';
	var base_v = '<?php echo $BASE_V;?>';
	var php_self = '<?php echo PHP_SELF; ?>';
	var searchParameter = <?php echo $searchParameter;?>;
</script>
<script src="<?php echo STATIC_URL; ?>common/assets/js/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo STATIC_URL; ?>common/assets/js/amazeui.js" type="text/javascript"></script>
<script src="<?php echo STATIC_URL; ?>js/jquery-plugin/jquery.pagination-min.js" type="text/javascript"></script>
<script src="<?php echo STATIC_URL; ?>common/assets/js/app.js" type="text/javascript"></script>
<script src="<?php echo STATIC_URL; ?>js/jquery-plugin/ui/minified/jquery.cookie-min.js" type="text/javascript"></script>
<script src="<?php echo $BASE_V;?>js/common.js" type="text/javascript"></script>
<script src="<?php echo $BASE_V;?>js/modal_html.js" type="text/javascript"></script>
<script src="<?php echo $BASE_V;?>js/order/list.js?v=2" type="text/javascript"></script>
<script language="javascript">
	$(document).ready(function() {
		search.bindParam();
		search.getOrderList();
	});
</script>