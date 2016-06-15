<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\member/agent_detail.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\mobile\application\View\default\member\agent_detail.tpl', 1461299488)
;?>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">
  <meta content="telephone=no" name="format-detection">
  <meta name="apple-touch-fullscreen" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <title>我的东家 - <?php echo $config['cfg_webname'];?></title>
  <link href="<?php echo $BASE_V;?>css/common/base.css" type="text/css" rel="stylesheet">
  <link href="<?php echo $BASE_V;?>css/order/kd.css" type="text/css" rel="stylesheet">
</head>

<body>
  <header id="common_hd" class="c_txt rel">
      <a id="hd_back" class="abs comm_p8" href="<?php echo $referer_url;?>">返回</a>
      <a id="common_hd_logo" class="t_hide abs common_hd_logo">我的东家</a>
      <h1 class="hd_tle">我的东家</h1>
      <a id="hd_enterShop" class="hide abs" href="<?php echo MOBILE_URL; ?>member/home" style="display: block;"> 
        <span id="hd_enterShop_img" class="abs"> <img class="block" src="" width="32" height="32" style="display: block;"> </span>会员中心
      </a>
  </header>
  
  <section>
    <div class="kd_wrap">
      <div class="kd_title">我的东家</div>
      <div id="kd_loading_infos" class="loading" style="display: none;">&nbsp;</div>
      <div id="kd_loaded_infos" class="hide" style="display: block;">
        <?php if($member_agent_info !== false) { ?>        
        <p id="kd_useradd" class="kd_tle kd_line"><img src="<?php echo $member_agent_info->member_image_id;?>" width="55" height="55" class="left"></p>
        <p class="kd_line" style="border: none;"><span id="kd_username"><?php echo empty($member_agent_info->realname) ? $member_agent_info->nickname : $member_agent_info->realname; ?></span><span id="kd_telephone"><?php echo $member_agent_info->mobile;?></span></p>              
        <!--<div id="kd_detail" class="">无物流信息：暂无物流跟踪数据</div>-->
        <?php } else { ?>
        <p id="kd_useradd" class="kd_tle kd_line">您没有东家</p>
        <?php } ?>
      </div>
    </div>
  </section>
  
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