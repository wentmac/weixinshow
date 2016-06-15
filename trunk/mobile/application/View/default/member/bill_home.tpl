<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">
  <meta content="telephone=no" name="format-detection">
  <meta name="apple-touch-fullscreen" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <title>我的账单 - {$config[cfg_webname]}</title>
  <link href="{$BASE_V}css/common/base.css" type="text/css" rel="stylesheet">
  <link href="{$BASE_V}css/order/kd.css" type="text/css" rel="stylesheet">
  <style>
.kd_loaded_times p{
    position: relative;
    display: block;
    height: 44px;
    line-height: 44px;
    color: #333333;
    -webkit-box-align: center;
}  
.kd_loaded_times p a{
    width: 100%;
    display: block;
}  
.kd_loaded_times p::after{
    content: "";
    display: inline-block;
    position: absolute;
    right: 0;
    top: 50%;
    margin-top: -4px;
    width: 8px;
    height: 8px;
    border: 1px solid #71787b;
    border-width: 1px 1px 0 0;
    -webkit-transform: rotate(45deg);
}  
.rebind a{
  text-decoration: underline;
  color: red;
}
.kd_status .btn {
    display: inline-block;
    float: right;
    margin-left: 5px;
    color: #666666;
    width: 75px;
    font-size: 13px;
    line-height: 25px;
    height: 25px;
    background-color: #FFFFFF;
    border: 1px solid #666666;
    border-radius: 2px;
    margin-right: 10px;
}
.btncancel {
    display: block;
    height: 44px;
    line-height: 44px;
    text-align: center;
    border-radius: 3px;
    font-size: 15px;
}
#kd_status input.money{
  border:1px solid #ccc;
  width: 64px;
  line-height: 25px;
}
.history_money{
  float: left;
}
.current_money{
  float: left;
}
.settle_div{
  float:left;
  margin-left: 86px;
}
.settle_list{
  float: right;
  margin-right: 20px;
}
.settle_list a{
  color:red;
  text-decoration: underline;
}
.settle span{
  float:left;
}
  </style>
</head>

<body>
  <header id="common_hd" class="c_txt rel">
      <a id="hd_back" class="abs comm_p8" href="{$referer_url}">返回</a>
      <a id="common_hd_logo" class="t_hide abs common_hd_logo">我的账单</a>
      <h1 class="hd_tle">我的账单</h1>
      <a id="hd_enterShop" class="hide abs" href="{MOBILE_URL}member/home" style="display: block;"> <span id="hd_enterShop_img" class="abs"> <img class="block" src="" width="32" height="32" style="display: block;"> </span>会员中心</a>
  </header>

  <header id="kd_status" class="kd_tle kd_status">
      <h3>提现的微信账号绑定<font color="red">重要</font></h3>
        <p style="float:left;margin-right: 10px"><img src="{$member_setting_info->avatar_imgurl}" width="55" height="55" class="left" id="avatar_imgurl"></p>
        <p>
          <span class="rebind">{$member_setting_info->nickname} <a style="cursor:pointer" id="update_avatar">更新头像</a></span>
          <span class="rebind">如果不是您的微信账号，请<a href="{MOBILE_URL}member/bill.authorize">重新绑定</a></span>
        </p>                      
        
  </header>

<!--{if $member_setting_info->settle_status==1}-->
  <section id="kd_status">
      <span style="color:red">
        <!--{$member_setting_info->error_message}-->
      </span>      
  </section>
<!--{/if}-->

  <section id="kd_status" class="kd_tle kd_status settle">
      <span class="leixing" style="width:100%">
        <div class="history_money">累计收入：<em>￥{$history_money}</em></div>
        <div class="settle_list"><a href="{MOBILE_URL}member/settle">提现历史</a></div>
      </span>
      <span class="order" style="width:100%;">
        <div class="current_money">账户余额：<em>￥{$current_money}</em></div>
        <div class="settle_div" id="settle_div"><input type="text" class="money" name="money" id="money" placeholder="提现金额"></div>
        <div class="btn btncancel" id="settle_button"><a href="javascript:void(0);"><em>提现</em></a></div>
      </span>
  </section>

  <section>
    <div class="kd_wrap">
      <div class="kd_title">分账单</div>      
      <div id="kd_loaded_times" class="kd_loaded_times">
        <p><a href="{MOBILE_URL}member/bill.index?status=in">收入:￥{$in}</a></p>
        <p id="kd_wd_sn"><a href="{MOBILE_URL}member/bill.index?status=in">等确认:￥{$waiting_confirm}</a></p>        
        <p id="kd_order_time"><a href="{MOBILE_URL}member/bill.index?status=in">提现中:￥{$expense_withdrawals_ing}</a></p>
        <p id="kd_pay_time" style="border:none"><a href="{MOBILE_URL}member/bill.index?status=in">已提现:￥{$expense_withdrawals_success}</a></p>                
        
      </div>
    </div>
  </section>  

</body>

</html>
<script type="text/javascript">
  var index_url = '{INDEX_URL}';
  var mobile_url = '{MOBILE_URL}';
  var static_url = '{STATIC_URL}';
  var base_v = '{$BASE_V}';
  var php_self = '{PHP_SELF}';
  var current_money = parseFloat('{$current_money}');
</script>
<script type="text/javascript" src="{STATIC_URL}js/jquery/1.11.2/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="{$BASE_V}js/common.js"></script>
<script type="text/javascript" src="{$BASE_V}js/bill_home.js?v=2"></script>