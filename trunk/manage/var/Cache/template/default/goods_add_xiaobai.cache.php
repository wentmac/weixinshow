<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\goods_add_xiaobai.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\manage\application\View\default\goods_add_xiaobai.tpl', 1464787681)
|| self::check('default\goods_add_xiaobai.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\manage\application\View\default\inc/header_paul.tpl', 1464787681)
|| self::check('default\goods_add_xiaobai.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\manage\application\View\default\inc/sidebar_paul.tpl', 1464787681)
|| self::check('default\goods_add_xiaobai.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\manage\application\View\default\inc/footer_paul.tpl', 1464787681)
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
		<link href="<?php echo STATIC_URL; ?>js/fineuploader/fine-uploader-5.1.3.min.css" rel="stylesheet">
		<link href="<?php echo $BASE_V;?>css/base.css" type="text/css" rel="stylesheet">
		<link href="<?php echo $BASE_V;?>css/goods_add.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $BASE_V;?>css/goods.css" type="text/css" rel="stylesheet">
		<link href="<?php echo $BASE_V;?>css/goods_edit.css" type="text/css" rel="stylesheet">
		<link href="<?php echo STATIC_URL; ?>js/popover/jquery.webui-popover.min.css" rel="stylesheet">
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
					<div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">上传产品</strong></div>
				</div>
				<hr/>
				<div class="am-container" style="margin-left: 0">
					<form class="am-form am-form-horizontal">
						<div class="am-form-group">
							<label class="am-u-sm-2 am-form-label">图片</label>
							<div class="am-u-sm-8 am-u-end">
								<div class="controls" id="fine-uploader"></div>
								<div class="am-cf"></div>
								<div class="help-desc am-cf">
									<small>
										<span class="am-text-xs am-icon-flag-checkered"></span>&nbsp;表示为主图;
										所上传的图片最大支持 1 MB 的图片( jpg / gif / png )，不能选中大于 1 MB 的图片
									</small></div>
								<input type="hidden" id="set_goods_image_id">
							</div>
						</div>
						<div class="am-form-group">
							<label for="goods_name" class="am-u-sm-2 am-form-label">商品名</label>
							<div class="am-u-sm-8 am-u-end">
								<input class="am-radius" type="text" id="goods_name" name="goods_name" value="<?php echo $editinfo->goods_name;?>" placeholder="商品名称请尽量包含品牌、名称、型号等信息……">
							</div>
							<small id="sm_goods_name"></small>
						</div>
						
						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">市场价</label>
							<div class="am-u-sm-3 am-u-end">
								<div class="am-input-group">
									<span class="am-input-group-label am-radius">￥</span>
									<input type="text" class="am-form-field am-radius" id="promote_price" name="promote_price" value="<?php echo $editinfo->promote_price;?>">
									<span class="am-input-group-label am-radius">元</span>
								</div>
								<small class="am-text-warning">吊牌/专柜/标价</small>
							</div>
						</div>
						
						<div id="product_price" class="am-form-group <?php if(count($goods_spec_array)>0) { ?> am-hide<?php } ?>">
							<label for="" class="am-u-sm-2 am-form-label">价格</label>
							<div class="am-u-sm-3 am-u-end">
								<div class="am-input-group">
									<span class="am-input-group-label am-radius">￥</span>
									<input type="text" class="am-form-field am-radius" id="i_no_sku_price" name="i_no_sku_price" value="<?php echo $editinfo->goods_price;?>">
									<span class="am-input-group-label am-radius">元</span>
								</div>
							</div>
						</div>
						<div id="product_shipping_fee" class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">邮费</label>
							<div class="am-u-sm-3 am-u-end">
								<div class="am-input-group">
									<span class="am-input-group-label am-radius">￥</span>
									<input type="text" class="am-form-field am-radius" id="shipping_fee" name="shipping_fee" value="<?php echo $editinfo->shipping_fee;?>">
									<span class="am-input-group-label am-radius">元</span>
								</div>
								<small class="am-text-warning">不填写邮费表示包邮</small>
							</div>
						</div>
						
						
						
						<div id="product_stock" class="am-form-group <?php if(count($goods_spec_array)>0) { ?> am-hide<?php } ?>">
							<label for="" class="am-u-sm-2 am-form-label">库存</label>
							<div class="am-u-sm-3 am-u-end">
								<div class="am-input-group">
									<input type="text" class="am-form-field am-radius" id="i_no_sku_stock" name="i_no_sku_stock" value="<?php echo $editinfo->goods_stock;?>">
									<span class="am-input-group-label am-radius">件</span>
								</div>
							</div>
						</div>

						<div id="goods_type_div" class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">产品类型</label>
							<div class="am-u-sm-8 am-u-end">							
								<div class="am-input-group">
									<select id="goods_type" class="am-dropdown" style="width: 150px;" name="goods_type">
             							<?php echo $goods_type_option;?>
            						</select>
            						
								</div>
							</div>							
						</div>

						<div id="goods_member_level_div" class="am-form-group hide">
							<label for="" class="am-u-sm-2 am-form-label">会员商品级别</label>
							<div class="am-u-sm-8 am-u-end">							
								<div class="am-input-group">
									<select id="goods_member_level" class="am-dropdown" name="goods_member_level">
             							<?php echo $goods_member_level_option;?>
            						</select>
            						
								</div>
							</div>							
						</div>						
						
						<input type="hidden" name="goods_source" id="goods_source">
						<input type="hidden" name="goods_source_id" id="goods_source_id">						
						
						
						
						<div id="product_code" class="am-form-group <?php if(count($goods_spec_array)>0) { ?> am-hide<?php } ?>">
							<label for="" class="am-u-sm-2 am-form-label">商品编码</label>
							<div class="am-u-sm-3 am-u-end">
								<input class="am-radius" type="text" id="coding_val" name="i_no_sku_price" placeholder="选填，用于商家系统对接" value="<?php echo $editinfo->outer_code;?>">
							</div>
						</div>

						
						<div id="goods_sort_div" class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">商品排序</label>
							<div class="am-u-sm-3 am-u-end">
								<input class="am-radius" type="text" id="goods_sort" name="goods_sort" value="<?php echo $editinfo->goods_sort;?>">								
							</div>
							<small class="am-text-warning">越大越靠前</small>
						</div>

						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">参加积分活动</label>
							<div class="am-u-sm-8 am-u-end">
								<select class="am-radius" id="is_integral" name="is_integral" style="width: 150px;" >
									<?php echo $is_integral_option;?>
								</select>
							</div>
							<small class="am-text-warning"></small>
						</div>								
				

						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">直推人佣金</label>
							<div class="am-u-sm-3 am-u-end">
								<div class="am-input-group">
									<span class="am-input-group-label am-radius">￥</span>
									<input type="text" class="am-form-field am-radius" id="commission_fee" name="commission_fee" value="<?php echo $editinfo->commission_fee;?>">
									<span class="am-input-group-label am-radius">元</span>
								</div>
								<small class="am-text-warning"></small>
							</div>
						</div>
						
						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">排位奖佣金</label>
							<div class="am-u-sm-3 am-u-end">
								<div class="am-input-group">
									<span class="am-input-group-label am-radius">￥</span>
									<input type="text" class="am-form-field am-radius" id="commission_fee_rank" name="commission_fee_rank" value="<?php echo $editinfo->commission_fee_rank;?>">
									<span class="am-input-group-label am-radius">元</span>
								</div>
								<small class="am-text-warning"></small>
							</div>
						</div>

						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">分类</label>
							<div class="am-u-sm-8 am-u-end">
								<select class="am-radius" id="goods_cat_id" name="goods_cat_id" >
									<?php echo $category_list_option;?>
								</select>
							</div>
						</div>
						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label am-text-middle am-fl">商品规格</label>
							<div>
								<div class="group-inner am-u-sm-10 am-margin-left-0">
									<div class="js-goods-sku control-group">
										<div id="sku-region" class="controls">
											<div class="sku-group am-u-sm-10 am-margin-left-0 am-padding-left-0 ">
												<div class="js-sku-list-container">
													<?php if(is_array($goods_spec_array)) foreach($goods_spec_array AS $k => $v) { ?>
													<div class="sku-sub-group">
														<h3 class="sku-group-title">					
															<div class="select2-container js-sku-name" style="width: 100px;">						
																<a href="javascript:void(0)" onclick="return false;" class="select2-choice" tabindex="-1">
																	<span class="select2-chosen"><?php echo $v['0']->spec_name;?></span>
																	<abbr class="select2-search-choice-close"></abbr>   
																	<span class="select2-arrow"><b></b></span>
																</a>											
																<input class="select2-focusser select2-offscreen" type="text" value="" style="display:none;">
															</div>
															<input type="hidden" name="spec_name" value="<?php echo $k;?>" class="js-sku-name select2-offscreen" tabindex="-1">
															<a class="js-remove-sku-group remove-sku-group">×</a>					
														</h3>
														<div class="js-sku-atom-container sku-group-cont">
															<div class="js-sku-atom-list sku-atom-list">
																<?php if(is_array($v)) foreach($v AS $kk => $vv) { ?>
																<div class="sku-atom">
																	<span data-atom-id="<?php echo $vv->spec_id;?>:<?php echo $vv->spec_value_id;?>"><?php echo $vv->spec_value_name;?></span>
																	<div class="close-modal small js-remove-sku-atom">×</div>
																	</div>
																<?php } ?>
																</div>
																<a href="javascript:;" class="js-add-sku-atom add-sku" data-title="" data-content=''>+添加</a>
															</div>
														</div>
													<?php } ?>

												</div>
												
												<div class="js-sku-group-opts am-u-sm-10 am-margin-left-xs am-padding-left-xs am-u-end" style="<?php if(count($goods_spec_array)<3) { ?>display: block;<?php } else { ?>display: none;<?php } ?>margin-left: 0px; ">
													
													<h3 class="sku-group-title">
														<button type="button"  class="js-add-sku-group am-btn am-btn-sm am-btn-secondary">
															<i class="am-icon-plus-circle"></i>添加规格项目</button>
													</h3>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

						</div>
						<div class="am-form-group">
						<div class="i_do_div rel form-horizontal" id="goods_sku_stock">
							<div class="js-goods-stock control-group" style="display: block;">							
								<div id="stock-region" class="controls sku-stock" style="margin-left: 32px;"></div>
							</div>
						</div>
						</div>
						<div class="am-form-group">
							<label class="am-u-sm-2 am-form-label">描述</label>
							<div class="am-u-sm-10 am-u-end">
								<textarea class="am-radius editor" style="width: 100%; height:1000px;" id="i_des" name="i_des" rows="25" placeholder="商品简要文字描述"><?php echo str_replace('data-lazyload="','src="',$editinfo->goods_desc); ?></textarea>
							</div>
						</div>
						<hr>
						<div class="am-form-group" id="loading_box">
							<label for="" class="am-u-sm-2 am-form-label"></label>
							<div class="am-u-sm-2 am-u-end">
								<input type="hidden" value="<?php echo $goods_id;?>" name="goods_id" id="goods_id" />
								<button id="submit_i_do_item" type="button" class="am-btn am-btn-primary am-btn-block am-radius" data-for-gaq="商品管理-提交商品">
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
<script type="text/template" id="qq-simple-thumbnails-template">
	<div class="control-action qq-uploader-selector">
		<div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
			<span>Drop files here to upload</span>
		</div>
		<span class="qq-drop-processing-selector qq-drop-processing">
		<span>Processing dropped files...</span>
		<span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
		</span>
		<ul class="js-upload-image-list upload-image-list clearfix ui-sortable">
			<span class="qq-upload-list-selector">
				<li class="upload-preview-img sort">
					<img src="" qq-max-size="80" class="qq-thumbnail-selector">
					<div class="uploadify-progress qq-progress-bar-container-selector">
						 <div class="uploadify-progress-bar qq-progress-bar-selector"></div>
						</div>
					<a href="javascript:;" class="close-modal small js-remove-image qq-upload-cancel-selector">×</a>
					<a href="javascript:;" class="close-modal small js-remove-image qq-upload-delete-selector">×</a>
					<a class="qq-upload-retry-selector upload-retry" href="#">重试</a>
					<a href="javascript:;" style="top: 15px;" class="close-modal small js-set-image-id" >
						<span class="am-icon-flag-o am-text-xs"></span>
					</a>
				</li>
			</span>
			<li class="fileinput-button">
				<a class="fileinput-button-icon js-fileupload-input fileupload qq-upload-button-selector" href="javascript:;">+</a>
			</li>
			<div class="uploadify-progress-all qq-total-progress-bar-container-selector">
				<div class="uploadify-progress-bar qq-total-progress-bar-selector" id="qq-total-progress-bar"></div>
			</div>
		</ul>
	</div>
	
