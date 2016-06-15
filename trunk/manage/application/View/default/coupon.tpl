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
    <div class="admin-content">
      <div class="am-cf am-padding">
        <div class="am-fl"><strong class="am-text-primary am-text-lg">代金券管理</strong>
        </div>
      </div>
      <hr>
      <div class="am-g">
        <div class="am-u-sm-1">
            <label for="doc-ipt-email-1">代金券总额度</label>
            <p>￥{$member_mall_info->mall_coupon}</p>            
        </div>
        <div class="am-u-sm-1">
            <label for="doc-ipt-email-1">已经生成</label>
            <p>￥{$coupon_money_created_total}</p>            
        </div>
        <div class="am-u-sm-2">
            <label for="doc-ipt-email-1">还可以生成</label>
            <p>￥{$coupon_money_credits}</p>            
        </div>
        <div class="am-u-sm-2">
            <label for="doc-ipt-email-1">代金券数量</label>
            <input type="text" class="am-form-field" id="coupon_num" name="coupon_num" placeholder="要生成的代金券的张数">                        
        </div>  
        <div class="am-u-sm-2">
            <label for="doc-ipt-email-1">代金券面额</label>            
            <select data-am-selected id="coupon_value" name="coupon_value">

                <!--{loop $coupon_value_array $key $value}-->
                <option value="{$value}"{if $key==0} selected{/if}>{$value}元</option>
                <!--{/loop}-->                
            </select>            
        </div>                
        <div class="am-u-sm-1 am-fl">
            <a href="javascript:void(0);" class="am-btn am-btn-primary am-radius" id="coupon_create_button"><i class="am-icon-fw am-icon-plus"></i>生成代金券</a>            
        </div>        
        
      </div>
      <hr/>
      <div class="am-g">
        <div class="am-u-sm-7">
          <div class="am-btn-group" id="condition_list">
            <button id="sort_addtime" data_id="sort_addtime" type="button" class="am-btn am-btn-lg am-btn-primary am-radius" data_status="0">全部 </button>
            <button id="sort_sales_count" data_id="sort_sales_count" type="button" class="am-btn am-btn-lg am-btn-default am-radius" data_status="-1">未使用 <i class="am-icon-long-arrow-down"></i></button>
            <button id="sort_inventory" data_id="sort_inventory" type="button" class="am-btn am-btn-lg am-btn-default am-radius" data_status="1">已使用 <i class="am-icon-long-arrow-down"></i></button>            
          </div>          
        </div>
        <div class="am-u-sm-5">
          <div class="am-input-group am-input-group-lg">
            <span class="am-input-group-label am-radius"><i class="am-icon-search am-icon-fw"></i></span>
            <input id="txt_keyword" type="text" class="am-form-field am-radius" placeholder="输入代金券号码搜索……">
            <span class="am-input-group-btn">
            <button class="am-btn am-btn-default" type="button" id="coupon_code_submit">搜索</button>
      						</span>
          </div>
        </div>
      </div>
      <hr>
      <div class="am-g">
        <div class="am-u-lg-12">
          <table class="am-table am-table-striped am-table-hover table-main">
            <thead>
              <tr>                
                <th width="5%">ID</th>
                <th width="30%">代金券</th>
                <th width="10%">金额</th>                
                <th width="10%">使用状态</th>                
                <th width="10%">生成时间</th>                
                <th width="20%">订单号</th>                                               
                <th width="10%">使用时间</th>                                               
              </tr>
            </thead>

              <tbody  id="order_list_loading">
                <tr>
                  <td colspan="10" class="am-text-center">
                    <div class="am-modal-hd am-text-center"><img  src="{$BASE_V}img/loading.gif">正在载入...</div>
                  </td>
                </tr>

              </tbody>
              <tbody style="display: none;" id="order_list_nofund">
                <tr>
                  <td class="am-text-center" colspan="10">
                        <div class="am-modal-hd">很抱歉，没有找到结果...</div>
                    </td>
                  
                </tr>

              </tbody>
              <tbody id="tbody_order_list">

              </tbody>
              <tfoot>
                <tr>
                  <td colspan="10" id="roomListPages" class="am-text-center page pagination"></td>
                </tr>
              </tfoot>
          </table>
        </div>
      </div>

      <hr/>
    </div>
    
  </div>
  <!--{template inc/footer_paul}-->
</body>

</html>
<script type="text/javascript">
var index_url = '{MOBILE_URL}';
var static_url = '{STATIC_URL}';
var base_v = '{$BASE_V}';
var php_self = '{PHP_SELF}';
var coupon_money_credits = {$coupon_money_credits};
var searchParameter = $searchParameter;
</script>
<script type="text/javascript" src="{STATIC_URL}common/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="{STATIC_URL}common/assets/js/amazeui.js"></script>
<script type="text/javascript" src="{STATIC_URL}common/assets/js/app.js"></script>
<script type="text/javascript" src="{$BASE_V}js/common.js"></script>
<script src="{STATIC_URL}js/jquery-plugin/ui/minified/jquery.cookie-min.js" type="text/javascript"></script>
<script type="text/javascript" src="{$BASE_V}js/modal_html.js"></script>
<script type="text/javascript" src="{STATIC_URL}js/formValidate.js?v=20160110"></script>
<script type="text/javascript" src="{STATIC_URL}js/jquery-plugin/jquery.pagination-min.js"></script>
<script type="text/javascript" src="{$BASE_V}js/coupon.js?v=20160110"></script>
