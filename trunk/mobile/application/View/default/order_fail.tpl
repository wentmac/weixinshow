<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">
  <meta content="telephone=no" name="format-detection">
  <meta name="apple-touch-fullscreen" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <title>{$config[cfg_webname]}</title>
  <link href="{$BASE_V}css/common/base.css" type="text/css" rel="stylesheet">
  <link href="{$BASE_V}css/order/pay.css" type="text/css" rel="stylesheet">
  <style id="style-1-cropbar-clipper">
  /* Copyright 2014 Evernote Corporation. All rights reserved. */
  
  .en-markup-crop-options {
    top: 18px !important;
    left: 50% !important;
    margin-left: -100px !important;
    width: 200px !important;
    border: 2px rgba(255, 255, 255, .38) solid !important;
    border-radius: 4px !important;
  }
  
  .en-markup-crop-options div div:first-of-type {
    margin-left: 0px !important;
  }
  </style>
</head>

<body>
  <h1 class="successTxt" style="height:50px;">支付失败！</h1>
  <h1 class="successTxt">如果您的支付平台已经扣款，谢联系客服查询！</h1>
  <p class="back_Btn"><span class="left"><a class="btncancel for_gaq" data-for-gaq="返回购物车" href="{MOBILE_URL}order/cart">返回购物车</a></span><span class="right"><a class="btnok for_gaq" data-for-gaq="返回店铺" id="backShop" href="{MOBILE_URL}shop/{$order_info->item_uid}">返回店铺</a></span></p>
  <section id="paySuccess">
    <div id="payLoading" class="loading hide" style="display: none;">&nbsp;</div>
    <div id="payLoaded" class="hide" style="display: block;">
      <ul id="succ_ulinfo">
        <li id="buyer"><span>收件人：</span>{$order_info->consignee}</li>
        <li class="address" id="addressStr"><span>收货地址：</span>{$order_info->full_address}</li>
        <li class="e1" id="orderID"><span>订单号：</span>{$order_info->order_sn}</li>
        <li class="e2"><span>可登陆：</span><a href="{MOBILE_URL}member/order" id="iVdianLink">查看全部订单信息</a></li>
        <li id="payFor"><span>付款金额：</span> ￥{$order_info->order_amount}元</li>
      </ul>
      <p class="orderInfo"><a href="{MOBILE_URL}member/order.detail?sn={$order_info->order_sn}" id="orderInfo" class="for_gaq" data-for-gaq="查看订单详情" href="#">订单详情</a></p>
    </div>
  </section>
  <!--<div class="erweima"><img src="" id="erweima"></div>
  <div id="erweimaTxt">
    <p class="e1">随时查看你的订单物流信息</p>
    <p class="e2">长按上面图片，关注{$config[cfg_webname]}公众号</p>
  </div>-->

</body>

</html>
