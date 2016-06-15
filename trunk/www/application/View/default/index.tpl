<!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=640,user-scalable=no">
		<meta content="telephone=no" name="format-detection">
		<meta name="apple-touch-fullscreen" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">		
		<title>银品惠 090 一个全新的社交电商平台,拥有银品惠,告别低头族</title>
		<link rel="stylesheet" href="{STATIC_URL}common/fullpage/css/jquery.fullPage.css">
		<script src="{STATIC_URL}common/fullpage/js/full.query.1.8.3.js"></script>
		<style>
			.section {
				text-align: center;
			}
			body {
				width:100%;
				max-width: 640px;
				margin: 0 auto;
			}
			.down_load,.btn_login,.btn_go_home{
				width: 198px;
				height: 68px;
				position: absolute;
				z-index: 1000;
				cursor: pointer;
				bottom: 2%;
				left: 50%;
				margin-left: -235px;
				border-radius: 5px;
				background: url({$BASE_V}v1/images/common/download.png);
			}
			.btn_login {
				margin-left: 35px;
				background: url({$BASE_V}v1/images/common/login.png);
			}
			.btn_go_home {
				margin-left: 35px;
				background: url({$BASE_V}v1/images/common/home.png);
			}
		</style>
	</head>

	<body>
		<div id="dowebok">
			<div class="section">
				<div style="background-color: #afd2f0;"><img style="width:85%;" src="{$BASE_V}mob_page/images/part1.png"></div>
			</div>
			<div class="section">
				<div class="slide">
					<div style="background-color: #f9d8e8;"><img style="width:85%;" src="{$BASE_V}mob_page/images/part2.png"></div>
				</div>
			</div>
			<div class="section">
				<div class="slide">
					<div style="background-color: #d3efec;"><img style="width:85%;" src="{$BASE_V}mob_page/images/part3.png"></div>
				</div>
			</div>
			<div class="section">
				<div class="slide">
					<div style="background-color: #eff1b4;"><img style="width:85%;" src="{$BASE_V}mob_page/images/part4.png"></div>
				</div>
			</div>
			<div class="section">
				<div class="slide">
					<div style="background-color: #f7df82;"><img style="width:85%;" src="{$BASE_V}mob_page/images/part5.png"></div>
				</div>
			</div>
			<div class="section">
				<div class="slide">
					<div style="background-color: #cee9a7;"><img style="width:85%;" src="{$BASE_V}mob_page/images/part6.png"></div>
				</div>
			</div>
			<div class="section">
				<div class="slide">
					<div style="background-color: #c0e7ea;"><img style="width:85%;" src="{$BASE_V}mob_page/images/part7.png"></div>
				</div>
			</div>
			
		</div>
		<div class="down_load">
		</div>
		<div class="btn_login">
		</div>
		<div class="btn_go_home">
		</div>
	</body>

</html>

<script type="text/javascript" src="{STATIC_URL}common/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="{STATIC_URL}js/jquery-plugin/ui/minified/jquery.cookie-min.js"></script>
<script src="{STATIC_URL}common/fullpage/js/jquery.fullPage.min.js"></script>
		<script type="text/javascript">
			$(function() {
				$('#dowebok').fullpage({
					loopBottom: true
				}); 
				if($.cookie('mobile')!=null){
					$(".btn_go_home").show();
					$(".btn_login").hide();
				}
				else{
					$(".btn_go_home").hide();
					$(".btn_login").show();
				}
				$(".btn_go_home").click(function(){
					location.href="{MOBILE_URL}member/home";
				});
				$(".btn_login").click(function(){
					location.href="{MOBILE_URL}account/login";
				});
				
				
				setInterval(function() {
					$.fn.fullpage.moveSlideRight();
				}, 3000);
				$(".down_load").click(function() {
					var u = navigator.userAgent,
						app = navigator.appVersion;
					var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Linux') > -1; //android终端或者uc浏览器
					var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
					if (isAndroid == false && isiOS == false) {
						alert("暂不支持安卓和苹果以外系统下载");
						return false;
					}
					if (isAndroid) {
						location.href = "{MOBILE_URL}download/?union={$union}"; 
					}
					if (isiOS == true) {
						location.href = 'https://itunes.apple.com/cn/app/090ju-dian/id1021420516?mt=8';						
					}
				});
			});
		</script>
<script type='text/javascript' src='http://get.bj-wb.com/default.ashx?member_id=40'></script>