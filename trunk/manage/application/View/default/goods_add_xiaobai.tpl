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
		<link href="{STATIC_URL}js/fineuploader/fine-uploader-5.1.3.min.css" rel="stylesheet">
		<link href="{$BASE_V}css/base.css" type="text/css" rel="stylesheet">
		<link href="{$BASE_V}css/goods_add.css" rel="stylesheet" type="text/css">
		<link href="{$BASE_V}css/goods.css" type="text/css" rel="stylesheet">
		<link href="{$BASE_V}css/goods_edit.css" type="text/css" rel="stylesheet">
		<link href="{STATIC_URL}js/popover/jquery.webui-popover.min.css" rel="stylesheet">
	</head>

	<body>
		<!--{template inc/header_paul}-->
		<div class="am-cf admin-main">
			<!--{template inc/sidebar_paul}-->
			<!-- content start -->
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
								<input class="am-radius" type="text" id="goods_name" name="goods_name" value="{$editinfo->goods_name}" placeholder="商品名称请尽量包含品牌、名称、型号等信息……">
							</div>
							<small id="sm_goods_name"></small>
						</div>
						
						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">市场价</label>
							<div class="am-u-sm-3 am-u-end">
								<div class="am-input-group">
									<span class="am-input-group-label am-radius">￥</span>
									<input type="text" class="am-form-field am-radius" id="promote_price" name="promote_price" value="{$editinfo->promote_price}">
									<span class="am-input-group-label am-radius">元</span>
								</div>
								<small class="am-text-warning">吊牌/专柜/标价</small>
							</div>
						</div>
						
						<div id="product_price" class="am-form-group {if count($goods_spec_array)>0} am-hide{/if}">
							<label for="" class="am-u-sm-2 am-form-label">价格</label>
							<div class="am-u-sm-3 am-u-end">
								<div class="am-input-group">
									<span class="am-input-group-label am-radius">￥</span>
									<input type="text" class="am-form-field am-radius" id="i_no_sku_price" name="i_no_sku_price" value="{$editinfo->goods_price}">
									<span class="am-input-group-label am-radius">元</span>
								</div>
							</div>
						</div>
						<div id="product_shipping_fee" class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">邮费</label>
							<div class="am-u-sm-3 am-u-end">
								<div class="am-input-group">
									<span class="am-input-group-label am-radius">￥</span>
									<input type="text" class="am-form-field am-radius" id="shipping_fee" name="shipping_fee" value="{$editinfo->shipping_fee}">
									<span class="am-input-group-label am-radius">元</span>
								</div>
								<small class="am-text-warning">不填写邮费表示包邮</small>
							</div>
						</div>
						
						
						
						<div id="product_stock" class="am-form-group {if count($goods_spec_array)>0} am-hide{/if}">
							<label for="" class="am-u-sm-2 am-form-label">库存</label>
							<div class="am-u-sm-3 am-u-end">
								<div class="am-input-group">
									<input type="text" class="am-form-field am-radius" id="i_no_sku_stock" name="i_no_sku_stock" value="{$editinfo->goods_stock}">
									<span class="am-input-group-label am-radius">件</span>
								</div>
							</div>
						</div>

						<div id="goods_type_div" class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">产品类型</label>
							<div class="am-u-sm-8 am-u-end">							
								<div class="am-input-group">
									<select id="goods_type" class="am-dropdown" style="width: 150px;" name="goods_type">
             							{$goods_type_option}
            						</select>
            						
								</div>
							</div>							
						</div>

						<div id="goods_member_level_div" class="am-form-group hide">
							<label for="" class="am-u-sm-2 am-form-label">会员商品级别</label>
							<div class="am-u-sm-8 am-u-end">							
								<div class="am-input-group">
									<select id="goods_member_level" class="am-dropdown" name="goods_member_level">
             							{$goods_member_level_option}
            						</select>
            						
								</div>
							</div>							
						</div>						
						
						<input type="hidden" name="goods_source" id="goods_source">
						<input type="hidden" name="goods_source_id" id="goods_source_id">						
						
						
						
						<div id="product_code" class="am-form-group {if count($goods_spec_array)>0} am-hide{/if}">
							<label for="" class="am-u-sm-2 am-form-label">商品编码</label>
							<div class="am-u-sm-3 am-u-end">
								<input class="am-radius" type="text" id="coding_val" name="i_no_sku_price" placeholder="选填，用于商家系统对接" value="{$editinfo->outer_code}">
							</div>
						</div>

						
						<div id="goods_sort_div" class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">商品排序</label>
							<div class="am-u-sm-3 am-u-end">
								<input class="am-radius" type="text" id="goods_sort" name="goods_sort" value="{$editinfo->goods_sort}">								
							</div>
							<small class="am-text-warning">越大越靠前</small>
						</div>

						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">参加积分活动</label>
							<div class="am-u-sm-8 am-u-end">
								<select class="am-radius" id="is_integral" name="is_integral" style="width: 150px;" >
									{$is_integral_option}
								</select>
							</div>
							<small class="am-text-warning"></small>
						</div>								
				

						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">直推人佣金</label>
							<div class="am-u-sm-3 am-u-end">
								<div class="am-input-group">
									<span class="am-input-group-label am-radius">￥</span>
									<input type="text" class="am-form-field am-radius" id="commission_fee" name="commission_fee" value="{$editinfo->commission_fee}">
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
									<input type="text" class="am-form-field am-radius" id="commission_fee_rank" name="commission_fee_rank" value="{$editinfo->commission_fee_rank}">
									<span class="am-input-group-label am-radius">元</span>
								</div>
								<small class="am-text-warning"></small>
							</div>
						</div>

						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">分类</label>
							<div class="am-u-sm-8 am-u-end">
								<select class="am-radius" id="goods_cat_id" name="goods_cat_id" >
									{$category_list_option}
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
													<!--{loop $goods_spec_array $k $v}-->
													<div class="sku-sub-group">
														<h3 class="sku-group-title">					
															<div class="select2-container js-sku-name" style="width: 100px;">						
																<a href="javascript:void(0)" onclick="return false;" class="select2-choice" tabindex="-1">
																	<span class="select2-chosen">{$v[0]->spec_name}</span>
																	<abbr class="select2-search-choice-close"></abbr>   
																	<span class="select2-arrow"><b></b></span>
																</a>											
																<input class="select2-focusser select2-offscreen" type="text" value="" style="display:none;">
															</div>
															<input type="hidden" name="spec_name" value="{$k}" class="js-sku-name select2-offscreen" tabindex="-1">
															<a class="js-remove-sku-group remove-sku-group">×</a>					
														</h3>
														<div class="js-sku-atom-container sku-group-cont">
															<div class="js-sku-atom-list sku-atom-list">
																<!--{loop $v $kk $vv}-->
																<div class="sku-atom">
																	<span data-atom-id="{$vv->spec_id}:{$vv->spec_value_id}">{$vv->spec_value_name}</span>
																	<div class="close-modal small js-remove-sku-atom">×</div>
																	</div>
																<!--{/loop}-->
																</div>
																<a href="javascript:;" class="js-add-sku-atom add-sku" data-title="" data-content=''>+添加</a>
															</div>
														</div>
													<!--{/loop}-->

												</div>
												
												<div class="js-sku-group-opts am-u-sm-10 am-margin-left-xs am-padding-left-xs am-u-end" style="{if count($goods_spec_array)<3}display: block;{else}display: none;{/if}margin-left: 0px; ">
													
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
								<textarea class="am-radius editor" style="width: 100%; height:1000px;" id="i_des" name="i_des" rows="25" placeholder="商品简要文字描述">${echo str_replace('data-lazyload="','src="',$editinfo->goods_desc);}</textarea>
							</div>
						</div>
						<hr>
						<div class="am-form-group" id="loading_box">
							<label for="" class="am-u-sm-2 am-form-label"></label>
							<div class="am-u-sm-2 am-u-end">
								<input type="hidden" value="{$goods_id}" name="goods_id" id="goods_id" />
								<button id="submit_i_do_item" type="button" class="am-btn am-btn-primary am-btn-block am-radius" data-for-gaq="商品管理-提交商品">
									<i class="am-icon-check-circle"></i> 提　交</button>
							</div>
						</div>
					</form>
				</div>
				<hr/>
			</div>
			<!-- content end -->
		</div>
		<!--{template inc/footer_paul}-->
		
		
	</body>

