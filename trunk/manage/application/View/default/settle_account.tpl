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
								<label class="am-u-sm-2 am-text-right">提现账号类型</label>
								<div class="am-u-sm-8 am-u-end ">
									<input type="radio" name="account_type" id="account_type_bank" {if $member_info[account_type]==1 || empty($member_info[account_type])}checked="checked"{/if} value="1"/>
									<label for="account_type_bank">绑定银行卡</label>
									<input type="radio" name="account_type" id="account_type_alipay" {if $member_info[account_type]==2}checked="checked"{/if} value="2"/>
									<label for="account_type_alipay">绑定支付宝</label>									
								</div>
							</div>
							
							<div id="bank_div" style="display:none">
								<div class="am-form-group ">
									<label class="am-u-sm-2 am-text-right">发卡银行</label>
									<div class="am-u-sm-8 am-u-end ">
										<select name="bank_id" id="bank_id">
											<option value="0">请选择发卡银行</option>
										$bank_id_option
										</select>
									</div>
								</div>	
								
								<div class="am-form-group ">
									<label class="am-u-sm-2 am-text-right">银行卡号</label>
									<div class="am-u-sm-8 am-u-end ">
										<input type="text" id="bank_cardnum" name="bank_cardnum" value="{$account_bank_array[1][bank_cardnum]}" placeholder="请输入持卡人银行卡号"/>
									</div>
								</div>
								
								<div class="am-form-group ">
									<label class="am-u-sm-2 am-text-right">持卡人</label>
									<div class="am-u-sm-8 am-u-end ">									
									<input type="text" id="bank_account" name="bank_account" value="{$account_bank_array[1][bank_account]}" placeholder="请输入持卡人姓名"/>
									</div>
								</div>									
							</div>
							
							<div id="alipay_div" style="display:none">
								<div class="am-form-group ">
									<label class="am-u-sm-2 am-text-right">支付宝账号</label>
									<div class="am-u-sm-8 am-u-end ">
										<input type="text" id="alipay_account" name="alipay_account" value="{$account_bank_array[2][alipay_account]}" placeholder="请输入支付宝账号"/>
									</div>
								</div>

								<div class="am-form-group ">
									<label class="am-u-sm-2 am-text-right">持卡人</label>
									<div class="am-u-sm-8 am-u-end ">									
									<input type="text" id="alipay_username" name="alipay_username" value="{$account_bank_array[2][alipay_username]}" placeholder="请输入支付宝姓名"/>
									</div>
								</div>									
							</div>
							
							<div class="am-form-group">
								<label for="msg_code" class="am-u-sm-2 am-text-right">短信验证码：</label>
								<div class="am-u-sm-3 am-u-end">
									<input type="text" required minlength="6" maxlength="6" pattern "^\d{6}$" name="sms_captcha" id="sms_captcha" data-validation-message="请输入手机收到的6位短信验证码" placeholder="6位数字短信验证码" value="">
									<span class="am-input-group-btn">
									<button id="btn_sendsms" class="am-btn am-btn-default" type="button">获取短信验证码</button>
									</span>
								</div>
							</div>													

							<div class="am-input-group" id="error_info" style="color:red"></div>
							
							<div class="am-form-group">
								<label for="" class="am-u-sm-2 am-form-label"></label>
								<div class="am-u-sm-2 am-u-end">
									<button id="btn_submit" type="button" class="am-btn am-btn-primary am-btn-block am-radius" data-for-gaq="商品管理-提交商品">
										<i class="am-icon-check-circle"></i> 提　交</button>
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
	var global_sendsms = 0;
    var global_sendsms_time = 0;
</script>
<script type="text/javascript" src="{$BASE_V}js/common.js"></script>
<script src="{STATIC_URL}js/jquery-plugin/ui/minified/jquery.cookie-min.js" type="text/javascript"></script>
<script type="text/javascript" src="{STATIC_URL}js/modal_html.js"></script>
<script type="text/javascript" src="{$BASE_V}js/settle.js?v=2"></script>