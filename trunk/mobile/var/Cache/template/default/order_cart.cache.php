<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\order_cart.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\mobile\application\View\default\order_cart.tpl', 1465554526)
;?>
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
  <link href="<?php echo $BASE_V;?>css/common/base.css" type="text/css" rel="stylesheet">
  <link href="<?php echo $BASE_V;?>css/cart/mycart.css" type="text/css" rel="stylesheet">
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
    <a id="hd_back" class="abs comm_p8 hide" href="<?php echo $referer_url;?>" style="display: block;">返回</a> 
    <a id="common_hd_logo" class="t_hide abs"><?php echo $config['cfg_webname'];?></a>
    <h1 class="hd_tle">购物车</h1>
    <a id="hd_edit" class="abs hide" style="display: block;"><em id="cart_del_a" class="abs">&nbsp;</em> <em id="cart_del_b" class="abs">&nbsp;</em></a>
  </header>

  <?php if($member_info['mobile']!='') { ?>
  <section id="mycart_user" class="">
    <div id="user_content_isLogin">
      <div class="left">
        <p>当前账号:&nbsp;<?php echo $member_info['mobile'];?></p>
        <p id="tel_notice">* 交易通知短信会发到这个手机号码</p>
      </div> <a id="use_other_tele" class="right" style="display:none">切换账号</a>
      <div class="clear"></div>
    </div>
  </section>
  <?php } else { ?>
  <section id="mycart_user" class="hide">
    <p class="mycart_tle">请填写手机号码来快速下单</p>
    <div class="mycart_user_content">
      <p class="mycart_input_p rel">
        <label for="tel">手机号码</label>
        <input type="tel" required maxlength="11" minlength="11" pattern="^1([3]|[5]|[8]|[4]|[7])[0-9]{9}$" id="tel" name="tel" value="<?php echo $member_info['mobile'];?>" placeholder="填写你的手机号码"> </p>
        <p id="tel_notice">* 交易通知短信会发到这个手机号码</p>
        <a id="submit_user_tel" class="btnok">确认手机号码</a>
	  
            <div id="fast_login" class="hide">
                <p class="weixinTxt"><span class="shuxian"></span>
                    <span class="or"><em>或</em></span></p>
                <div id="weixin" class="">
                    <a class="weixinLogin" id="weixinLogin" href="<?php echo MOBILE_URL; ?>oauth/wechat?display=weixin">用微信登录
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
                        <a class="weixinLogin" id="qqLogin" href="<?php echo MOBILE_URL; ?>oauth/qq?display=mobile">用QQ登录</a>
                    </div>
                    <div class="weibo">
                        <a class="weixinLogin" id="weiboLogin" href="<?php echo MOBILE_URL; ?>oauth/weibo?display=mobile">用微博登录</a>
                    </div>
                </div>
            </div>
			
    </div>
  </section>
  <?php } ?>
  <section id="mycart_wrap">
    <section class="cart_sec" id="select_items">
      <div class="cart_wrap">
      <?php if(count($cart_list)==0) { ?>
        <div id="cart_empty">还没有选购商品</div>
      <?php } else { ?>
        <div id="cart_seller_list">
          <?php if(is_array($cart_list)) foreach($cart_list AS $v) { ?>
          <div class="cart_seller_wrap">
            <div class="cart_seller_title rel">
              <mark class="cart_seller_mask abs cart_seller_mask_already" data-seller="<?php echo $v['item_uid'];?>">&nbsp;</mark>
              <a class="block over_hidden ellipsis" href="/shop/<?php echo $v['item_uid'];?>"><?php echo $v['shop_name'];?></a>
            </div>
            <?php $num=0 ?>            <ul id="cart_ul_<?php echo $v['item_uid'];?>" class="cart_ul" data-seller="<?php echo $v['item_uid'];?>" data-goods-uid="<?php echo $v['goods_uid'];?>">
              <?php if(is_array($v['item_list'])) foreach($v['item_list'] AS $item) { ?>
              <li class="cart_li rel" id="<?php echo $item->cart_id;?>" data-cart-id="<?php echo $item->cart_id;?>" data-item-id="<?php echo $item->item_id;?>" data-item-sku="<?php echo $item->goods_sku_id;?>" data-item-price="<?php echo $item->item_price;?>" data-current-num="<?php echo $item->item_number;?>" data-goods-type="<?php echo $item->goods_type;?>">
                <mark class="cart_mask abs already_mask" data-cart-id="<?php echo $item->cart_id;?>">&nbsp;</mark>
                <a href="/item/<?php echo $item->item_id;?>.html" class="cart_img abs"><img src="<?php echo $item->goods_image_id;?>" width="50" height="50"></a><a href="<?php echo MOBILE_URL; ?>goods/<?php echo $item->goods_id;?>.html" class="cart_tle over_hidden"><?php echo $item->goods_name;?><?php if($item->is_integral==1) { ?><font color="red">[积分抵扣]</font><?php } ?></a>
                <p class="cart_cls over_hidden ellipsis">型号:&nbsp;<?php echo $item->goods_sku_name;?></p>
                <!--<?php $idd=$item->goods_sku_id==0?$item->goods_id:$item->goods_sku_id;$key=$item->goods_sku_id==0?'goods_id':'goods_sku_id'; ?>-->
                <div class="control_count" data-stock="<?php echo $goods_stock_array[$key][$idd]; ?>" data-current-num="<?php echo $item->item_number;?>" data-goods-member-level="<?php echo $item->goods_member_level;?>" data-goods-type="<?php echo $item->goods_type;?>">
                  <div class="left">
                    <span class="i_pri">¥<?php echo $item->item_price;?></span>
                    <?php if($item->item_price<>$item->item_total_price) { ?>
                    <span class="disable_edit"><del>¥<?php echo $item->item_total_price;?></del></span>
                    <?php } ?>
                  </div>
                  <div class="control_num right c_txt bold rel"> <em class="control_num_sub abs">－</em>
                    <input type="tel" class="item_num bold c_txt block" value="<?php echo $item->item_number;?>"> <em class="control_num_add abs">＋</em></div>
                </div>
              </li>
              <?php $num+=$item->item_number ?>              <?php } ?>
            </ul>
            <div class="cart_fix_footer wrap">
              <div class="cart_fix_footer_wrap margin_auto wrap">
                <div class="cart_fix_footer_money">
                  <p class="cart_fix_footer_notice left"> 
                  	
                  <?php if(empty($v['shipping_fee'])) { ?>不含运费<?php } else { ?>
                  	总商品金额:<?php echo $v['item_amount'];?>&nbsp;&nbsp;&nbsp;&nbsp;邮费:<?php echo $v['shipping_fee'];?><?php } ?>
                  </p>
                  <p class="mycart_money right">合计:&nbsp;<span id="money_count_<?php echo $v['item_uid'];?>" class="money_count i_pri">¥<?php echo $v['all_amount'];?></span></p>
                </div>
                <div class="cart_fix_footer_inner rel"><a class="do_buy btnok right" data-sellerid="<?php echo $v['item_uid'];?>">去结算(<em id="cash_count_<?php echo $v['item_uid'];?>" class="cash_count"><?php echo $num; ?></em>)</a></div>
              </div>
            </div>
          </div>
          <?php } ?>
        </div>
      <?php } ?>
      </div>
    </section>
  </section>
  <script src="<?php echo STATIC_URL; ?>common/assets/js/jquery.min.js" type="text/javascript"></script>
  <script src="<?php echo STATIC_URL; ?>js/json2.js" type="text/javascript"></script>
  <script type="text/javascript">  
  var index_url = '<?php echo INDEX_URL; ?>';
  var mobile_url = '<?php echo MOBILE_URL; ?>';
  var static_url = '<?php echo STATIC_URL; ?>';
  var base_v = '<?php echo $BASE_V;?>';
  var php_self = '<?php echo PHP_SELF; ?>';
  
  var global_stock_array = JSON.parse('<?php echo $goods_stock_array_json;?>');
  var global_member_info = JSON.parse('<?php echo $member_info_json;?>');
  </script>
<script src="<?php echo $BASE_V;?>js/common.js" type="text/javascript"></script>
  <script src="<?php echo $BASE_V;?>js/order_cart.js?v=8" type="text/javascript"></script>
</body>

</html>
