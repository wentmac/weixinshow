<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\order_confirm.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\mobile\application\View\default\order_confirm.tpl', 1464412939)
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
  <title>订单确认</title>
  <link href="<?php echo $BASE_V;?>css/common/base.css" type="text/css" rel="stylesheet">
  <link href="<?php echo $BASE_V;?>css/order/order.css" type="text/css" rel="stylesheet">
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
      <a id="hd_back" class="abs comm_p8" href="<?php echo $referer_url;?>">返回</a>
      <a id="common_hd_logo" class="t_hide abs common_hd_logo">订单确认</a>
      <h1 class="hd_tle">确认下单</h1>
      <a id="hd_enterShop" class="hide abs" href="<?php echo MOBILE_URL; ?>member/home" style="display: block;"> <span id="hd_enterShop_img" class="abs"> <img class="block" src="" width="32" height="32" style="display: block;"> </span>会员中心</a>
  </header>

  <section id="order_wrap">
    <section id="order_request" class="over_hidden">
      <ul id="requester_ul" class="rel hide" style="display: block;">
        <li class="requester_li o_tle" id="o_nam"><?php echo $address_info->consignee;?></li>
        <li class="requester_li o_tle" id="o_tele"><?php echo $address_info->mobile;?></li>
        <li class="requester_li" id="o_address_wrap">
          <p id="o_address"><?php echo $address_info->full_address;?></p>
        </li>
      </ul>
    </section>
    <section id="xiaomai" class="xiaomai hide">
      <ul>
        <li><span>联系人</span>
          <input type="text" placeholder="请输入联系人姓名" id="xmUsername">
        </li>
        <li><span>联系电话</span>
          <input type="tel" placeholder="请输入联系人电话" id="xmUsertel" class="tel">
        </li>
        <li><span>收货方式</span>
          <p class="shouhuo"><a class="shouhuoStyle esp" showli="1">明日自提</a></p>
        </li>
      </ul>
      <ul id="zitiAdress">
        <li><span>自提地点</span>
          <p class="xmztAdress" id="xmztAdress">北京大学小博实超市内小麦公社自提点</p>
        </li>
      </ul>
    </section>
    <section id="order_payment" class="rel hide">
      <p id="payment_loading" class="loading" style="display: none;">&nbsp;</p>
      <ul id="pay_class_ul" class="wrap hide" style="display: block;">
        <li class="pay_class_li rel pre_pay_class_li current_pay_class" id="pay_class_ol" data-pay-class="-1" style="padding-bottom: 77px;"> <em class="payclass_select abs">&nbsp;</em>在线支付</li>
        <li class="pay_class_li rel" id="pay_class_delivery" data-pay-class="2" style="display: none; padding-bottom: 0px;"> <em class="payclass_select abs">&nbsp;</em>货到付款</li>
      </ul>
      <div id="payment_ul_wrap" class="order_padding_l_r abs wrap" style="top: 55px; left: 0px;">
        <ul id="payment_ul" class="over_hidden">
          <li id="pay_by_ol_db" class="payment_li rel toShowPayment payment_li_current" data-payment="-3" style="display: block;"><em class="payment_select abs">&nbsp;</em>
            <div id="payment_WDZF" class="payment_logo abs">&nbsp;</div>
            <div class="payment_txt">
              <p>担保交易（推荐）</p>
              <p class="gray_txt payment_intro over_hidden ellipsis"><?php echo $config['cfg_webname'];?>提供担保，确定收货后才会打款给卖家</p>
            </div>
          </li>
          <li id="pay_by_ol_zj" class="payment_li rel" data-payment="-4"><em class="payment_select abs">&nbsp;</em>
            <div id="payment_WDZF" class="payment_logo abs">&nbsp;</div>
            <div class="payment_txt">
              <p>直接付款</p>
              <p class="gray_txt payment_intro over_hidden ellipsis">付款后，卖家将立即收到货款，请注意风险</p>
            </div>
          </li>
          <li id="pay_by_delivery" class="payment_li rel for_ol_pay_style" data-payment="-1" style="display: none;"><em class="payment_select abs">&nbsp;</em>
            <div id="payment_HDFK" class="payment_logo abs">&nbsp;</div>
            <div class="payment_txt">
              <p>货到付款</p>
              <p class="gray_txt payment_intro over_hidden ellipsis">验货后付款，网购更安心</p>
            </div>
          </li>
        </ul>
      </div>
    </section>
    <section id="order_seller">
      <a id="o_seller_a" class="block rel">
        <?php if($cart_list) { ?>
        <p id="o_seller" data-id='<?php echo $cart_list['0']->item_uid;?>' class="over_hidden ellipsis"><?php echo $cart_list['0']->shop_name;?></p>
        <?php } ?>
      </a>
    </section>
    <section id="order_item">
      <ul id="o_ul" class="hide over_hidden" style="display: block;">
    <?php if(is_array($cart_list)) foreach($cart_list AS $v) { ?>
        <li class="o_li li_for_pay" data-cart-id='<?php echo $v->cart_id;?>' data-price="<?php echo $v->item_price;?>" data-item-id="<?php echo $v->item_id;?>" data-sku-id="<?php echo $v->goods_sku_id;?>" data-count="<?php echo $v->item_number;?>" data-limit_discount_id="">
          <a href="<?php echo MOBILE_URL; ?>goods/<?php echo $v->goods_id;?>.html" class="o_a rel block"><img src="<?php echo $v->goods_image_id;?>" width="50" height="50" class="left">
            <p class="o_name"><?php echo $v->goods_name;?><?php if($v->is_integral==1) { ?><font color="red">[积分抵扣]</font><?php } ?></p>
            <p class="o_a_sku over_hidden ellipsis">型号:&nbsp;<?php echo $v->goods_sku_name;?></p>
            <p class="o_a_pri i_pri abs r_txt">¥<?php echo $v->item_price;?></p>
            <p class="o_a_count abs r_txt">x&nbsp;<?php echo $v->item_number;?></p>
          </a>
        </li>
    <?php } ?>
      </ul>
    </section>
    <section id="hongbao" class="hongbao_current hide">
      <div id="hongbao_content">
        <p class="left"><?php echo $config['cfg_webname'];?>购物券</p>
        <div class="right rel"><em id="hongbao_select" class="abs">&nbsp;</em> 立减&nbsp;<em id="hongbao_cash">&nbsp;</em>&nbsp;元</div>
      </div>
    </section>
    <section id="order_money" class="hide" style="display: block;">
      <p id="express_money" class="r_txt hide"></p>
      <p id="delivery_service_money" class="r_txt">共<?php echo count($cart_list); ?>件商品，商品总额:&nbsp;<span id="delivery_service_money_span" class="i_pri">¥<?php echo $total_price;?></span>
      	
      </p>
      <p id="shipping_fee" class="r_txt">邮费: &nbsp;<span class="i_pri">+¥<?php echo $shipping_fee;?></span></p>      
      <p id="count_money_wrap" class="r_txt">应付总额:&nbsp;<span class="i_pri">¥<?php echo $order_payable_amount;?></span></p>
      <?php if($use_integral==true) { ?>
      <p id="use_integral" class="r_txt">积分抵扣: &nbsp;<span class="i_pri">-¥<?php echo $use_available_integral;?></span></p>
      <?php } ?>      
      <p id="discount_money" class="r_txt hide"></p>
      <p id="free_postage" class="r_txt hide"></p>
    </section>
    <section id="order_remark">
      <p class="address_p rel <?php if($agent_lock==1) { ?>hide<?php } ?>">
        <label for="wxID" class="abs">推荐人</label>
        <input type="text" id="agent_uid" name="agent_uid" class="block input noborder" placeholder="（选填）填写会员商品的推荐人ID" value="" tabindex="8">
      </p>

      <p class="address_p rel">
        <label for="remark" class="abs" id="remark_label">备注</label>
        <textarea name="remark" cols="" rows="" id="remark" placeholder="（选填）给卖家留言" class="block input" tabindex="7"></textarea>
      </p>
      <p class="address_p rel">
        <label for="wxID" class="abs">微信号</label>
        <input type="text" id="wxID" name="wxID" class="block input noborder" placeholder="（选填）方便卖家与你联系" value="<?php echo $weixin_id;?>" tabindex="8">
      </p>
                    
    </section>
    <section id="order_notice" class="hide rel" style="display: block;">
      <p><span id="notice_icon" class="abs c_txt">!</span> <span id="pay_notice">该商家支持担保交易，在您确认收货后，商家才能使用或提现资金，请放心购买！</span></p>
      <p id="warrant_flag_txt" class="hide" style="display: block;">“提交订单”视为已同意 <a href="/others/securedtrans">《<?php echo $config['cfg_webname'];?>担保交易协议》</a></p>
    </section>
    <footer id="order_footer" class="wrap fix hide" style="display: block;">
      <div id="order_btns" class="wrap rel margin_auto">
        <div id="order_btns_inner">
        	<a id="submit_order" class="btnok right for_gaq" data-for-gaq="提交订单">提交订单</a>
          <p id="last_money" class="r_txt"><?php if($use_integral==true) { ?>我的可用积分：<span class="i_pri"><?php echo $available_integral;?></span>   &nbsp;&nbsp;<?php } ?>应付总额:&nbsp;<span class="i_pri" id="last_money_show">¥<?php echo $total_amount;?></span></p>
        </div>
      </div>
    </footer>
  </section>
  <div id="fmx_tags" class="abs" style="left:-100px">
    <object type="application/x-shockwave-flash" data="https://fp.fraudmetrix.cn/static/clear.swf" width="1" height="1" id="fmFlash">
      <param name="movie" value="https://fp.fraudmetrix.cn/static/clear.swf">
      <param name="allowScriptAccess" value="always">
      <param name="flashVars" value="sessionId=KD1433043944434_7379587029572576&amp;serviceUrl=https://fp.fraudmetrix.cn/fp/profile.json">
    </object>
  </div>
  <script src="<?php echo STATIC_URL; ?>common/assets/js/jquery.min.js" type="text/javascript"></script>
  <script src="<?php echo STATIC_URL; ?>js/json2.js" type="text/javascript"></script>
  <script type="text/javascript">
  var index_url = '<?php echo INDEX_URL; ?>';
  var mobile_url = '<?php echo MOBILE_URL; ?>';
  var static_url = '<?php echo STATIC_URL; ?>';
  var base_v = '<?php echo $BASE_V;?>';
  var php_self = '<?php echo PHP_SELF; ?>';

  var global_item_uid = '<?php echo $item_uid;?>';
  var global_address_id = '<?php echo $address_info->address_id;?>';
  var global_goods_uid = '<?php echo $goods_uid;?>';
  </script>
  <script src="<?php echo $BASE_V;?>js/order_confirm.js?v=1" type="text/javascript"></script>
</body>

</html>
