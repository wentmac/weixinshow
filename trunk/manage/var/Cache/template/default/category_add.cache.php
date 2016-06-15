<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\category_add.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\manage\application\View\default\category_add.tpl', 1464868534)
|| self::check('default\category_add.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\manage\application\View\default\inc/header_paul.tpl', 1464868534)
|| self::check('default\category_add.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\manage\application\View\default\inc/sidebar_paul.tpl', 1464868534)
|| self::check('default\category_add.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\manage\application\View\default\inc/footer_paul.tpl', 1464868534)
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
		<link href="<?php echo $BASE_V;?>css/goods_add.css" rel="stylesheet" type="text/css">		
		<link href="<?php echo $BASE_V;?>css/goods_edit.css" type="text/css" rel="stylesheet">		
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
			<div class="admin-content" id="i_do_wrap">
				<div class="am-cf am-padding">
					<div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">分类管理</strong></div>
				</div>
				<hr/>
				<div class="am-container" style="margin-left: 0">
					<form class="am-form am-form-horizontal">
						<div class="am-form-group">
							<label for="title" class="am-u-sm-2 am-form-label">分类名称</label>
							<div class="am-u-sm-7 am-u-end">
								<input class="am-radius" type="text" id="cat_name" name="cat_name" value="<?php echo $editinfo->cat_name;?>" placeholder="分类名称">
							</div>
							<small id="sm_title">这将是它在站点上显示的名字。</small>
						</div>

						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">内容模型</label>
							<div class="am-u-sm-6 am-u-end">							
								<div class="am-input-group">
									<select id="channeltype" class="am-dropdown" style="width: 150px;" name="channeltype">
             							<?php echo $channeltype_option;?>
            						</select>
            						
								</div>
							</div>	
							<small>栏目模型分类(article,image)</small>						
						</div>																											
						
						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">别名</label>
							<div class="am-u-sm-8 am-u-end">
								<input class="am-radius" type="text" id="category_nicename" name="category_nicename" placeholder="别名是对于 URL 友好的一个别称。它通常为小写并且只能包含字母，数字和连字符（-）。" value="<?php echo $editinfo->category_nicename;?>">
							</div>
							<small></small>
						</div>

						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">父级分类</label>
							<div class="am-u-sm-6 am-u-end">							
								<div class="am-input-group">
									<select id="cat_pid" name="cat_pid" class="am-dropdown" style="width: 150px;">
             							<?php echo $category_tree_list;?>
            						</select>
            						
								</div>
							</div>	
							<small>分类目录，和标签不同，它可以有层级关系。</small>						
						</div>			
						
						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">关键字</label>
							<div class="am-u-sm-8 am-u-end">
								<input class="am-radius" type="text" id="category_keywords" name="category_keywords" placeholder="关键字" value="<?php echo $editinfo->category_keywords;?>">
							</div>
							<small>keywords</small>
						</div>										

						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">描述</label>
							<div class="am-u-sm-8 am-u-end">								
								<textarea class="10" rows="3" id="category_description" name="category_description"><?php echo $editinfo->category_description;?></textarea>
							</div>
							<small>description</small>
						</div>

						
						<div class="am-form-group">
							<label class="am-u-sm-2 am-form-label">描述</label>
							<div class="am-u-sm-10 am-u-end">
								<textarea class="am-radius editor" style="width: 100%; height:700px;" id="category_content" name="category_content" rows="25" placeholder="某个分类的详细说明"><?php echo $editinfo->category_content;?></textarea>
							</div>
							<small class="am-text-warning"></small>
						</div>

						
						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">分类排序</label>
							<div class="am-u-sm-3 am-u-end">
								<input class="am-radius" type="text" id="cat_order" name="cat_order" value="<?php echo $editinfo->cat_order;?>">								
							</div>
							<small class="am-text-warning">越大越靠前</small>
						</div>

						
						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">前台链接文件</label>
							<div class="am-u-sm-3 am-u-end">
								<input class="am-radius" type="text" id="urlfile" name="urlfile" value="<?php echo $editinfo->urlfile;?>">								
							</div>
							<small class="am-text-warning">没有特别需求,请不用填写这里</small>
						</div>						
				
						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">前台导航条显示</label>
							<div class="am-u-sm-3 am-u-end">							
								<div class="am-input-group">
									<select id="nav_show" name="nav_show" class="am-dropdown" style="width: 150px;">
             							<?php echo $nav_show_array_option;?>
            						</select>
            						
								</div>
							</div>	
							<small class="am-text-warning">是否在前台导航条中显示</small>						
						</div>

						<hr>
						<div class="am-form-group" id="loading_box">
							<label for="" class="am-u-sm-2 am-form-label"></label>
							<div class="am-u-sm-2 am-u-end">
								<input type="hidden" value="<?php echo $cat_id;?>" name="cat_id" id="cat_id" />
								<button id="submit_i_do_item" type="button" class="am-btn am-btn-primary am-btn-block am-radius" data-for-gaq="保存">
									<i class="am-icon-check-circle"></i> 提　交</button>
							</div>
						</div>
					</form>
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

<script src="<?php echo STATIC_URL; ?>js/jquery/1.11.2/jquery-1.11.2.min.js" type="text/javascript"></script>
<script src="<?php echo $BASE_V;?>assets/js/amazeui.js" type="text/javascript"></script>
<script type="text/javascript">
var index_url = '<?php echo MOBILE_URL; ?>';
var static_url = '<?php echo STATIC_URL; ?>';
var base_v = '<?php echo $BASE_V;?>';
var php_self = '<?php echo PHP_SELF; ?>';
var postField = new Object();
</script>
<script src="<?php echo STATIC_URL; ?>js/jquery-plugin/ui/minified/jquery.cookie-min.js" type="text/javascript"></script>		
<script src="<?php echo $BASE_V;?>js/common.js" type="text/javascript"></script>
<script src="<?php echo $BASE_V;?>js/modal_html.js" type="text/javascript"></script>
<script src="<?php echo $BASE_V;?>js/category/detail.js?v=20151129" type="text/javascript"></script>		
<script src="<?php echo STATIC_URL; ?>js/xheditor-1.2.2/xheditor-1.2.2.min.js?v=20150822" type="text/javascript"></script>
<script src="<?php echo STATIC_URL; ?>js/xheditor-1.2.2/xheditor_lang/zh-cn.js" type="text/javascript"></script>
<script language="javascript">
//以后jquery中的都用jq代替即可。
var editor;
function pageInit()
{
	var editor=$('#category_content').xheditor({
		tools:'full',
		upImgUrl:"manage.php?m=tool.uploadImg&amp;action=category",
		upImgExt:"jpg,jpeg,gif,png",
		loadCSS:'<style>pre{margin-left:2em;border-left:3px solid #CCC;padding:0 1em;}</style>'		
	});
}
$(pageInit);
$.AMUI.progress.configure({ parent: '#loading_box' });
</script>