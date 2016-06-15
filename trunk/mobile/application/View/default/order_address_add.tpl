<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">
  <meta content="telephone=no" name="format-detection">
  <meta name="apple-touch-fullscreen" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <title>收货地址</title>
  <link href="{$BASE_V}css/common/base.css" type="text/css" rel="stylesheet">
  <link href="{$BASE_V}css/cart/myaddress.css" type="text/css" rel="stylesheet">
  <link href="{$BASE_V}css/user/user.css" type="text/css" rel="stylesheet">
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
  <header id="common_hd" class="c_txt rel"><a id="hd_back" class="abs comm_p8" href="{$referer_url}">返回</a> <a id="common_hd_logo" class="t_hide abs">{$config[cfg_webname]}</a>
    <h1 class="hd_tle" id="titlename">收货地址</h1></header>
  <section class="cart_sec" id="address_sec">
    <div class="cart_wrap" id="address_wrap">
      <div class="cart_list_title rel" id="address_title">收货人信息
        <div id="share_wx_addr" class="abs">&nbsp;</div>
        <div id="share_position_addr" class="abs">自动获取所在地</div>
      </div>
      <div id="changeposition" class="hide"></div>
      <div id="address_default" class="hide"></div>
      <div id="address_form" class="hide" style="display: block;">
        <p class="address_p rel">
          <label for="nam" class="abs"><span class="hide hidename" style="color:#f00">*</span>收货人</label>
          <input type="text" id="nam" name="nam" class="block input" placeholder="请输入收货人姓名" tabindex="1">
        </p>
        <p class="address_p rel">
          <label for="tele" class="abs"><b class="hide hidename" style="color:#f00">*</b>手机号码</label>
          <input type="tel" id="tele" name="tele" maxlength="11" placeholder="请输入收货人手机号码" class="block input noborder" tabindex="2">
        </p>
        <p class="txt">* 如果由其他人收货，请填写收货人的手机号码</p>
        <p class="address_p rel">
          <label for="province" class="abs">所在地区</label>
          <select id="province" name="province" class="block input" tabindex="3" data-region-id="2">
          </select>
          <select id="city" name="city" class="block input" tabindex="4" data-region-id="52">
          </select>
          <select id="district" name="district" class="block input" tabindex="5" data-region-id="500">
          </select>
        </p>
        <p class="address_p rel address_p_area">
          <label for="detail_add" class="abs" style="top:14px">详细地址</label>
          <textarea name="detail_add" cols="" rows="" id="detail_add" placeholder="请输入街道地址" class="block input" tabindex="6"></textarea>
        </p>
      </div>
    </div>
  </section>
  <nav class="buybtn"><a href="javascript:void(0)" id="js_okBtn" class="btnok">确认收货地址</a></nav>
  <script type="text/javascript" src="{STATIC_URL}common/assets/js/jquery.min.js"></script>
  <script type="text/javascript" src="{STATIC_URL}js/json2.js"></script>
  <script type="text/javascript" src="{STATIC_URL}js/jquery-plugin/ui/minified/jquery.cookie-min.js"></script>
  <script type="text/javascript">
  var index_url = '{INDEX_URL}';
  var mobile_url = '{MOBILE_URL}';
  var static_url = '{STATIC_URL}';
  var base_v = '{$BASE_V}';
  var php_self = '{PHP_SELF}';

  var global_member_info = JSON.parse('{$member_info_json}');

  var global_order_address_pid = $.cookie('global_order_address_pid');
  var global_order_address_cityid = $.cookie('global_order_address_cityid');
  var global_order_address_disid  = $.cookie('global_order_address_disid');



  console.log(global_member_info);

  </script>
  <script type="text/javascript" src="{$BASE_V}js/order_address.js"></script>
</body>

</html>
