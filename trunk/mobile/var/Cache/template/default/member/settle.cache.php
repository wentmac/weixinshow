<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\member/settle.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\mobile\application\View\default\member\settle.tpl', 1459174425)
;?>
<!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">
		<meta content="telephone=no" name="format-detection">
		<meta name="apple-touch-fullscreen" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<title>账单列表 - <?php echo $config['cfg_webname'];?></title>
		<link href="<?php echo $BASE_V;?>css/common/base.css" type="text/css" rel="stylesheet">
		<link href="<?php echo $BASE_V;?>css/user/user.css" type="text/css" rel="stylesheet">
		<style>						 
			  .orderListType ul li{width:25%;}
.shopList .p-sum {    
    border-bottom: none;    
}			  
.main {
    padding-bottom: 0px;
}
.nickname{
	float: right;
}
.shopList .p-actions {
    height: auto;
    position: relative;
    padding-top: 1em;
    margin-left: 10px;
}
		</style>
	</head>

	<body>
		<section class="main">
		    <header id="common_hd" class="c_txt rel">
		        <a id="hd_back" class="abs comm_p8" href="<?php echo $referer_url;?>">返回</a>
		        <a id="common_hd_logo" class="t_hide abs common_hd_logo">提现历史</a>
		        <h1 class="hd_tle">提现历史</h1>
		        <a id="hd_enterShop" class="hide abs" href="<?php echo MOBILE_URL; ?>member/home" style="display: block;"> <span id="hd_enterShop_img" class="abs"> <img class="block" src="" width="32" height="32" style="display: block;"> </span>会员中心</a>
		    </header>

			<div class="orderListTypeHolder">&nbsp;</div>
			<div class="orderListType" id="tabPlus">
				<ul>
					<li><a id="all" class="cur">全部</a></li>
					<li><a id="verify">申请提现</a></li>
					<li><a id="success">提现成功</a></li>
					<li><a id="fail">提现失败</a></li>					
				</ul>
			</div>

<div class="orderList" id="js_settle_list">
	<div style="display:block"> 
		<nav class="shopList">   			
			<nav class="probody maxheight">
			     <a class="product" href="#">       
			     <div class="flex">         			     	
			     	<div class="flex-auto p-details">           
			     		<div class="flex">             
			     			<div class="flex-auto">
			     				<span class="p-name color-dark">			     					
			     					<div>申请提现时间：2015-07-08 17:30:47</div>
			     					<div>提现微信支付账号:1000018301201603270826692776</div>			     					
			     				</span>			     				
			     			</div>
			     			<div class="flex-item">
			     			    <div class="color-dark p-desc">￥0.02</div>               
			     			    <div class="color-grey p-desc">申请提现</div>             
			     			</div>         
			     		</div>
			     	</div>
			     </div>
			     </a>
			</nav>
			<p class="p-sum p-actions clearfix">
				<span>打款时间：2015-07-08 17:30:47</span>
				<span class="nickname">微信账号：依然特雷西</span>
			</p>   			
		</nav>

		<nav class="shopList">   			
			<nav class="probody maxheight">
			     <a class="product" href="http://dev.yph.weixinshow.com/member/order.detail?sn=2016030421542664775">       
			     <div class="flex">         			     	
			     	<div class="flex-auto p-details">           
			     		<div class="flex">             
			     			<div class="flex-auto">
			     				<span class="p-name color-dark">申请提现时间：2015-07-08 17:30:47</span>			     				
			     			</div>
			     			<div class="flex-item">
			     			    <div class="color-dark p-desc">￥0.02</div>               
			     			    <div class="color-grey p-desc">申请提现</div>             
			     			</div>         
			     		</div>
			     	</div>
			     </div>
			     </a>
			</nav>
			<p class="p-sum p-actions clearfix">
				<span>打款时间：2015-07-08 17:30:47</span>
				<span class="nickname">微信账号：依然特雷西</span>
			</p>   			
		</nav>

		<nav class="shopList">   			
			<nav class="probody maxheight">
			     <a class="product" href="http://dev.yph.weixinshow.com/member/order.detail?sn=2016030421542664775">       
			     <div class="flex">         			     	
			     	<div class="flex-auto p-details">           
			     		<div class="flex">             
			     			<div class="flex-auto">
			     				<span class="p-name color-dark">申请提现时间：2015-07-08 17:30:47</span>			     				
			     			</div>
			     			<div class="flex-item">
			     			    <div class="color-dark p-desc">￥0.02</div>               
			     			    <div class="color-grey p-desc">申请提现</div>             
			     			</div>         
			     		</div>
			     	</div>
			     </div>
			     </a>
			</nav>
			<p class="p-sum p-actions clearfix">
				<span>打款时间：2015-07-08 17:30:47</span>
				<span class="nickname">微信账号：依然特雷西</span>
			</p>   			
		</nav>			

		</div></div>
		</section>
		<p id="scroll_loading_txt" class="loading hide">&nbsp;</p>		
	</body>

</html>

<script type="text/javascript">
	var index_url = '<?php echo INDEX_URL; ?>';
	var mobile_url = '<?php echo MOBILE_URL; ?>';
	var static_url = '<?php echo STATIC_URL; ?>';
	var base_v = '<?php echo $BASE_V;?>';
	var php_self = '<?php echo PHP_SELF; ?>';
	var global_status='<?php echo $status;?>';
</script>
<script src="<?php echo STATIC_URL; ?>js/jquery/1.11.2/jquery-1.11.2.min.js" type="text/javascript"></script>
<script src="<?php echo STATIC_URL; ?>js/jquery-plugin/jquery.tmpl.min.js" type="text/javascript"></script>
<script src="<?php echo $BASE_V;?>js/settle.js" type="text/javascript"></script>