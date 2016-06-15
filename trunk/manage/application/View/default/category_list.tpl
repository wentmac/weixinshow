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
  
  <style type="text/css">
.category_list{ float:left; margin-left:15px; width:100%; padding:0px}
.category_list ul{ padding:0px}
.category_list dl{ float:left; width:100%; border-bottom:1px solid #D7D7D7; line-height:28px}
.category_list dl dd{ float:left; }
.category_list dl .cl_one{width:20%; text-align:left}
.category_list dl .cl_two{width:10%; text-align:left}
.category_list dl .cl_three{width:45%; text-align:left}
.category_list dl .cl_four{width:25%; text-align:center}

.category_list .cate{ float:left; width:100%; }
.category_list .cate img{ float:left; top:7px; position:relative; margin-right:5px; cursor:pointer}

.category_list .category_list_bg{ background:#f6f9fd}  
  </style>
</head>

<body>
  <!--{template inc/header_paul}-->
  <div class="am-cf admin-main">
    <!--{template inc/sidebar_paul}-->
    <!-- content start -->
    <div class="admin-content">
      <div class="am-cf am-padding">
        <div class="am-fl"><strong class="am-text-primary am-text-lg">分类管理</strong>
        </div>
        <div class="am-fr">
          <a href="{PHP_SELF}?m=category.add" class="am-btn am-btn-primary am-radius"><i class="am-icon-fw am-icon-plus"></i>添加分类</a>
        </div>
      </div>
      <hr>
      <div class="am-g">
        <div class="am-u-lg-12">
          <button class="am-btn am-btn-sm am-btn-primary cbk_all" onclick="open_cat()"><i class="am-icon-check-circle"></i> 全部展开</button>
          <button class="am-btn am-btn-sm am-btn-primary cbk_no_all"  onclick="close_cat()"><i class="am-icon-check-circle-o"></i> 全部收缩</button>                            
        </div>
      </div>
      <div class="am-g">
        <div class="am-u-lg-12">
          <table class="am-table am-table-striped table-main">
            <thead>
              <tr>                
                <th width="20%">名称</th>                
                <th width="20%">别名</th>
                <th width="45%">描述</th>                
                <th width="35%">操作</th>              
              </tr>
            </thead>

            <tbody id="tbody_list">
                <tr>            
                  <td colspan="4" style="border-bottom:0px">
                  {$category_list}
                  </td>
                </tr>
            </tbody>

          </table>
        </div>
      </div>      
      <hr/>
    </div>
    <!-- content end -->
  </div>
  <!--{template inc/footer_paul}-->
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
<script type="text/javascript" src="{STATIC_URL}js/jquery-plugin/jquery.pagination-min.js"></script>
<script type="text/javascript" src="{$BASE_V}js/category/list.js?v=1"></script>