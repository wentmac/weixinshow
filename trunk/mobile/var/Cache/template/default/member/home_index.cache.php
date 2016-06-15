<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\member/home_index.tpl', 'D:\Web\Witkey\wwwroot\yph\trunk\mobile\application\View\default\member\home_index.tpl', 1465299663)
;?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <title>会员中心</title>
  <meta charset="utf-8">
  <meta content="" name="description">
  <meta content="" name="keywords">
  <meta content="eric.wu" name="author">
  <meta content="application/xhtml+xml;charset=UTF-8" http-equiv="Content-Type">
  <meta content="telephone=no, address=no" name="format-detection">
  <meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport">
  <link href="<?php echo $BASE_V;?>css/common/common.css" rel="stylesheet" />
  <link href="<?php echo $BASE_V;?>css/buyer/userCenter.css" rel="stylesheet" />
</head>

<body style="margin: 0 auto; max-width: 640px">
  <div data-role="container" class="body userCenter">
    <header data-role="header">
      <div class="uc-user">
        <a class="box">
          <div>
            <span class="img-wrap"><img src="<?php echo $memberInfo['member_avatar_url'];?>"></span>
          </div>
          <!--显示昵称时，需添加样式：nickname-->
          <div class="nickname">
            <p><?php echo $memberInfo['username'];?> [<?php echo $memberInfo['member_level'];?>]</p>
            <p>已绑定：<?php echo $memberInfo['mobile'];?></p>
            <p>我的推荐ID:<?php echo $memberInfo['uid'];?></p>
          </div>
        </a>
      </div>
    </header>
    <section data-role="body" class="section-body">
      <!--通知-->
      <div class="uc-notification">
        <ul class="box">
          <li>
            <a href="<?php echo MOBILE_URL; ?>member/order?status=waiting_payment"><i class="icon-pay" data-tip="<?php echo $homeinfo['order_status_buyer_waiting_payment'];?>"></i>待支付</a>
          </li>
          <li>
            <a href="<?php echo MOBILE_URL; ?>member/order?status=wating_seller_delivery"><i class="icon-deliver" data-tip="<?php echo $homeinfo['order_status_buyer_wating_seller_delivery'];?>"></i>待发货</a>
          </li>
          <li>
            <a href="<?php echo MOBILE_URL; ?>member/order?status=wating_receiving"><i class="icon-receipt" data-tip="<?php echo $homeinfo['order_status_buyer_wating_receiving'];?>"></i>待收货</a>
          </li>
          <li>
            <a href="<?php echo MOBILE_URL; ?>member/order?status=wating_comment"><i class="icon-comment" data-tip="<?php echo $homeinfo['order_status_buyer_wating_comment'];?>"></i>待评价</a>
          </li>
          <li>
            <a href="<?php echo MOBILE_URL; ?>member/order?status=complete"><i class="icon-complete" data-tip="<?php echo $homeinfo['order_status_buyer_complete'];?>"></i>已完成</a>
          </li>
          <li>
            <a href="<?php echo MOBILE_URL; ?>member/order?status=close"><i class="icon-close" data-tip="<?php echo $homeinfo['order_status_buyer_close'];?>"></i>已关闭</a>
          </li>
          
        </ul>
      </div>
      <!--订单和收藏-->
      
      <div>
        <ul class="list">
          <li><a href="<?php echo MOBILE_URL; ?>member/order"><i class="icon-order"></i>全部订单</a></li>
          <li><a href="<?php echo MOBILE_URL; ?>member/refund"><i class="icon-fav"></i>退款维权</a></li>
        </ul>
      </div>
      
      <div>
        <ul class="list">
  
          <li><a href="<?php echo MOBILE_URL; ?>member/collect?type=goods"><i class="icon-collect-shop"></i>收藏商品</a></li>
          <li><a href="<?php echo MOBILE_URL; ?>member/collect?type=shop"><i class="icon-collect"></i>收藏店铺</a></li>
        </ul>
      </div>
      
      <!--管理地址-->
      <div>
        <ul class="list">
          <li><a href="<?php echo MOBILE_URL; ?>member/address"><i class="icon-address"></i>管理收货地址</a></li>
          <li><a href="<?php echo MOBILE_URL; ?>member/qrcode.detail?uid=<?php echo $memberInfo['uid'];?>"><i class="icon-qrcode"></i>推广二维码</a></li>
        </ul>
      </div>

      <div>
        <ul class="list">  
          <li><a href="<?php echo MOBILE_URL; ?>member/agent.detail"><i class="icon-agent-uid"></i>我的东家</a></li>
          <li><a href="<?php echo MOBILE_URL; ?>member/agent.level"><i class="icon-level"></i>排位</a></li>
          <li><a href="<?php echo MOBILE_URL; ?>member/bill.home"><i class="icon-bill"></i>我的账单</a></li>
        </ul>
      </div>

      <div>
        <ul class="list">
          <li><a href="<?php echo MOBILE_URL; ?>account/loginout"><i class="icon-login-out"></i>注销</a></li>
        </ul>
      </div>      
    </section>
    <footer data-role="footer">      
      <div data-role="data-widget" data-widget="home-menu" class="home-menu">
        <div class="widget_wrap">
          <ul class="box" ontouchstart="return true;">
            <li>
              <a id="btn_home" href="/shop/46" class=""><span class="category-1">&nbsp;</span><label>银品惠</label></a>
            </li>
            <li>
              <a id="btn_shopcart" href="/order/cart" class=""><span>&nbsp;</span><label>购物车</label></a>
            </li>
            <li>
              <a id="btn_center" href="/member/home" class="on"><span>&nbsp;</span><label>会员中心</label></a>
            </li>
          </ul>
        </div>
      </div>
    </footer>
  </div>
</body>

</html>
