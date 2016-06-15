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
  <link href="{$BASE_V}css/base.css" type="text/css" rel="stylesheet">
  <link href="{STATIC_URL}js/webuploader/webuploader.css" rel="stylesheet" type="text/css">
</head>

<body>
  <!--{template inc/header_paul}-->
  <div class="am-cf admin-main">
    <!--{template inc/sidebar_paul}-->
    <!-- content start -->
    <div class="admin-content">
      <div class="am-cf am-padding">
        <div class="am-fl"><strong class="am-text-primary am-text-lg">个人设置</strong></div>
      </div>
      <hr/>
      <div class="am-container" style="margin-left: 0">
        <form class="am-form am-form-horizontal">
          <div class="am-form-group">
            <label for="" class="am-u-sm-2 am-form-label">姓　名</label>
            <div class="am-u-sm-5 am-u-end">
              <div class="am-input-group">
                <span class="am-input-group-label am-radius"><i class="am-icon-user am-icon-fw"></i></span>
                <input type="text" class="am-form-field am-radius" id="txt_realname" name="txt_realname" placeholder="请填写您的身份证上的姓名" value="{$editinfo->realname}">
              </div>
            </div>
          </div>
          <div class="am-form-group">
            <label for="" class="am-u-sm-2 am-form-label">身份证号</label>
            <div class="am-u-sm-5 am-u-end">
              <div class="am-input-group">
                <span class="am-input-group-label am-radius"><i class="am-icon-credit-card am-icon-fw"></i></span>
                <input type="text" class="am-form-field am-radius" id="txt_cid" name="txt_cid" placeholder="请填写您的身份证号" value="{$editinfo->idcard}">
              </div>
            </div>
          </div>
          <div class="am-form-group">
            <label for="" class="am-u-sm-2 am-form-label">身份证正面</label>
            <div class="am-u-sm-10 am-u-end">
              <div id="fileList1" class="uploader-list am-fl"></div>
              <div id="filePicker1" class="am-fl am-u-sm-3">上传图片</div>
            </div>
          </div>
          <div class="am-form-group">
            <label for="" class="am-u-sm-2 am-form-label">身份证反面</label>
            <div class="am-u-sm-10 am-u-end">
              <div id="fileList2" class="uploader-list am-fl"></div>
              <div id="filePicker2" class="am-fl am-u-sm-3">上传图片</div>
            </div>
          </div>
          <div class="am-form-group">
            <label for="" class="am-u-sm-2 am-form-label">手持身份证照片</label>
            <div class="am-u-sm-10 am-u-end">
              <div id="fileList3" class="uploader-list am-fl"></div>
              <div id="filePicker3" class="am-fl am-u-sm-3">上传图片</div>
            </div>
          </div>
          <hr>
          <div class="am-form-group am-cf">
            <label for="" class="am-u-sm-2 am-form-label"></label>
            <div class="am-u-sm-2 am-u-end">
              <button id="btn_submit" type="button" class="am-btn am-btn-primary am-btn-block am-radius" data-for-gaq="商品管理-提交商品"><i class="am-icon-check-circle"></i> 提　交</button>
            </div>
          </div>
        </form>
        <hr>
        <h2>示例：</h2>
        <p><img src="{$BASE_V}/image/IDCard-demo.jpg"></p>
      </div>
      <hr/>
    </div>
    <!-- content end -->
  </div>
  <!--{template inc/footer_paul}-->
  <script type="text/javascript" src="{$BASE_V}assets/js/jquery.min.js"></script>
  <script type="text/javascript" src="{$BASE_V}assets/js/amazeui.js"></script>
  <script type="text/javascript">
  var index_url = '{INDEX_URL}';
  var static_url = '{STATIC_URL}';
  var base_v = '{$BASE_V}';
  var php_self = '{PHP_SELF}';
  var global_idcard_image_id = '{$editinfo->idcard_image_id}';
  var global_idcard_positive_image_id = '{$editinfo->idcard_positive_image_id}';
  var global_idcard_negative_image_id = '{$editinfo->idcard_negative_image_id}';
  var global_idcard_verify = '{$editinfo->idcard_verify}';
  var global_idcard_image_url = '{$editinfo->idcard_image_url}';
  var global_idcard_positive_image_url = '{$editinfo->idcard_positive_image_url}';
  var global_idcard_negative_image_url = '{$editinfo->idcard_negative_image_url}';
  var postField = new Object();
  postField.idcard_image_url = global_idcard_image_url;
  postField.idcard_positive_image_id = global_idcard_positive_image_id;
  postField.idcard_negative_image_id = global_idcard_negative_image_id;
  </script>
  <script type="text/javascript" src="{STATIC_URL}js/webuploader/webuploader.js"></script>
  <script type="text/javascript" src="{$BASE_V}js/modal_html.js"></script>
  <script type="text/javascript" src="{$BASE_V}js/saller/member_idcard.js"></script>
  <script type="text/javascript" src="{$BASE_V}js/common.js"></script>
<script src="{STATIC_URL}js/jquery-plugin/ui/minified/jquery.cookie-min.js" type="text/javascript"></script>
</body>

</html>
