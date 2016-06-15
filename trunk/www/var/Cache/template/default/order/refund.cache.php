<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\order/refund.tpl', 'D:\Web\Work\www.090.cn\trunk\admin\application\View\default\order\refund.tpl', 1444374017)
;?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="<?php echo $BASE_V;?>layout.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?php echo STATIC_URL; echo APP_MOBILE_NAME; ?>/default/v1/css/common/base.css">
<link rel="stylesheet" type="text/css" href="<?php echo STATIC_URL; echo APP_MOBILE_NAME; ?>/default/v1/css/user/user.css">
<title>TBlog博客系统</title>
<script src="<?php echo STATIC_URL; ?>js/tools.js" type="text/javascript"></script>
</head>
	<body>
		<header id="common_hd" class="c_txt rel">
			<h1 class="hd_tle">申请退款</h1>
		</header>
		<div class="refund_wrap" id="refundMoney">
			<div class="money_main">
				<ul>
					<li><span><em>*</em>是否退货</span>
						<select id="needProduct" name="needProduct" tabindex="1">
							<option class="esp" value="0">请选择是否退货</option>
							<?php echo $refund_service_status_option;?>
						</select>
					</li>
					<li><span><em>*</em>退款原因</span>
						<select id="refundReason" name="refundReason" tabindex="2">
							<option class="esp" value="0">请选择退款原因</option>
							<?php echo $refund_service_reason_option;?>
						</select>
					</li>
					<li><span><em>*</em>退款金额</span>
						<input type="number" placeholder="请输入退款金额" id="priceNeed"> <em id="refundPrice"></em>
					</li>
				</ul>
			</div>
		</div>
		
		<footer class="footer">
			<div class="footerMain">
				<p class="refundAction"><a class="btnok c_txt abs for_gaq" data-for-gaq="申请退款" id="btnOk">提交</a> <a class="btnok c_txt abs for_gaq" style="display:none" data-for-gaq="申请退款" id="refundOK">确认退款</a>
				</p>
			</div>
		</footer>		
	</body>
</html>
<script src="<?php echo STATIC_URL; ?>js/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script language="javascript">
var index_url = '<?php echo INDEX_URL; ?>';
var mobile_url = '<?php echo MOBILE_URL; ?>';
var static_url = '<?php echo STATIC_URL; ?>';
var base_v = '<?php echo $BASE_V;?>';
var php_self = '<?php echo PHP_SELF; ?>';
var global_order_sn='<?php echo $order_sn;?>';
var global_order_goods_id='<?php echo $order_goods_id;?>';
jq = jQuery.noConflict();

jq(document).ready(function(){	
	jq("#btnOk").click(function(){
		refund.save();		
	});
	jq("#hd_back").click(function(){
		window.close();
	});
});
var refund={	
	save : function(){
		if ( jq("#needProduct").val() == "0" ) {
			alert("请选择是否退货" );
			return false;
		}
		if ( jq("#refundReason").val() == "0" ) {
			alert("请选择退款原因" );
			return false;
		}
		if ( jq("#priceNeed").val() == "" ) {
			alert("请输入金额" );
			return false;
		}
		jq.ajax({
			type:"post",
			url:php_self+ "?m=order.refund_save",
			data:{
				sn:global_order_sn,
				order_goods_id:global_order_goods_id,
				money:jq("#priceNeed").val(),
				refund_service_status:jq("#needProduct option:selected").val(),
				refund_service_reason:jq("#refundReason option:selected").val()
			},
			cache:false,
			success:function(data){
				if ( data.success == true ) {
					alert("提交成功");
					window.close();
				} else {
					alert(data.message);
				}
			},
			error:function(data){
				alert(data.message);
			}			
		});
	}
}

</script>