<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">
  <meta content="telephone=no" name="format-detection">
  <meta name="apple-touch-fullscreen" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <title>{$title}</title>
  <link href="{$BASE_V}v1/css/common/base.css" type="text/css" rel="stylesheet">
  <link href="{$BASE_V}v1/css/index/index.css" type="text/css" rel="stylesheet">
 
<body>
    <header id="common_hd" class="c_txt rel" style="padding-left: 68px; padding-right: 15px;">
    	<a id="hd_back" class="abs" onclick="history.go(-1)">返回</a>
    	<a id="common_hd_logo" class="hd_logo t_hide abs" style="display: block;">{$config[cfg_webname]}</a>
    </header>
    <div style="margin: 0px auto; padding-top: 20px; text-align: center; background-color: #fff; height: 100%;">
    	<img src="{$BASE_V}v1/images/404.png">
    	抱歉，您要查看的内容走丢了...
    	<br/>
    	{$title}
    </div>
    
</body>

</html>