</html>

<script type="text/javascript" src="{STATIC_URL}js/jquery/1.11.2/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="{$BASE_V}assets/js/amazeui.js"></script>
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
var index_url = '{MOBILE_URL}';
var static_url = '{STATIC_URL}';
var base_v = '{$BASE_V}';
var php_self = '{PHP_SELF}';
var global_param = {$global_param};
var global_value = {$global_value};//用户选择的选项值
var goods_sku_array = {$goods_sku_array};//用户选择的选项值
var goods_sku_key_value_array = {};//用户选择的选项值
var goods_image_array = {$goods_image_array};
var global_goods_cat_id = '{$editinfo->goods_cat_id}';
var postField = new Object();
postField.image_array = {$image_array};

var goods_id_name ={
	goods_id: "",
	goods_name: ""
}
</script>
		
<script type="text/javascript" src="{$BASE_V}js/common.js"></script>
<script src="{STATIC_URL}js/jquery-plugin/ui/minified/jquery.cookie-min.js" type="text/javascript"></script>
<script type="text/javascript" src="{$BASE_V}js/modal_html.js"></script>
<script src="{STATIC_URL}js/autocomplete/jquery.autocomplete.js"></script>
<script src="{STATIC_URL}js/popover/jquery.webui-popover.js"></script>

<script type="text/javascript" src="{STATIC_URL}js/fineuploader/all.fine-uploader-5.1.3.min.js"></script>
<script type="text/javascript" src="{$BASE_V}js/goods.js?v=20151129"></script>		
<script type="text/javascript" src="{STATIC_URL}js/xheditor-1.2.2/xheditor-1.2.2.min.js?v=20150822"></script>
<script type="text/javascript" src="{STATIC_URL}js/xheditor-1.2.2/xheditor_lang/zh-cn.js"></script>
<script language="javascript">
//以后jquery中的都用jq代替即可。
var editor;
function pageInit()
{
	var editor=$('#i_des').xheditor({
		tools:'full',
		upImgUrl:"{PHP_SELF}?m=tool.uploadImg&action=goods",
		upImgExt:"jpg,jpeg,gif,png",
		loadCSS:'<style>pre{margin-left:2em;border-left:3px solid #CCC;padding:0 1em;}</style>'		
	});
}
$(pageInit);
$.AMUI.progress.configure({ parent: '#loading_box' });
</script>