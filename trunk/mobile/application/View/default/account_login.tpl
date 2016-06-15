<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">
    <meta content="telephone=no" name="format-detection">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>用户登录</title>
    <link href="{$BASE_V}css/common/base.css" type="text/css" rel="stylesheet">
    <link href="{$BASE_V}css/cart/mycart.css" type="text/css" rel="stylesheet">
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
    <header id="common_hd" class="c_txt rel"><a id="hd_back" class="abs comm_p8 hide" href="{$referer_url}" style="display: block;">返回</a>
        <a id="common_hd_logo" class="t_hide abs">{$config[cfg_webname]}</a>
        <h1 id="page_tit" class="hd_tle">用户登录</h1>
        <a id="hd_edit" class="abs hide" style="display: none;">
            <em id="cart_del_a" class="abs">&nbsp;</em> <em id="cart_del_b" class="abs">&nbsp;</em></a>
    </header>
    <section id="mycart_user">
        <p class="mycart_tle">请填写手机号码</p>
        <div class="mycart_user_content">
            <p class="mycart_input_p rel">
                <label for="tel">手机号码</label>
                <input type="tel" required maxlength="11" minlength="11" pattern="^1([3]|[5]|[8]|[4]|[7])[0-9]{9}$" id="tel" name="tel" value="" placeholder="填写你的手机号码"> </p>
            <p id="tel_notice">* 交易通知短信会发到这个手机号码</p><a id="submit_user_tel" class="btnok">确认手机号码</a>
            <div id="fast_login" class="hide">
                <p class="weixinTxt"><span class="shuxian"></span>
                    <span class="or"><em>或</em></span></p>
                <div id="weixin" class="hide">
                    <a class="weixinLogin" id="weixinLogin" href="{MOBILE_URL}oauth/wechat?display=weixin">用微信登录
							<div id="wx_icon_a" class="abs">
								<em class="wx_icon_em_a abs">&nbsp;</em> 
								<em class="wx_icon_em_b abs">&nbsp;</em>
								<em class="wx_icon_em_c abs">&nbsp;</em>
							</div>
							<div id="wx_icon_b" class="abs">
								<em class="wx_icon_em_a abs">&nbsp;</em>
								<em class="wx_icon_em_b abs">&nbsp;</em> 
							</div>
						</a>
                </div>
                <div id="qq_weibo">					
                    <div class="qq">
                        <a class="weixinLogin" id="qqLogin" href="{MOBILE_URL}oauth/qq?display=mobile">用QQ登录</a>
                    </div>
                    <div class="weibo">
                        <a class="weixinLogin" id="weiboLogin" href="{MOBILE_URL}oauth/weibo?display=mobile">用微博登录</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script type="text/javascript" src="{STATIC_URL}common/assets/js/jquery.min.js"></script>
    <script type="text/javascript" src="{STATIC_URL}js/jquery-plugin/ui/minified/jquery.cookie-min.js"></script>
    <script type="text/javascript">	
    var index_url = '{INDEX_URL}';
    var mobile_url = '{MOBILE_URL}';
    var static_url = '{STATIC_URL}';
    var base_v = '{$BASE_V}';
    var php_self = '{PHP_SELF}';
    var referer_url = '{$referer_login}';
    var global_mobile = $.cookie('mobile');
    </script>
    <script type="text/javascript" src="{$BASE_V}js/account_login.js?v=1"></script>
</body>

</html>
