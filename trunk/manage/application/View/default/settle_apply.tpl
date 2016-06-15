<!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8">
		<link rel="apple-touch-icon-precomposed" href="/i/app-icon72x72@2x.png">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>我要提现</title>
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
					<div class="am-fl"><strong class="am-text-primary am-text-lg">我要提现</strong></div>
				</div>
				<hr/>
				<div class="am-u-sm-12">
					<div class="am-u-sm-9 am-container " style="margin-left: 0 ">
						<form class="am-form am-form-horizontal ">							
							<div class="am-form-group ">
								<label class="am-u-sm-2 am-text-right">账户提现的余额</label>
								<div class="am-u-sm-8 am-u-end ">
									{$member_info[current_money]}
								</div>
							</div>
							
							<div class="am-form-group">
								<label class="am-u-sm-2 am-text-right">提现（元）</label>
								<div class="am-u-sm-3 am-u-end ">
									<input type="text" id="money" name="money" placeholder="请输入提现金额" size=5/>
								</div>
							</div>
							
							<div class="am-form-group ">
								<label class="am-u-sm-2 am-text-right">提现账号</label>
								<div class="am-u-sm-8 am-u-end ">
									<!--{if empty($member_info[account_type])}-->
									<a href="{MOBILE_URL}{PHP_SELF}?m=settle.account" title="绑定提现账户" style="text-decoration:underline">绑定提现账户></a>
									<!--{else}-->
									<a href="{MOBILE_URL}{PHP_SELF}?m=settle.account" title="选择绑定的提现账户" style="text-decoration:underline">
									<!--{if $member_info[account_type]==1}-->
									银行卡（{$account_bank_array[1][bank_cardnum]}）
									<!--{elseif $member_info[account_type]==2}-->
									支付宝（{$account_bank_array[2][alipay_account]}）
									<!--{/if}-->>
									</a>
									<!--{/if}-->
								</div>
							</div>							
							
							<div class="am-input-group" id="error_info" style="color:red"></div>
							<div class="am-form-group">
								<label for="" class="am-u-sm-2 am-form-label"></label>
								<div class="am-u-sm-2 am-u-end">
									<button id="apply_btn_submit" type="button" class="am-btn am-btn-primary am-btn-block am-radius" data-for-gaq="商品管理-提交商品">提　交</button>
								</div>
							</div>														
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
	var global_account_type = {$member_info[account_type]};
	var global_current_money = {$member_info[current_money]};
</script>
<script type="text/javascript" src="{$BASE_V}js/common.js"></script>
<script src="{STATIC_URL}js/jquery-plugin/ui/minified/jquery.cookie-min.js" type="text/javascript"></script>
<script type="text/javascript" src="{STATIC_URL}js/modal_html.js"></script>
<script type="text/javascript" src="{$BASE_V}js/settle.js"></script>