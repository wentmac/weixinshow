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
  <link href="http://s.koudai.com/css/common/base.css?v=1502170101" type="text/css" rel="stylesheet">
  <link href="http://s.koudai.com/css/cart/myaddress.css?v=1502170101" type="text/css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="http://s.koudai.com/css/user/user.css?v=1502170101">
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
  <!--{include inc/header}-->
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
            <option value="-1">--省份--</option>
            <option value="0">北京</option>
            <option value="1">安徽</option>
            <option value="2">福建</option>
            <option value="3">甘肃</option>
            <option value="4">广东</option>
            <option value="5">广西</option>
            <option value="6">贵州</option>
            <option value="7">海南</option>
            <option value="8">河北</option>
            <option value="9">河南</option>
            <option value="10">黑龙江</option>
            <option value="11">湖北</option>
            <option value="12">湖南</option>
            <option value="13">吉林</option>
            <option value="14">江苏</option>
            <option value="15">江西</option>
            <option value="16">辽宁</option>
            <option value="17">内蒙古</option>
            <option value="18">宁夏</option>
            <option value="19">青海</option>
            <option value="20">山东</option>
            <option value="21">山西</option>
            <option value="22">陕西</option>
            <option value="23">上海</option>
            <option value="24">四川</option>
            <option value="25">天津</option>
            <option value="26">西藏</option>
            <option value="27">新疆</option>
            <option value="28">云南</option>
            <option value="29">浙江</option>
            <option value="30">重庆</option>
            <option value="31">香港</option>
            <option value="32">澳门</option>
            <option value="33">台湾</option>
          </select>
          <select id="city" name="city" class="block input" tabindex="4" data-region-id="52">
            <option value="-1">--城市--</option>
            <option value="0">北京</option>
          </select>
          <select id="district" name="district" class="block input" tabindex="5" data-region-id="500">
            <option value="-1">--地区--</option>
            <option value="0">东城区</option>
            <option value="1">西城区</option>
            <option value="2">海淀区</option>
            <option value="3">朝阳区</option>
            <option value="4">崇文区</option>
            <option value="5">宣武区</option>
            <option value="6">丰台区</option>
            <option value="7">石景山区</option>
            <option value="8">房山区</option>
            <option value="9">门头沟区</option>
            <option value="10">通州区</option>
            <option value="11">顺义区</option>
            <option value="12">昌平区</option>
            <option value="13">怀柔区</option>
            <option value="14">平谷区</option>
            <option value="15">大兴区</option>
            <option value="16">密云县</option>
            <option value="17">延庆县</option>
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
</body>

</html>
