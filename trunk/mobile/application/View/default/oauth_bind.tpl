<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">
  <meta content="telephone=no" name="format-detection">
  <meta name="apple-touch-fullscreen" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <title>绑定手机号</title>
  <link href="{$BASE_V}css/common/base.css" type="text/css" rel="stylesheet">
  <link href="{$BASE_V}css/others/wxlogin/wxlogin.css" rel="stylesheet" type="text/css">
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
  <header id="common_hd" class="c_txt rel"><a id="hd_back" class="abs comm_p8 hide" href="javascript:window.history.go(-1)" style="display: block;">返回</a> <a id="common_hd_logo" class="t_hide abs">{$config[cfg_webname]}</a>
    <h1 class="hd_tle">绑定手机号</h1></header>
  <div id="success" class="">
    <nav class="wxlogin">
      <p class="status"><span id="pay_scuuess_bg"><em id="pay_scuuess_icon" class="abs"></em> <em class="abs quekou"></em></span></p>
      <p class="txt1">{$oauth_type}登录成功！请绑定手机号</p>
      <p class="txt2">绑定后可接收交易通知短信。</p>
    </nav>
    <nav class="wxlogin logininput">
      <h2>绑定手机号到微信号：<span id="wxName">{$nickname}</span></h2>
      <ul
        <li class="mycart_input_p rel">
          <input type="tel" required  maxlength="11" minlength="11" pattern="^1([3]|[5]|[8]|[4]|[7])[0-9]{9}$" placeholder="填写你常用的手机号" id="user_tele">
        </li>
      </ul><a class="nextBtn btnok" id="login_next">下一步</a></nav>
  </div>
  <div id="login_wrap" class="hide">
    <p class="telephone">我们已经发送<span>短信验证码</span>到你的手机</p>
    <p class="tel"><span id="telVal"></span></p>
    <p id="loginSMS_tle" class="bold">&nbsp;</p>
    <form name="js_for_false_submit" id="js_for_false_submit" method="post" action="">
      <input type="hidden" name="param" id="form_SMS_param" value="">
      <div class="rel wrap hide" id="vcodeimg_wraper" style="margin-bottom: 2px;">
        <input type="text" maxlength="4" id="imgvcode" name="imgvcode" class="input_for_login block right" placeholder="请先输入图片验证码">
        <img id="vcodeimg" width="113" height="35" class="right">
        <div style="clear: both"></div>
      </div>
      <div class="rel wrap" id="catch_wrap">
        <input type="tel" maxlength="6" id="safe_code_input" name="safe_code_input" class="input_for_login block left" placeholder="请填写验证码">
        <div id="catch_times" class="abs c_txt"><span id="honey_times">60</span> 秒后重新获取</div>
        <div id="catch_code_btn" class="abs c_txt bold">获取验证码</div>
      </div>
    </form><a id="login_form_submit" class="btnok c_txt bold">确认</a>
    <p style="text-align:right;height:33px;line-height:50px;color:#2b86e0"><span id="speak_code">收不到验证码?</span></p>
  </div>
  <script type="text/javascript" src="{STATIC_URL}common/assets/js/jquery.min.js"></script>
  <script type="text/javascript" src="{STATIC_URL}js/jquery-plugin/ui/minified/jquery.cookie-min.js"></script>
  <script type="text/javascript">
 
  var index_url = '{INDEX_URL}';
  var mobile_url = '{MOBILE_URL}';
  var static_url = '{STATIC_URL}';
  var base_v = '{$BASE_V}';
  var php_self = '{PHP_SELF}';

  var referer_url = '{$oauth_referer_url}';
  var display = '{$display}';
  var domain = '{$domain}';
  var global_mobile = $.cookie('mobile');
  </script>
  <script type="text/javascript" src="{$BASE_V}js/oauth_bind.js?r=1"></script>
</body>

</html>
