<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">
  <meta content="telephone=no" name="format-detection">
  <meta name="apple-touch-fullscreen" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <title>选择收货地址</title>
  <link href="{$BASE_V}css/common/base.css" type="text/css" rel="stylesheet">
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

<body id="selectAddBody">
  <section>    
    <header id="common_hd" class="c_txt rel">
        <a id="hd_back" class="abs comm_p8" href="{$referer_url}">返回</a>
        <a id="common_hd_logo" class="t_hide abs common_hd_logo">我的地址</a>
        <h1 class="hd_tle">我的地址</h1>
        <a id="hd_enterShop" class="hide abs" href="{MOBILE_URL}member/home" style="display: block;"> <span id="hd_enterShop_img" class="abs"> <img class="block" src="" width="32" height="32" style="display: block;"> </span>会员中心</a>
    </header>

    <div id="js_addressList">
      <!-- {loop $address_list $v} -->
      <nav class="adressList js_addressLength{if $v->address_id==$address_id} adressListborder{/if}"><a class="adress js_adress addressBorder" dataid="{$v->address_id}"><h2>{$v->consignee} <span>{$v->mobile}</span></h2><p>{$v->full_address}</p><span class="change hide"><em><i></i></em></span></a></nav>
      <!-- {/loop} -->
    </div>
  </section>
  <footer id="selectAdd_footer" class="fix wrap">
    <div id="buybtn_wrap" class="margin_auto">
      <div id="buybtn_inner"><a class="for_gaq btncancel left" id="editaddress" data-for-gaq="修改收货地址" href="{MOBILE_URL}member/address">修改收货地址</a> <a class="for_gaq btnok right" id="adadress" data-for-gaq="添加新收货地址" href="{MOBILE_URL}member/address.add">添加新地址</a>
        <div class="clear"></div>
      </div>
    </div>
  </footer>
  <script type="text/javascript" src="{STATIC_URL}common/assets/js/jquery.min.js"></script>
  <script type="text/javascript" src="{STATIC_URL}js/jquery-plugin/ui/minified/jquery.cookie-min.js"></script>
  <script type="text/javascript">
  var global_backurl = $.cookie('back_order_address_url');
  $(".js_adress").click(function() {
    var id = $(this).attr('dataid');
    if (global_backurl) {
      location.href = global_backurl + '&address_id=' + id;
    } else {
      location.href = '{$referer_url}';
    }
  });
  </script>
</body>

</html>