</script>
<script type="text/javascript">
var index_url = '<?php echo MOBILE_URL; ?>';
var static_url = '<?php echo STATIC_URL; ?>';
var base_v = '<?php echo $BASE_V;?>';
var php_self = '<?php echo PHP_SELF; ?>';
var global_param = <?php echo $global_param;?>;
var global_value = <?php echo $global_value;?>;//用户选择的选项值
var goods_sku_array = <?php echo $goods_sku_array;?>;//用户选择的选项值
var goods_sku_key_value_array = {};//用户选择的选项值
var goods_image_array = <?php echo $goods_image_array;?>;
var global_goods_cat_id = '<?php echo $editinfo->goods_cat_id;?>';
var postField = new Object();
postField.image_array = <?php echo $image_array;?>;

var goods_id_name ={
	goods_id: "",
	goods_name: ""
}
</script>
		
<script src="<?php echo $BASE_V;?>js/common.js" type="text/javascript"></script>
<script src="<?php echo STATIC_URL; ?>js/jquery-plugin/ui/minified/jquery.cookie-min.js" type="text/javascript"></script>
<script src="<?php echo $BASE_V;?>js/modal_html.js" type="text/javascript"></script>
<script src="<?php echo STATIC_URL; ?>js/autocomplete/jquery.autocomplete.js" type="text/javascript"></script>
<script src="<?php echo STATIC_URL; ?>js/popover/jquery.webui-popover.js" type="text/javascript"></script>

<script src="<?php echo STATIC_URL; ?>js/fineuploader/all.fine-uploader-5.1.3.min.js" type="text/javascript"></script>
<script src="<?php echo $BASE_V;?>js/goods.js?v=20151129" type="text/javascript"></script>		
<script src="<?php echo STATIC_URL; ?>js/xheditor-1.2.2/xheditor-1.2.2.min.js?v=20150822" type="text/javascript"></script>
<script src="<?php echo STATIC_URL; ?>js/xheditor-1.2.2/xheditor_lang/zh-cn.js" type="text/javascript"></script>
<script language="javascript">
//以后jquery中的都用jq代替即可。
var editor;
function pageInit()
{
	var editor=$('#i_des').xheditor({
		tools:'full',
		upImgUrl:"<?php echo PHP_SELF; ?>?m=tool.uploadImg&action=goods",
		upImgExt:"jpg,jpeg,gif,png",
		loadCSS:'<style>pre{margin-left:2em;border-left:3px solid #CCC;padding:0 1em;}</style>'		
	});
}
$(pageInit);
$.AMUI.progress.configure({ parent: '#loading_box' });
</script>