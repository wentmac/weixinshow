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
  <link href="{STATIC_URL}common/assets/css/amazeui.css" rel="stylesheet" type="text/css">
  <link href="{STATIC_URL}common/assets/css/admin.css" rel="stylesheet" type="text/css">
  <link href="{$BASE_V}css/base.css" type="text/css" rel="stylesheet">
  <link href="{$BASE_V}css/page.css" type="text/css" rel="stylesheet">
  <link href="{$BASE_V}css/form_list.css" type="text/css" rel="stylesheet">
</head>

<body>
  <!--{template inc/header_paul}-->
  <div class="am-cf admin-main">
    <!--{template inc/sidebar_paul}-->
    <!-- content start -->
    <div class="admin-content">
      <div class="am-cf am-padding">
        <div class="am-fl">
            <strong class="am-text-primary am-text-lg">内容发布向导</strong>
        </div>        
      </div>
      <hr/>      
      
      <div class="am-g">
        <div class="am-u-lg-12">
          <table class="am-table am-table-striped am-table-hover table-main">
            <thead>
              <tr>
                <th width="10%" class="am-text-center">频道ID</th>
                <th width="50%" class="am-text-center">频道内容模型</th>                                
                <th width="40%" class="am-text-center">操作选项</th>
              </tr>
            </thead>

            <tbody>
              <!--{loop $channeltype $k $v}-->
                <tr>                  
                <td class="am-text-middle">{$k}</td>
                <td class="am-text-middle">{$v}</td>       
                <td class="am-text-middle"><a href="{PHP_SELF}?m=category">管理栏目</a> | <a href="{PHP_SELF}?m=archives.arclist&channelid=$k">管理内容</a> | <a href="{PHP_SELF}?m=archives.catgoto&channelid=$k">发布内容</a></td>                
              </tr>    
              <!--{/loop}-->          
            </tbody>

            <tfoot>
              <tr>
                <td colspan="9" class="am-text-center"></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>      
      <hr/>
    </div>
    <!-- content end -->
  </div>
  <!--{template inc/footer_paul}-->
  <div class="am-popup" id="my-popup">
    <div class="am-popup-inner">
      <div class="am-popup-hd">
        <h4 class="am-popup-title">批量调整售价</h4>
        <span data-am-modal-close class="am-close">&times;</span>
      </div>
      <div class="am-popup-bd">
        <div class="am-padding-sm">
          在原始价格的基础上：
          <div class="am-form-group">
            <label>
              <input type="radio" name="set_price_type" value="plus" checked> <span class="am-text-danger">+加价</span>
            </label>
            <label>
              <input type="radio" name="set_price_type" value="less"> <span class="am-text-success">-减价</span>
            </label>
          </div>
          <div class="am-g">
            <div class="am-u-sm-7 am-margin-0 am-padding-0">
              <label>
                <div class="am-input-group am-input-group-xs">
                  <span class="am-input-group-label"><input type="radio" name="set_price_class" value="fixed"> 固定的金额￥</span>
                  <input type="text" class="am-form-field" id="fixed_value" placeholder="输入固定的金额">
                </div>
              </label>
              <label>
                <div class="am-input-group am-input-group-xs">
                  <span class="am-input-group-label">
                	<input type="radio" name="set_price_class" value="percent" checked="checked"> 价格百分比%</span>
                  <input type="text" class="am-form-field" id="percent_value" placeholder="请输入总价加/减百分比">
                </div>
              </label>
            </div>
          </div>
          <a class="am-btn am-btn-primary" id="set_price_button"><i class="am-icon-check"></i> 提交</a>
          <div class="am-text-warning am-text-xs">温馨提示：批量调整价格时，如遇到调整后的价格小于成本价（原始价格减去利润）时，系统会自动调回原价，以避免给您带来亏损！</div>
        </div>
      </div>
      <div class="doc-example"></div>
    </div>
  </div>
</body>

</html>
<script type="text/javascript">
var index_url = '{INDEX_URL}';
var static_url = '{STATIC_URL}';
var base_v = '{$BASE_V}';
var php_self = '{PHP_SELF}';
</script>
<script type="text/javascript" src="{STATIC_URL}common/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="{STATIC_URL}common/assets/js/amazeui.js"></script>
<script type="text/javascript" src="{STATIC_URL}common/assets/js/app.js"></script>
<script type="text/javascript" src="{$BASE_V}js/common.js"></script>
<script src="{STATIC_URL}js/jquery-plugin/ui/minified/jquery.cookie-min.js" type="text/javascript"></script>
<script type="text/javascript" src="{$BASE_V}js/modal_html.js"></script>