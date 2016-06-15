<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\member/qrcode_detail.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\mobile\application\View\default\member\qrcode_detail.tpl', 1465365526)
;?>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">
  <meta content="telephone=no" name="format-detection">
  <meta name="apple-touch-fullscreen" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <title>我的二维码 - <?php echo $config['cfg_webname'];?></title>
  <link href="<?php echo $BASE_V;?>css/common/base.css" type="text/css" rel="stylesheet">
  <link href="<?php echo $BASE_V;?>css/order/kd.css" type="text/css" rel="stylesheet">
  <style>
.kd_loaded_times p{
    position: relative;
    display: block;
    height: 44px;
    line-height: 44px;
    color: #333333;
    -webkit-box-align: center;
}  
.kd_loaded_times img{
    width:100%;
}  
  </style>
</head>

<body>
  <header id="common_hd" class="c_txt rel">
      <a id="hd_back" class="abs comm_p8" href="<?php echo $referer_url;?>">返回</a>
      <a id="common_hd_logo" class="t_hide abs common_hd_logo">我的二维码</a>
      <h1 class="hd_tle">我的二维码</h1>
      <a id="hd_enterShop" class="hide abs" href="<?php echo MOBILE_URL; ?>member/home" style="display: block;"> <span id="hd_enterShop_img" class="abs"> <img class="block" src="" width="32" height="32" style="display: block;"> </span>会员中心</a>
  </header>

  <header id="kd_status" class="kd_status">
      <span>您的推广二维码已经生成成功,请长按二维码进行保存和分享</span>      
  </header>

  <section>
    <div class="kd_wrap">      
      <div id="kd_loaded_times" class="kd_loaded_times">
        <img src="<?php echo MOBILE_URL; ?>member/qrcode.get_image?uid=<?php echo $uid;?>" width="auto">
      </div>
    </div>
  </section>  

  <script src="BASE_Vjs/order_detail.js" type="text/javascript"></script>
</body>

</html>
<script src="<?php echo STATIC_URL; ?>js/jquery/1.11.2/jquery-1.11.2.min.js" type="text/javascript"></script>
<script type="text/javascript">
  var index_url = '<?php echo INDEX_URL; ?>';
  var mobile_url = '<?php echo MOBILE_URL; ?>';
  var static_url = '<?php echo STATIC_URL; ?>';
  var base_v = '<?php echo $BASE_V;?>';
  var php_self = '<?php echo PHP_SELF; ?>';
</script>