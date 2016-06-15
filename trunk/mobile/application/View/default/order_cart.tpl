<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">
  <meta content="telephone=no" name="format-detection">
  <meta name="apple-touch-fullscreen" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <title>购物车</title>
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
  <header id="common_hd" class="c_txt rel">
    <a id="hd_back" class="abs comm_p8 hide" href="{$referer_url}" style="display: block;">返回</a> 
    <a id="common_hd_logo" class="t_hide abs">{$config[cfg_webname]}</a>
    <h1 class="hd_tle">购物车</h1>
    <a id="hd_edit" class="abs hide" style="display: block;"><em id="cart_del_a" class="abs">&nbsp;</em> <em id="cart_del_b" class="abs">&nbsp;</em></a>
  </header>

  <!--{if $member_info[mobile]!=''}-->
  <section id="mycart_user" class="">
    <div id="user_content_isLogin">
      <div class="left">
        <p>当前账号:&nbsp;{$member_info[mobile]}</p>
        <p id="tel_notice">* 交易通知短信会发到这个手机号码</p>
      </div> <a id="use_other_tele" class="right" style="display:none">切换账号</a>
      <div class="clear"></div>
    </div>
  </section>
  <!--{else}-->
  <section id="mycart_user" class="hide">
    <p class="mycart_tle">请填写手机号码来快速下单</p>
    <div class="mycart_user_content">
      <p class="mycart_input_p rel">
        <label for="tel">手机号码</label>
        <input type="tel" required maxlength="11" minlength="11" pattern="^1([3]|[5]|[8]|[4]|[7])[0-9]{9}$" id="tel" name="tel" value="{$member_info[mobile]}" placeholder="填写你的手机号码"> </p>
        <p id="tel_notice">* 交易通知短信会发到这个手机号码</p>
        <a id="submit_user_tel" class="btnok">确认手机号码</a>
	  
            <div id="fast_login" class="hide">
                <p class="weixinTxt"><span class="shuxian"></span>
                    <span class="or"><em>或</em></span></p>
                <div id="weixin" class="">
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
  <!--{/if}-->
  <section id="mycart_wrap">
    <section class="cart_sec" id="select_items">
      <div class="cart_wrap">
      <!--{if count($cart_list)==0}-->
        <div id="cart_empty">还没有选购商品</div>
      <!--{else}-->
        <div id="cart_seller_list">
          <!--{loop $cart_list $v}-->
          <div class="cart_seller_wrap">
            <div class="cart_seller_title rel">
              <mark class="cart_seller_mask abs cart_seller_mask_already" data-seller="{$v[item_uid]}">&nbsp;</mark>
              <a class="block over_hidden ellipsis" href="/shop/{$v[item_uid]}">{$v[shop_name]}</a>
            </div>
            ${$num=0}
            <ul id="cart_ul_{$v[item_uid]}" class="cart_ul" data-seller="{$v[item_uid]}" data-goods-uid="{$v[goods_uid]}">
              <!--{loop $v[item_list] $item}-->
              <li class="cart_li rel" id="{$item->cart_id}" data-cart-id="{$item->cart_id}" data-item-id="{$item->item_id}" data-item-sku="{$item->goods_sku_id}" data-item-price="{$item->item_price}" data-current-num="{$item->item_number}" data-goods-type="$item->goods_type" data-is-integral="$item->is_integral">
                <mark class="cart_mask abs already_mask" data-cart-id="{$item->cart_id}">&nbsp;</mark>
                <a href="/item/{$item->item_id}.html" class="cart_img abs"><img src="{$item->goods_image_id}" width="50" height="50"></a><a href="{MOBILE_URL}goods/{$item->goods_id}.html" class="cart_tle over_hidden">{$item->goods_name}<!--{if $item->is_integral==1}--><font color="red">[积分抵扣]</font><!--{/if}--></a>
                <p class="cart_cls over_hidden ellipsis">型号:&nbsp;{$item->goods_sku_name}</p>
                <!--${$idd=$item->goods_sku_id==0?$item->goods_id:$item->goods_sku_id;$key=$item->goods_sku_id==0?'goods_id':'goods_sku_id';}-->
                <div class="control_count" data-stock="${echo $goods_stock_array[$key][$idd];}" data-current-num="{$item->item_number}" data-goods-member-level="{$item->goods_member_level}" data-goods-type="$item->goods_type">
                  <div class="left">
                    <span class="i_pri">¥{$item->item_price}</span>
                    <!--{if $item->item_price<>$item->item_total_price}-->
                    <span class="disable_edit"><del>¥{$item->item_total_price}</del></span>
                    <!--{/if}-->
                  </div>
                  <div class="control_num right c_txt bold rel"> <em class="control_num_sub abs">－</em>
                    <input type="tel" class="item_num bold c_txt block" value="{$item->item_number}"> <em class="control_num_add abs">＋</em></div>
                </div>
              </li>
              ${$num+=$item->item_number}
              <!--{/loop}-->
            </ul>
            <div class="cart_fix_footer wrap">
              <div class="cart_fix_footer_wrap margin_auto wrap">
                <div class="cart_fix_footer_money">
                  <p class="cart_fix_footer_notice left"> 
                  	
                  <!--{if empty($v[shipping_fee])}-->不含运费<!--{else}-->
                  	总商品金额:{$v[item_amount]}&nbsp;&nbsp;&nbsp;&nbsp;邮费:{$v[shipping_fee]}<!--{/if}-->
                  </p>
                  
                  <p class="mycart_money right">实付总额:&nbsp;<span class="total_amount i_pri">¥{$v[all_amount]}</span></p>                  
                <!--{if $v[use_integral]==1}-->
                  <p class="mycart_money right">积分抵扣:&nbsp;<span class="use_available_integral i_pri">-¥{$v[use_available_integral]}</span></p>                  
                <!--{/if}-->                   
                <p class="mycart_money right">应付总额:&nbsp;<span id="money_count_{$v[item_uid]}" class="money_count i_pri">¥{$v[order_payable_amount]}</span></p>
                  
                </div>
                <div class="cart_fix_footer_inner rel"><a class="do_buy btnok right" data-sellerid="{$v[item_uid]}">去结算(<em id="cash_count_{$v[item_uid]}" class="cash_count">${echo $num;}</em>)</a></div>
              </div>
            </div>
          </div>
          <!--{/loop}-->
        </div>
      <!--{/if}-->
      </div>
    </section>
  </section>
  <script type="text/javascript" src="{STATIC_URL}common/assets/js/jquery.min.js"></script>
  <script type="text/javascript" src="{STATIC_URL}js/json2.js"></script>
  <script type="text/javascript">  
  var index_url = '{INDEX_URL}';
  var mobile_url = '{MOBILE_URL}';
  var static_url = '{STATIC_URL}';
  var base_v = '{$BASE_V}';
  var php_self = '{PHP_SELF}';
  
  var global_stock_array = JSON.parse('{$goods_stock_array_json}');
  var global_member_info = JSON.parse('{$member_info_json}');
  var available_integral = '{$available_integral}';
  </script>
<script type="text/javascript" src="{$BASE_V}js/common.js"></script>
  <script type="text/javascript" src="{$BASE_V}js/order_cart.js?v=9"></script>
</body>

</html>
