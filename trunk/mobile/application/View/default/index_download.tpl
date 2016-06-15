<!DOCTYPE html>
<html class="" lang="zh-CN">

	<head>
		<meta charset="utf-8">
		<meta name="keywords" content="{$config[cfg_webname]},移动电商服务平台" />
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">
		<meta name="format-detection" content="telephone=no">
		<meta http-equiv="cleartype" content="on">

		<title>{$config[cfg_webname]}App 下载</title>

		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<script>
			 //			location.href = "{INDEX_URL}download/090.apk";
		</script>
		<link href="{$BASE_V}v1/css/index/app_download.css" type="text/css" rel="stylesheet">
		<!-- 去除wap.css -->

		<body class=" ">

			<div class="container container-app-download" style="min-height: 569px;">
				<div class="app-download-logo"></div>
				<div class="app-download-applogo"></div>
				<div class="app-download-btn-wrap">
					<a id="a_down" class="js-download-link">
						<button class="btn btn-block btn-green btn-download" type="button">下载安装</button>
					</a>
				</div>
				<hr class="app-download-hr">
				<p class="app-download-intro">一个全新的社交电商平台,拥有{$config[cfg_webname]},告别低头族</p>
				<div class="app-download-intro-btn-area" style="display:none">
					<a id="down_load">
						<button class="btn btn-intro" type="button">免费注册，0元开店</button>
					</a>
				</div>

				<div id="wxcover"></div>
			</div>

			<div class="footer">
				<p>© 090.cn</p>
			</div>
			<script type="text/javascript" src="{STATIC_URL}common/assets/js/jquery.min.js"></script>
			
			<script type="text/javascript">
				$(function() {
					
					$(".btn-download").click(function() {
						if (is_weixin()) {
							$("#a_down").attr("href", "{$qq_download_url}");
						} else {
							var down_url="";
							$.getJSON("http://api.090.cn/ver.php?type=android",function(data){
								
								location.href=data.url;
								});
							
						}
						$.ajax({
							type: "get",
							url: "{MOBILE_URL}index.php?m=tool.download_statistic",
							data:{
								union:"{$union}"
							},
							success: function(data) {}
						});
					});
				});
				 //是否使用微信支付
				function is_weixin() {
					var ua = navigator.userAgent.toLowerCase();
					if (ua.match(/MicroMessenger/i) == "micromessenger") {
						return true;
					} else {
						return false;
					}
				}
			</script>
		</body>

</html>