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
		<link href="{$BASE_V}css/goods_add.css" rel="stylesheet" type="text/css">		
		<link href="{$BASE_V}css/goods_edit.css" type="text/css" rel="stylesheet">		
	</head>

	<body>
		<!--{template inc/header_paul}-->
		<div class="am-cf admin-main">
			<!--{template inc/sidebar_paul}-->
			<!-- content start -->
			<div class="admin-content" id="i_do_wrap">
				<div class="am-cf am-padding">
					<div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">文章管理</strong></div>
				</div>
				<hr/>
				<div class="am-container" style="margin-left: 0">
					<form class="am-form am-form-horizontal">
						<div class="am-form-group">
							<label for="title" class="am-u-sm-2 am-form-label">标题</label>
							<div class="am-u-sm-8 am-u-end">
								<input class="am-radius" type="text" id="title" name="title" value="{$editinfo->title}" placeholder="文章标题">
							</div>
							<small id="sm_title"></small>
						</div>

						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">分类</label>
							<div class="am-u-sm-8 am-u-end">							
								<div class="am-input-group">
									<select id="cat_id" class="am-dropdown" style="width: 150px;" name="cat_id">
										<option value="0">-根分类-</option>
             							{$category_list_option}
            						</select>
            						
								</div>
							</div>							
						</div>																											
						
						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">关键字</label>
							<div class="am-u-sm-8 am-u-end">
								<input class="am-radius" type="text" id="keywords" name="keywords" placeholder="关键字" value="{$editinfo->keywords}">
							</div>
						</div>

						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">描述</label>
							<div class="am-u-sm-8 am-u-end">								
								<textarea class="10" rows="3" id="description" name="description">{$editinfo->description}</textarea>
							</div>
						</div>

						
						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">排序</label>
							<div class="am-u-sm-3 am-u-end">
								<input class="am-radius" type="text" id="article_order" name="article_order" value="{$editinfo->article_order}">								
							</div>
							<small class="am-text-warning">越大越靠前</small>
						</div>

						
						<div class="am-form-group">
							<label class="am-u-sm-2 am-form-label">描述</label>
							<div class="am-u-sm-10 am-u-end">
								<textarea class="am-radius editor" style="width: 100%; height:700px;" id="content" name="content" rows="25" placeholder="商品简要文字描述">{$editinfo->content}</textarea>
							</div>
						</div>

						
						<div class="am-form-group" id="upload_div">
							<label for="" class="am-u-sm-2 am-form-label">上传图</label>
							<div class="am-u-sm-8 am-u-end">
								<div class="am-input-group">
									<span id="thumb_preview"><img src="{$editinfo->article_image_url}"/></span>
								</div>							
								<div class="am-input-group">
									<span class="am-input-group-label">地&nbsp;&nbsp;&nbsp;址：</span>
									<input type="text" class="am-form-field" size="96" value="" id="thumb" readonly="readonly">
								</div>
								<input type="hidden" value="" name="article_image_id" id="article_image_id" value="{$editinfo->article_image_id}"> 
								<div class="am-input-group">
									<span class="am-input-group-label">上&nbsp;&nbsp;&nbsp;传：</span>
									<input type="file" class="am-btn am-btn-default" onchange="image_preview('thumb',this.value,1)" id="thumb_upload" name="thumb_upload">
									<input type="button" class="am-btn am-btn-success" value="上传" onclick="return ajaxFileUpload('thumb_upload','/{PHP_SELF}?m=tool.uploadImageByAjax&filename=thumb_upload&action=article','#thumb_loading', 'thumb', 'thumb_preview','article_image_id');" id="thumbupload" name="thumbupload">
									<input type="button" class="am-btn am-btn-danger" value="删除" onclick="delimg('upload_div')" />
								</div>		
								<img style="display:none;" src="{STATIC_URL}js/loading.gif" id="thumb_loading">
							</div>
						</div>						
						<hr>
						<div class="am-form-group" id="loading_box">
							<label for="" class="am-u-sm-2 am-form-label"></label>
							<div class="am-u-sm-2 am-u-end">
								<input type="hidden" value="{$article_id}" name="article_id" id="article_id" />
								<button id="submit_i_do_item" type="button" class="am-btn am-btn-primary am-btn-block am-radius" data-for-gaq="保存">
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
<script type="text/javascript">
var index_url = '{MOBILE_URL}';
var static_url = '{STATIC_URL}';
var base_v = '{$BASE_V}';
var php_self = '{PHP_SELF}';
var postField = new Object();
</script>
<script src="{STATIC_URL}js/jquery-plugin/ui/minified/jquery.cookie-min.js" type="text/javascript"></script>		
<script type="text/javascript" src="{$BASE_V}js/common.js"></script>
<script type="text/javascript" src="{$BASE_V}js/modal_html.js"></script>
<script type="text/javascript" src="{$BASE_V}js/article/detail.js?v=20151129"></script>		
<script type="text/javascript" src="{STATIC_URL}js/xheditor-1.2.2/xheditor-1.2.2.min.js?v=20150822"></script>
<script type="text/javascript" src="{STATIC_URL}js/xheditor-1.2.2/xheditor_lang/zh-cn.js"></script>
<script type="text/javascript" src="{STATIC_URL}js/ajaxfileupload.js"></script>
<script type="text/javascript" src="{STATIC_URL}js/ThumbAjaxFileUpload.js"></script>
<script language="javascript">
//以后jquery中的都用jq代替即可。
var editor;
function pageInit()
{
	var editor=$('#content').xheditor({
		tools:'full',
		upImgUrl:"manage.php?m=tool.uploadImg&action=article",
		upImgExt:"jpg,jpeg,gif,png",
		loadCSS:'<style>pre{margin-left:2em;border-left:3px solid #CCC;padding:0 1em;}</style>'		
	});
}
$(pageInit);
$.AMUI.progress.configure({ parent: '#loading_box' });
</script>