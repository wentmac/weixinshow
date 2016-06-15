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
		<link href="{STATIC_URL}js/popover/jquery.webui-popover.min.css" rel="stylesheet">
		<style>
			#dianzhao {
				cursor: pointer;
				border: 2px solid #fff;
			}
			#dianzhao:hover {
				border: 2px solid #0e90d2;
			}
			#shop_logo {
				cursor: pointer;
				border: 2px solid #fff;
			}
			#shop_logo:hover {
				border: 2px solid #0e90d2;
			}
			#txt_shop_name {
				padding: 2px 2px 2px 2px!important;
				width: 60%;
			}
			.qq-hide {
				display: none;
			}
		</style>
	</head>

	<body>
		<!--{template inc/header_paul}-->
		<div class="am-cf admin-main">
			<!--{template inc/sidebar_paul}-->
			<!-- content start -->
			<div class="admin-content">
				<div class="am-cf am-padding">
					<div class="am-fl"><strong class="am-text-primary am-text-lg">店铺设置</strong></div>
				</div>
				<hr/>
				<div class="am-u-sm-12">
					<div class="am-u-sm-3 am-panel am-panel-default">
						<div class="am-u-sm-12">
							<div class="am-panel-hd am-cf">
								<img width="44" src="{$BASE_V}image/common_hd_logo.png">
							</div>
							<div style="am-padding-xs">
								<div class="am-form-group am-form-file am-padding-xs">
									<img id="dianzhao" src="{$shop_info->shop_signboard_image_url}" class="am-img-responsive" alt="点击修改店招" title="点击修改店招" />
								</div>
							</div>
							<div class="am-u-sm-4 am-margin-top-sm am-padding-xs">
								<img id="shop_logo" class="am-circle" src="{$shop_info->shop_image_url}" width="90" height="90" alt="点击修改LOGO" title="点击修改LOGO" />
							</div>
							<div id="index_hd_shop_info" class="am-fl am-u-sm-8 am-margin-top-sm">
								<h1 id="hd_name" class="am-text-lg" data-name="{$shop_info->shop_name}">
									<span>{$shop_info->shop_name}</span>
								</h1>
								<div id="hd_weixin" class="am-text-sm" style="display: block;">
									微信: {$shop_info->weixin_id}
								</div>
								<p id="hd_fav_count" class="am-text-sm" style="display: block;">
									已有&nbsp;<em id="hd_fav_count_em" class="am-text-danger">0</em> 人收藏
								</p>

							</div>
							<div id="hd_intro" class="am-u-sm-12 am-margin-top-lg">
								<hr>
								<p id="hd_note">{$shop_info->shop_intro}
									<em class="am-icon-edit am-text-primary"></em>
								</p>
							</div>
						</div>
						<div class="am-u-sm-12">
							<div class="am-container am-padding-sm" style="background:#fff;">
								<div class="am-tabs" id="doc-my-tabs">
									<ul class="am-tabs-nav am-nav am-nav-tabs am-nav-justify">
										<li class="am-active">
											<a>按上架时间展示</a>
										</li>
										<li>
											<a>按商品分类展示</a>

										</li>
									</ul>
									<div class="am-tabs-bd">
										<div class="am-tab-panel am-active">
											<div class="" style="height: 240px; background:#f1f1f1">
												<div class="am-g am-padding-xs">
													<div class="am-u-sm-6" style="padding:3px">
														<div style="background:#ccc; height: 50px;"></div>
													</div>
													<div class="am-u-sm-6" style="padding:3px">
														<div style="background:#ccc; height: 50px;"></div>
													</div>
													<div class="am-u-sm-6" style="padding:3px">
														<div style="background:#ccc; height: 50px;"></div>
													</div>
													<div class="am-u-sm-6" style="padding:3px">
														<div style="background:#ccc; height: 50px;"></div>
													</div>
													<div class="am-u-sm-6" style="padding:3px">
														<div style="background:#ccc; height: 50px;"></div>
													</div>
													<div class="am-u-sm-6" style="padding:3px">
														<div style="background:#ccc; height: 50px;"></div>
													</div>
													<div class="am-u-sm-6" style="padding:3px">
														<div style="background:#ccc; height: 50px;"></div>
													</div>
													<div class="am-u-sm-6" style="padding:3px">
														<div style="background:#ccc; height: 50px;"></div>
													</div>
												</div>
											</div>
										</div>
										<div class="am-tab-panel">
											<div class="" style="height: 240px; background:#f1f1f1">
												<div class="am-g am-padding-xs">
													<div><small>分类一</small></div>
													<div class="am-u-sm-6" style="padding:3px">
														<div style="background:#ccc; height: 50px;"></div>
													</div>
													<div class="am-u-sm-6" style="padding:3px">
														<div style="background:#ccc; height: 50px;"></div>
													</div>
													<div><small>分类二</small></div>
													<div class="am-u-sm-6" style="padding:3px">
														<div style="background:#ccc; height: 50px;"></div>
													</div>
													<div class="am-u-sm-6" style="padding:3px">
														<div style="background:#ccc; height: 50px;"></div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>

							</div>
						</div>
					</div>

					<div class="am-u-sm-9 am-container " style="margin-left: 0 ">
						<form class="am-form am-form-horizontal ">
							<label class="am-u-sm-2 am-text-right">设置店招</label>
							<div class="am-form-group ">
								<div class="am-form-file">
									<div class="am-u-sm-12 am-u-end ">
										<div class="controls am-u-sm-8 am-fl" id="fine-uploader-signboard">

										</div>
										<div id="triggerUpload" class="modal-action pull-right am-u-sm-4">
											<input type="button" class="am-btn am-btn-primary js-upload-image" data-loading-text="上传中..." value="确定上传">
										</div>
										<small>店招图片尺寸建议：640X330px</small>
										<!--
									<button type="button" class="am-btn am-btn-danger am-btn-sm">
										<i class="am-icon-cloud-upload"></i> 选择要上传的文件</button>
									<input id="dianzhao_file" type="file" multiple>-->
									</div>
								</div>
							</div>
							<div class="am-form-group ">
								<label class="am-u-sm-2 am-text-right">设置LOGO</label>
								<div class="am-form-file">
									<div class="am-u-sm-12 am-u-end ">
										<div class="controls am-u-sm-8 am-fl" id="fine-uploader-logo">

										</div>
										<div id="triggerUpload_logo" class="modal-action pull-right am-u-sm-4">
											<input type="button" class="am-btn am-btn-primary js-upload-image" data-loading-text="上传中..." value="确定上传">
											
										</div>
										<small>LOGO图片尺寸建议：110X110px</small>
										<!--
										<button type="button" class="am-btn am-btn-success am-btn-sm">
											<i class="am-icon-cloud-upload"></i> 选择要上传的文件</button>
										<input id="logo_file" type="file" multiple>
										-->
									</div>
									<div id="logo_list"></div>
								</div>
							</div>
							<div class="am-form-group ">
								<label class="am-u-sm-2 am-text-right">店铺名称</label>
								<div class="am-u-sm-8 am-u-end ">
									<input type="text" id="txt_shopname" value="{$shop_info->shop_name}" />
								</div>
							</div>
							<div class="am-form-group ">
								<label class="am-u-sm-2 am-text-right">微信号</label>
								<div class="am-u-sm-8 am-u-end ">
									<input type="text" id="txt_weixin" value="{$shop_info->weixin_id}" />
								</div>
							</div>
							<div class="am-form-group ">
								<label class="am-u-sm-2 am-text-right">手机号</label>
								<div class="am-u-sm-8 am-u-end ">
									<input type="text" id="txt_weixin" value="{$shop_info->mobile}" />
								</div>
							</div>

							<div class="am-form-group ">
								<label for="goods_name " class="am-u-sm-2 am-text-right">展示方式</label>
								<div class="am-u-sm-8 am-u-end ">
									<input type="radio" name="goods_show_type" id="rad_goods_show_type0" />
									<label for="rad_goods_show_type0">按上架时间展示</label>

									<input type="radio" name="goods_show_type" id="rad_goods_show_type1" />
									<label for="rad_goods_show_type1">按商品分类</label>
								</div>
							</div>

							<div class="am-form-group " style="display: none;"
								<label for="goods_name " class="am-u-sm-2 am-text-right">支付类型</label>
								<div class="am-u-sm-8 am-u-end ">
									<input type="radio" name="payment_type" id="rad_payment_type_yes" />
									<label for="rad_payment_type_yes">开启货到付款</label>

									<input type="radio" name="payment_type" id="rad_payment_type_no" />
									<label for="rad_payment_type_no">关闭货到付款</label>
								</div>
							</div>
							<div class="am-form-group ">
								<label for="goods_name " class="am-u-sm-2 am-text-right">库存设置</label>
								<div class="am-u-sm-8 am-u-end ">
									<input type="radio" name="stock_setting" id="rad_stock_setting1" />
									<label for="rad_stock_setting1">拍下减库存</label>

									<input type="radio" name="stock_setting" checked id="rad_stock_setting2" />
									<label for="rad_stock_setting2">付款减库存</label>
								</div>
							</div>
							<div class="am-form-group">
								<label for="i_des" class="am-u-sm-2 am-text-right">退货状态</label>
								<div class="am-u-sm-8 am-u-end">
									<input type="radio" name="refund_type" id="rad_refund_type1" />
									<label for="rad_refund_type1">关闭7天退货</label>
									<input type="radio" name="refund_type" id="rad_refund_type2" />
									<label for="rad_refund_type2">开启7天退货</label>
								</div>
							</div>
							<div class="am-form-group ">
								<label class="am-u-sm-2 am-form-label ">店铺的实体店地址</label>
								<div class="am-u-sm-8 am-u-end ">
									<textarea class="am-radius" id="shop_address" name="shop_intro" rows="5" placeholder="店铺的实体店地址">{$shop_info->shop_address}</textarea>
								</div>
							</div>
							<div class="am-form-group ">
								<label class="am-u-sm-2 am-form-label ">店铺简介公告</label>
								<div class="am-u-sm-8 am-u-end ">
									<textarea class="am-radius" id="shop_intro" name="shop_intro" rows="5" placeholder="店铺的实体店地址">{$shop_info->shop_intro}</textarea>
								</div>
							</div>
							<hr>
							<div class="am-form-group">
								<label for="" class="am-u-sm-2 am-form-label"></label>
								<div class="am-u-sm-2 am-u-end">
									<button id="btn_submit" type="button" class="am-btn am-btn-primary am-btn-block am-radius" data-for-gaq="商品管理-提交商品">
										<i class="am-icon-check-circle"></i> 提　交</button>
								</div>
							</div>
							<input id="hid_logo" type="hidden" value="{$shop_info->shop_image_id}" />
							<input id="hid_signboard" type="hidden"  value="{$shop_info->shop_signboard_image_id}" />
						</form>
					</div>
					<hr/>
				</div>
			</div>
			<!-- content end -->
		</div>
		<!--{template inc/footer_paul}-->
	</body>

