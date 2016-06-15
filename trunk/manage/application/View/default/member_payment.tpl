<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <link rel="apple-touch-icon-precomposed" href="/i/app-icon72x72@2x.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>用户中心</title>
  <meta name="description" content="用户中心">
  <meta name="keywords" content="index">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <meta name="renderer" content="webkit">
  <meta name="apple-mobile-web-app-title" content="Amaze UI" />
  <link href="{$BASE_V}assets/css/amazeui.css" rel="stylesheet" type="text/css">
  <link href="{$BASE_V}assets/css/admin.css" rel="stylesheet" type="text/css">
</head>

<body>
  <!--{template inc/header_paul}-->
  <div class="am-cf admin-main">
    <!--{template inc/sidebar_paul}-->
    <!-- content start -->
    <div class="admin-content">
      <div class="am-cf am-padding">
        <div class="am-fl"><strong class="am-text-primary am-text-lg">支付设置</strong></div>
      </div>
      <hr/>
      <div class="am-container" style="margin-left: 0">
        <form class="am-form am-form-horizontal">
          <div class="am-form-group">
            <label class="am-u-sm-2 am-form-label">地区</label>
            <div class="am-u-sm-8 am-u-end">
              <select class="am-radius" id="bank_proid" name="bank_proid" data-am-selected="{btnWidth:100,maxHeight:300}">
                <option value="0">省份</option>
              </select>
              <select class="am-radius" id="bank_cityid" name="bank_cityid" data-am-selected="{btnWidth:100,maxHeight:300}">
              <option value="0">地区</option>
              </select>
            </div>
          </div>
          <div class="am-form-group">
            <label for="goods_name" class="am-u-sm-2 am-form-label">银行</label>
            <div class="am-u-sm-8 am-u-end">
              <select class="am-radius" id="bank_id" name="bank_id" data-am-selected="{btnWidth:204,maxHeight:300}">
                <option value="">请选择银行</option>
                {$bank_id_option}
              </select>
            </div>
          </div>
          <div class="am-form-group">
            <label for="i_des" class="am-u-sm-2 am-form-label">卡号</label>
            <div class="am-u-sm-5 am-u-end">
              <input class="am-radius" type="text" id="bank_cardnum" name="bank_cardnum" value="{$editinfo->bank_cardnum}" placeholder="银行卡号">
            </div>
          </div>
          <div class="am-form-group">
            <label for="i_des" class="am-u-sm-2 am-form-label">开户名</label>
            <div class="am-u-sm-5 am-u-end">
              <input class="am-radius" type="text" id="bank_account" name="bank_account" value="{$editinfo->bank_account}" placeholder="开户姓名">
            </div>
          </div>
          <hr>
          <div class="am-form-group">
            <label for="" class="am-u-sm-2 am-form-label"></label>
            <div class="am-u-sm-2 am-u-end">
              <button id="btn_submit" type="button" class="am-btn am-btn-primary am-btn-block am-radius" data-for-gaq="商品管理-提交商品"><i class="am-icon-check-circle"></i> 提　交</button>
            </div>
          </div>
        </form>
      </div>
      <hr/>
    </div>
    <!-- content end -->
  </div>
  <!--{template inc/footer_paul}-->
  <script type="text/javascript" src="{$BASE_V}assets/js/jquery.min.js"></script>
  <script type="text/javascript" src="{$BASE_V}assets/js/amazeui.js"></script>
  <script type="text/javascript">
  var index_url = '{MOBILE_URL}';
  var static_url = '{STATIC_URL}';
  var base_v = '{$BASE_V}';
  var php_self = '{PHP_SELF}';
  var global_bank_pid = '{$editinfo->bank_pid}';
  var global_bank_cityid = '{$editinfo->bank_cityid}';
  var global_bank_id = '{$editinfo->bank_id}';
  var postField = new Object();
  </script>
  <script type="text/javascript" src="{$BASE_V}js/modal_html.js"></script>
  <script type="text/javascript" src="{$BASE_V}js/saller/member_payment.js"></script>
  <script type="text/javascript" src="{$BASE_V}js/common.js"></script>
  <script src="{STATIC_URL}js/jquery-plugin/ui/minified/jquery.cookie-min.js" type="text/javascript"></script>
</body>

</html>
