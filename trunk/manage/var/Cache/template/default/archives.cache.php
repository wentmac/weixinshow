<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\archives.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\manage\application\View\default\archives.tpl', 1464864377)
|| self::check('default\archives.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\manage\application\View\default\inc/header_paul.tpl', 1464864377)
|| self::check('default\archives.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\manage\application\View\default\inc/sidebar_paul.tpl', 1464864377)
|| self::check('default\archives.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\manage\application\View\default\inc/footer_paul.tpl', 1464864377)
;?>
﻿<!DOCTYPE html>
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
  <!-- header end -->  <div class="am-cf admin-main">
        <style type="text/css">
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
    <!-- sidebar end -->    <!-- content start -->
    <div class="admin-content">
      <div class="am-cf am-padding">
        <div class="am-fl">
            <strong class="am-text-primary am-text-lg">内容发布向导</strong>
        </div>        
      </div>
      <hr/>      
      
      <div class="am-g">
        <div class="am-u-lg-12">
          <table class="am-table am-table-striped am-table-hover table-main">
            <thead>
              <tr>
                <th width="10%" class="am-text-center">频道ID</th>
                <th width="50%" class="am-text-center">频道内容模型</th>                                
                <th width="40%" class="am-text-center">操作选项</th>
              </tr>
            </thead>

            <tbody>
              <?php if(is_array($channeltype)) foreach($channeltype AS $k => $v) { ?>
                <tr>                  
                <td class="am-text-middle"><?php echo $k;?></td>
                <td class="am-text-middle"><?php echo $v;?></td>       
                <td class="am-text-middle"><a href="<?php echo PHP_SELF; ?>?m=category">管理栏目</a> | <a href="<?php echo PHP_SELF; ?>?m=archives.arclist&channelid=<?php echo $k;?>">管理内容</a> | <a href="<?php echo PHP_SELF; ?>?m=archives.catgoto&channelid=<?php echo $k;?>">发布内容</a></td>                
              </tr>    
              <?php } ?>          
            </tbody>

            <tfoot>
              <tr>
                <td colspan="9" class="am-text-center"></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>      
      <hr/>
    </div>
    <!-- content end -->
  </div>
  <footer>
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
</footer>  <div class="am-popup" id="my-popup">
    <div class="am-popup-inner">
      <div class="am-popup-hd">
        <h4 class="am-popup-title">批量调整售价</h4>
        <span data-am-modal-close class="am-close">&times;</span>
      </div>
      <div class="am-popup-bd">
        <div class="am-padding-sm">
          在原始价格的基础上：
          <div class="am-form-group">
            <label>
              <input type="radio" name="set_price_type" value="plus" checked> <span class="am-text-danger">+加价</span>
            </label>
            <label>
              <input type="radio" name="set_price_type" value="less"> <span class="am-text-success">-减价</span>
            </label>
          </div>
          <div class="am-g">
            <div class="am-u-sm-7 am-margin-0 am-padding-0">
              <label>
                <div class="am-input-group am-input-group-xs">
                  <span class="am-input-group-label"><input type="radio" name="set_price_class" value="fixed"> 固定的金额￥</span>
                  <input type="text" class="am-form-field" id="fixed_value" placeholder="输入固定的金额">
                </div>
              </label>
              <label>
                <div class="am-input-group am-input-group-xs">
                  <span class="am-input-group-label">
                	<input type="radio" name="set_price_class" value="percent" checked="checked"> 价格百分比%</span>
                  <input type="text" class="am-form-field" id="percent_value" placeholder="请输入总价加/减百分比">
                </div>
              </label>
            </div>
          </div>
          <a class="am-btn am-btn-primary" id="set_price_button"><i class="am-icon-check"></i> 提交</a>
          <div class="am-text-warning am-text-xs">温馨提示：批量调整价格时，如遇到调整后的价格小于成本价（原始价格减去利润）时，系统会自动调回原价，以避免给您带来亏损！</div>
        </div>
      </div>
      <div class="doc-example"></div>
    </div>
  </div>
</body>

</html>
<script type="text/javascript">
var index_url = '<?php echo INDEX_URL; ?>';
var static_url = '<?php echo STATIC_URL; ?>';
var base_v = '<?php echo $BASE_V;?>';
var php_self = '<?php echo PHP_SELF; ?>';
</script>
<script src="<?php echo STATIC_URL; ?>common/assets/js/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo STATIC_URL; ?>common/assets/js/amazeui.js" type="text/javascript"></script>
<script src="<?php echo STATIC_URL; ?>common/assets/js/app.js" type="text/javascript"></script>
<script src="<?php echo $BASE_V;?>js/common.js" type="text/javascript"></script>
<script src="<?php echo STATIC_URL; ?>js/jquery-plugin/ui/minified/jquery.cookie-min.js" type="text/javascript"></script>
<script src="<?php echo $BASE_V;?>js/modal_html.js" type="text/javascript"></script>