</html>
<script type="text/javascript" src="{$BASE_V}assets/js/jquery.min.js"></script>
<script type="text/javascript" src="{$BASE_V}assets/js/amazeui.js"></script>
<script type="text/javascript">
	var index_url = '{MOBILE_URL}';
	var static_url = '{STATIC_URL}';
	var base_v = '{$BASE_V}';
	var php_self = '{PHP_SELF}';
	var payment_type = '{$shop_info->payment_type}';
	var refund_type = '{$shop_info->refund_type}';
	var is_guarantee_transaction = '{$shop_info->is_guarantee_transaction}';
	var stock_setting = '{$shop_info->stock_setting}';
	var shop_image_url = '{$shop_info->shop_image_url}';
	var shop_signboard_image_url = '{$shop_info->shop_signboard_image_url}';
	var goods_show_type = '{$shop_info->goods_show_type}';
	var shop_template_id='{$shop_info->shop_template_id}'
</script>
<script type="text/javascript" src="{STATIC_URL}js/fineuploader/all.fine-uploader-5.1.3.min.js"></script>
<script type="text/javascript" src="{$BASE_V}js/modal_html.js"></script>
<script type="text/javascript" src="{$BASE_V}js/common.js"></script>
<script src="{STATIC_URL}js/jquery-plugin/ui/minified/jquery.cookie-min.js" type="text/javascript"></script>
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
				<img src="" qq-max-size="500" class="qq-thumbnail-selector">
				<div class="uploadify-progress qq-progress-bar-container-selector">
					 <div class="uploadify-progress-bar qq-progress-bar-selector"></div>
					</div>
				<a style="width: 65px;" href="javascript:;" class="close-modal small js-remove-image qq-upload-cancel-selector am-btn am-btn-default am-btn-xs am-text-danger am-hide-sm-only"><span class="am-icon-trash-o"></span>取消</a>
			<!--<a href="javascript:;" class="close-modal small js-remove-image qq-upload-delete-selector">×</a>-->
			<!--<a class="qq-upload-retry-selector upload-retry" href="#">重试</a>-->
			</li>
			</span>
			<li class="fileinput-button">
				<a class="fileinput-button-icon js-fileupload-input fileupload qq-upload-button-selector am-btn am-btn-danger am-btn-sm" href="javascript:;"><i class="am-icon-cloud-upload"></i>请选择要上传的图片</a>
			</li>
			<div class="uploadify-progress-all qq-total-progress-bar-container-selector">
				<div class="uploadify-progress-bar qq-total-progress-bar-selector" id="qq-total-progress-bar"></div>
			</div>
		</ul>

	</div>
