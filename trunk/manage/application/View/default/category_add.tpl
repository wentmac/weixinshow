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
					<div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">分类管理</strong></div>
				</div>
				<hr/>
				<div class="am-container" style="margin-left: 0">
					<form class="am-form am-form-horizontal">
						<div class="am-form-group">
							<label for="title" class="am-u-sm-2 am-form-label">分类名称</label>
							<div class="am-u-sm-7 am-u-end">
								<input class="am-radius" type="text" id="cat_name" name="cat_name" value="{$editinfo->cat_name}" placeholder="分类名称">
							</div>
							<small id="sm_title">这将是它在站点上显示的名字。</small>
						</div>

						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">内容模型</label>
							<div class="am-u-sm-6 am-u-end">							
								<div class="am-input-group">
									<select id="channeltype" class="am-dropdown" style="width: 150px;" name="channeltype">
             							{$channeltype_option}
            						</select>
            						
								</div>
							</div>	
							<small>栏目模型分类(article,image)</small>						
						</div>																											
						
						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">别名</label>
							<div class="am-u-sm-8 am-u-end">
								<input class="am-radius" type="text" id="category_nicename" name="category_nicename" placeholder="别名是对于 URL 友好的一个别称。它通常为小写并且只能包含字母，数字和连字符（-）。" value="{$editinfo->category_nicename}">
							</div>
							<small></small>
						</div>

						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">父级分类</label>
							<div class="am-u-sm-6 am-u-end">							
								<div class="am-input-group">
									<select id="cat_pid" name="cat_pid" class="am-dropdown" style="width: 150px;">
             							{$category_tree_list}
            						</select>
            						
								</div>
							</div>	
							<small>分类目录，和标签不同，它可以有层级关系。</small>						
						</div>			
						
						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">关键字</label>
							<div class="am-u-sm-8 am-u-end">
								<input class="am-radius" type="text" id="category_keywords" name="category_keywords" placeholder="关键字" value="{$editinfo->category_keywords}">
							</div>
							<small>keywords</small>
						</div>										

						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">描述</label>
							<div class="am-u-sm-8 am-u-end">								
								<textarea class="10" rows="3" id="category_description" name="category_description">{$editinfo->category_description}</textarea>
							</div>
							<small>description</small>
						</div>

						
						<div class="am-form-group">
							<label class="am-u-sm-2 am-form-label">描述</label>
							<div class="am-u-sm-10 am-u-end">
								<textarea class="am-radius editor" style="width: 100%; height:700px;" id="category_content" name="category_content" rows="25" placeholder="某个分类的详细说明">{$editinfo->category_content}</textarea>
							</div>
							<small class="am-text-warning"></small>
						</div>

						
						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">分类排序</label>
							<div class="am-u-sm-3 am-u-end">
								<input class="am-radius" type="text" id="cat_order" name="cat_order" value="{$editinfo->cat_order}">								
							</div>
							<small class="am-text-warning">越大越靠前</small>
						</div>

						
						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">前台链接文件</label>
							<div class="am-u-sm-3 am-u-end">
								<input class="am-radius" type="text" id="urlfile" name="urlfile" value="{$editinfo->urlfile}">								
							</div>
							<small class="am-text-warning">没有特别需求,请不用填写这里</small>
						</div>						
				
						<div class="am-form-group">
							<label for="" class="am-u-sm-2 am-form-label">前台导航条显示</label>
							<div class="am-u-sm-3 am-u-end">							
								<div class="am-input-group">
									<select id="nav_show" name="nav_show" class="am-dropdown" style="width: 150px;">
             							{$nav_show_array_option}
            						</select>
            						
								</div>
							</div>	
							<small class="am-text-warning">是否在前台导航条中显示</small>						
						</div>

						<hr>
						<div class="am-form-group" id="loading_box">
							<label for="" class="am-u-sm-2 am-form-label"></label>
							<div class="am-u-sm-2 am-u-end">
								<input type="hidden" value="{$cat_id}" name="cat_id" id="cat_id" />
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
<script type="text/javascript" src="{$BASE_V}js/category/detail.js?v=20151129"></script>		
<script type="text/javascript" src="{STATIC_URL}js/xheditor-1.2.2/xheditor-1.2.2.min.js?v=20150822"></script>
<script type="text/javascript" src="{STATIC_URL}js/xheditor-1.2.2/xheditor_lang/zh-cn.js"></script>
<script language="javascript">
//以后jquery中的都用jq代替即可。
var editor;
function pageInit()
{
	var editor=$('#category_content').xheditor({
		tools:'full',
		upImgUrl:"manage.php?m=tool.uploadImg&action=category",
		upImgExt:"jpg,jpeg,gif,png",
		loadCSS:'<style>pre{margin-left:2em;border-left:3px solid #CCC;padding:0 1em;}</style>'		
	});
}
$(pageInit);
$.AMUI.progress.configure({ parent: '#loading_box' });
</script>