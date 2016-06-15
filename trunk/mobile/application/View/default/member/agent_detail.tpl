<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">
  <meta content="telephone=no" name="format-detection">
  <meta name="apple-touch-fullscreen" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <title>我的东家 - {$config[cfg_webname]}</title>
  <link href="{$BASE_V}css/common/base.css" type="text/css" rel="stylesheet">
  <link href="{$BASE_V}css/order/kd.css" type="text/css" rel="stylesheet">
</head>

<body>
  <header id="common_hd" class="c_txt rel">
      <a id="hd_back" class="abs comm_p8" href="{$referer_url}">返回</a>
      <a id="common_hd_logo" class="t_hide abs common_hd_logo">我的东家</a>
      <h1 class="hd_tle">我的东家</h1>
      <a id="hd_enterShop" class="hide abs" href="{MOBILE_URL}member/home" style="display: block;"> 
        <span id="hd_enterShop_img" class="abs"> <img class="block" src="" width="32" height="32" style="display: block;"> </span>会员中心
      </a>
  </header>
  
  <section>
    <div class="kd_wrap">
      <div class="kd_title">我的东家</div>
      <div id="kd_loading_infos" class="loading" style="display: none;">&nbsp;</div>
      <div id="kd_loaded_infos" class="hide" style="display: block;">
        <!--{if $member_agent_info !== false}-->        
        <p id="kd_useradd" class="kd_tle kd_line"><img src="{$member_agent_info->member_image_id}" width="55" height="55" class="left"></p>
        <p class="kd_line" style="border: none;"><span id="kd_username">${echo empty($member_agent_info->realname) ? $member_agent_info->nickname : $member_agent_info->realname;}</span><span id="kd_telephone">{$member_agent_info->mobile}</span></p>              
        <!--<div id="kd_detail" class="">无物流信息：暂无物流跟踪数据</div>-->
        <!--{else}-->
        <p id="kd_useradd" class="kd_tle kd_line">您没有东家</p>
        <!--{/if}-->
      </div>
    </div>
  </section>
  
</body>

</html>
<script type="text/javascript" src="{STATIC_URL}js/jquery/1.11.2/jquery-1.11.2.min.js"></script>    
<script type="text/javascript">
  var index_url = '{INDEX_URL}';
  var mobile_url = '{MOBILE_URL}';
  var static_url = '{STATIC_URL}';
  var base_v = '{$BASE_V}';
  var php_self = '{PHP_SELF}';
</script>