</script>
<script type="text/template" id="logo-template">
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
				<img src="" qq-max-size="500" class="qq-thumbnail-selector">
				<div class="uploadify-progress qq-progress-bar-container-selector">
					 <div class="uploadify-progress-bar qq-progress-bar-selector"></div>
					</div>
				<a style="width: 65px;" href="javascript:;" class="close-modal small js-remove-image qq-upload-cancel-selector am-btn am-btn-default am-btn-xs am-text-danger am-hide-sm-only"><span class="am-icon-trash-o"></span>取消</a>
			<!--<a href="javascript:;" class="close-modal small js-remove-image qq-upload-delete-selector">×</a>-->
			<!--<a class="qq-upload-retry-selector upload-retry" href="#">重试</a>-->
			</li>
			</span>
			<li class="fileinput-button">
				<a class="fileinput-button-icon js-fileupload-input fileupload qq-upload-button-selector am-btn am-btn-danger am-btn-sm" href="javascript:;"><i class="am-icon-cloud-upload"></i>请选择要上传的图片</a>
			</li>
			<div class="uploadify-progress-all qq-total-progress-bar-container-selector">
				<div class="uploadify-progress-bar qq-total-progress-bar-selector" id="qq-total-progress-bar"></div>
			</div>
		</ul>

	</div>
</script>
<script type="text/javascript" src="{$BASE_V}js/shop_detail.js"></script>
<script>
	$(function() {
		$('#doc-my-tabs').tabs();
	})
</script>