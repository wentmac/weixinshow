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
					<div class="am-fl"><strong class="am-text-primary am-text-lg"><a href="{PHP_SELF}?m=poster">广告位管理</a> > {$poster_custom_array[$poster_name]}</strong></div>
				</div>

				<div class="am-u-md-12">
					<div class="am-panel am-panel-default">
						<div class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-2'}"><font class="bill_type_info">商城广告位</font><span class="am-icon-chevron-down am-fr"></span></div>
						<div id="collapse-panel-2" class="am-in">														
	<form name="modform" id="forms" action="{PHP_SELF}?m=poster.save" method="post">							
        
		<!--{if !empty($imgurl_array)}-->
        {loop $imgurl_array $k $v}
        <div id="upload_div_$k">
			<div class="am-input-group">
				<span id="thumb_preview{$k}"><img src="$v" width="600" height="120" style="margin-bottom:6px"></span>
			</div>
			<div class="am-input-group">
				<span class="am-input-group-label">链&nbsp;&nbsp;&nbsp;接：</span>
				<input type="text" class="am-form-field" size="96" value="{$thumburl_array[$k]}" name="thumburl[]" placeholder="goods/12314.html">
			</div>
			<div class="am-input-group">
				<span class="am-input-group-label">描&nbsp;&nbsp;&nbsp;述：</span>
				<input type="text" class="am-form-field" size="96" value="{$thumbtitle_array[$k]}" name="thumbdes[]" placeholder="广告图片的描述内容">
			</div>
			<div class="am-input-group">
				<span class="am-input-group-label">地&nbsp;&nbsp;&nbsp;址：</span>
				<input type="text" class="am-form-field" size="96" value="{$imgid_array[$k]}" id="thumb{$k}" readonly="readonly">
			</div>
			<div class="am-input-group">
				<span class="am-input-group-label">自定义：</span>
				<input type="text" class="am-form-field" size="96" value="{$self_field_array[$k]}" name="self_field[]">
			</div>	
			<div class="am-input-group">
				<span class="am-input-group-label">排&nbsp;&nbsp;&nbsp;序：</span>
				<input type="text" class="am-form-field" size="96" value="{$sort_array[$k]}" name="sort[]" id="sort{$k}" placeholder="越大越靠前">
			</div>			
			<input type="hidden" value="{$imgid_array[$k]}" name="thumb[]" id="imgid_{$k}">
			<div class="am-input-group">
				<span class="am-input-group-label">上&nbsp;&nbsp;&nbsp;传：</span>
				<input type="file" class="am-btn am-btn-default" onchange="image_preview('thumb{$k}',this.value,1)" id="thumb_upload{$k}" name="thumb_upload{$k}">
				<input type="button" class="am-btn am-btn-success" value="上传" onclick="return ajaxFileUpload('thumb_upload{$k}','/{PHP_SELF}?m=tool.uploadImageByAjax&filename=thumb_upload{$k}&action=poster&size=600x200','#thumb_loading{$k}', 'thumb{$k}', 'thumb_preview{$k}','imgid_{$k}');" id="thumbupload{$k}" name="thumbupload{$k}">
				<input type="button" class="am-btn am-btn-danger" value="删除" onclick="delimg('upload_div_{$k}')" />
			</div>		
			<img style="display:none;" src="{STATIC_URL}js/loading.gif" id="thumb_loading{$k}">
        </div>		
        <!--{/loop}-->
        <!--{else}-->
        <div id="upload_div">
			<div class="am-input-group">
				<span id="thumb_preview"></span>
			</div>
			<div class="am-input-group">
				<span class="am-input-group-label">链&nbsp;&nbsp;&nbsp;接：</span>
				<input type="text" class="am-form-field" size="96" value="" name="thumburl[]" placeholder="goods/12314.html">
			</div>
			<div class="am-input-group">
				<span class="am-input-group-label">描&nbsp;&nbsp;&nbsp;述：</span>
				<input type="text" class="am-form-field" size="96" value="" name="thumbdes[]" placeholder="广告图片的描述内容">
			</div>
			<div class="am-input-group">
				<span class="am-input-group-label">地&nbsp;&nbsp;&nbsp;址：</span>
				<input type="text" class="am-form-field" size="96" value="" id="thumb" readonly="readonly">
			</div>
			<div class="am-input-group">
				<span class="am-input-group-label">自定义：</span>
				<input type="text" class="am-form-field" size="96" value="" name="self_field[]">
			</div>	
			<div class="am-input-group">
				<span class="am-input-group-label">排&nbsp;&nbsp;&nbsp;序：</span>
				<input type="text" class="am-form-field" size="96" value="" name="sort[]" placeholder="越大越靠前">
			</div>			
			<input type="hidden" value="" name="thumb[]" id="imgid_1"> 
			<div class="am-input-group">
				<span class="am-input-group-label">上&nbsp;&nbsp;&nbsp;传：</span>
				<input type="file" class="am-btn am-btn-default" onchange="image_preview('thumb',this.value,1)" id="thumb_upload" name="thumb_upload">
				<input type="button" class="am-btn am-btn-success" value="上传" onclick="return ajaxFileUpload('thumb_upload','/{PHP_SELF}?m=tool.uploadImageByAjax&filename=thumb_upload&action=poster&size=600x200','#thumb_loading', 'thumb', 'thumb_preview','imgid_1');" id="thumbupload" name="thumbupload">
				<input type="button" class="am-btn am-btn-danger" value="删除" onclick="delimg('upload_div')" />
			</div>		
			<img style="display:none;" src="{STATIC_URL}js/loading.gif" id="thumb_loading">
        </div>        
		<!--{/if}-->
        <div id="add_seriesArea">
		<button type="button" class="am-btn am-btn-warning" id="add">添加</button>		
        </div>   
											
						
						<div class="am-form-group">
							<input type="hidden" name="poster_name" value="{$poster_name}"/>
							<button type="submit" class="am-btn am-btn-success am-round">自定义广告位保存</button>
						</div>
		</form>
		
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
	var index_url = '{INDEX_URL}';
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
<script src="{STATIC_URL}js/jquery-plugin/ui/minified/jquery.cookie-min.js" type="text/javascript"></script>

<script type="text/javascript" src="{$BASE_V}js/modal_html.js"></script>

<script type="text/javascript" src="{STATIC_URL}js/ajaxfileupload.js"></script>
<script type="text/javascript" src="{STATIC_URL}js/ThumbAjaxFileUpload.js"></script>
<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.templates/beta1/jquery.tmpl.min.js"></script>
<script type="text/javascript" src="{$BASE_V}js/poster_detail.js"></script>