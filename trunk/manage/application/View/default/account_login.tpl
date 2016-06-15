<!DOCTYPE html>
<html>

<head lang="en">
  <meta charset="UTF-8">
  <title>用户登录－{$config[cfg_webname]}</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="format-detection" content="telephone=no">
  <meta name="renderer" content="webkit">
  <meta http-equiv="Cache-Control" content="no-siteapp" />
  <link rel="stylesheet" href="{STATIC_URL}common/assets/css/amazeui.min.css" />
  <style>
  .header {
    text-align: center;
  }
  
  .header h1 {
    font-size: 200%;
    color: #333;
    margin-top: 30px;
  }
  
  .header p {
    font-size: 14px;
  }
  </style>
</head>

<body>
  <div class="header">
    <div class="am-g">
      <h1>用户登录</h1>
      <p>银品惠是一个全新的社交电商平台！</p>
    </div>
    <hr />
  </div>
  <div style="width:330px; margin:0 auto">
      <h3>第三方登录</h3>
      <hr>
      <div class="am-btn-group" id="signup-wrapper">
        <a href="#" class="am-btn am-btn-secondary am-btn-sm am-radius" data-type="qq"><i class="am-icon-qq am-icon-sm" ></i> QQ登录</a>
        <a href="#" class="am-btn am-btn-success am-btn-sm am-radius" data-type="wechat"><i class="am-icon-weixin am-icon-sm"></i> 微信登录</a>
        <a href="#" class="am-btn am-btn-primary am-btn-sm am-radius" data-type="weibo"><i class="am-icon-weibo am-icon-sm"></i> 微博登录</a>
      </div>
      <br>
      <br>
      <form method="post" class="am-form" id="form_login">
      <div class="am-form-group">
        <label for="account_name">手机号：</label>
        <input type="text" required minlength="11" maxlength="11" class="am-radius" pattern="^1([3]|[5]|[8]|[4]|[7])[0-9]{9}$" name="account_name" id="account_name" placeholder="手机号" value="">
      </div>
      <div class="am-form-group">
        <label for="account_pwd">密码：</label>
        <input type="password" required minlength="6" name="account_pwd" class="am-radius" id="account_pwd" placeholder="密码" value="">
      </div>
        <label for="rember_pwd">
        <input id="rember_pwd" name="rember_pwd" value="1" type="checkbox"> <span style="font-weight:normal">记住密码</span>
        </label>
        <br />
        <div class="am-cf">
          <button id="btn_submit" type="submit" class="am-btn am-btn-primary am-radius"><i class="am-icon-lock am-icon-fw"></i> 登　录</button>
          <a class="am-btn am-btn-default am-radius am-btn-sm am-fr" href="{MOBILE_URL}{PHP_SELF}?m=account.forget">忘记密码 ^_^?</a>
        </div>
      </form>
      <hr>
      <p>还没有注册？<a href="{MOBILE_URL}{PHP_SELF}?m=account.register">马上注册></a></p>
  </div>
  <footer>
    <hr>
    <p class="am-padding-left am-text-center">© 2015 银品惠, Inc.</p>
  </footer>
  <script type="text/javascript" src="{STATIC_URL}common/assets/js/jquery.min.js"></script>
  <script type="text/javascript" src="{STATIC_URL}common/assets/js/amazeui.js"></script>
  <script type="text/javascript" src="{STATIC_URL}js/modal_html.js"></script>
  <script type="text/javascript">
  var index_url = '{MOBILE_URL}';
  var static_url = '{STATIC_URL}';
  var base_v = '{$BASE_V}';
  var php_self = '{PHP_SELF}';
  var postField = new Object();
  var mobile_url = '{MOBILE_URL}';
  </script>
  <script type="text/javascript" src="{$BASE_V}js/account_login.js"></script>
</body>

</html>
