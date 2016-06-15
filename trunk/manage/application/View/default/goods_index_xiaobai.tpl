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
        <div class="am-fl"><strong class="am-text-primary am-text-lg">商品管理</strong>
        </div>
        <div class="am-fr"><a href="{PHP_SELF}?m=goods.add" class="am-btn am-btn-primary am-radius"><i class="am-icon-fw am-icon-plus"></i>添加商品</a>
        </div>
      </div>
      <hr/>
      <div class="am-g">
        <div class="am-u-sm-6">
          <div class="am-btn-group" id="btns_sort">
            <button id="sort_addtime" data_id="sort_addtime" type="button" class="am-btn am-btn-lg am-btn-primary am-radius">上架时间 </button>
            <button id="sort_sales_count" data_id="sort_sales_count" type="button" class="am-btn am-btn-lg am-btn-default am-radius">销量 <i class="am-icon-long-arrow-down"></i></button>
            <button id="sort_inventory" data_id="sort_inventory" type="button" class="am-btn am-btn-lg am-btn-default am-radius">库存 <i class="am-icon-long-arrow-down"></i></button>
            <button id="sort_price" data_id="sort_price" type="button" class="am-btn am-btn-lg am-btn-default am-radius">价格 <i class="am-icon-long-arrow-down"></i></button>
          </div>
          <div class="am-btn-group">
            <button id="goods_down" data_id="goods_down" type="button" class="am-btn am-btn-lg am-btn-default am-radius">已下架</button>
          </div>
          <hr>
          <div class="am-btn-group">
          分类：
            <select id='_sel_0' class="_sel" level='0' style="display: none;">
            </select>
            <select id='_sel_1' class="_sel" level='1' style="display: none;">
            </select>
            <select id='_sel_2' class="_sel" level='2' style="display: none;">
            </select>
            <select id='_sel_3' class="_sel" level='3' style="display: none;">
            </select>
            <select id='_sel_4' class="_sel" level='4' style="display: none;">
            </select>
          </div>
          <input type="checkbox" id="cbk_is_just_this_cat" />
          <label for="cbk_is_just_this_cat">查询当前分类以下所有产品</label>
          &nbsp;&nbsp;&nbsp;当前分类：
          <label id="_sel_lable" title="点击此处清空当前分类" data_value=""></label>
          <button id="btn_cate_sel" type="button" class="am-btn am-btn-default am-radius"><i class="am-icon-filter"></i> 查询</button>
        </div>

        <div class="am-u-sm-3 am-form">
          <select id="goods_type">
            <option value="0">所有商品类型</option>
            {$goods_type_option}
          </select>
          <span class="am-form-caret"></span>             
        </div>

        <div class="am-u-sm-3">
          <div class="am-input-group am-input-group-lg">
            <span class="am-input-group-label am-radius"><i class="am-icon-search am-icon-fw"></i></span>
            <input id="txt_keyword" type="text" class="am-form-field am-radius" placeholder="输入关键词搜索……">
            <span class="am-input-group-btn">
            <button class="am-btn am-btn-default" type="button" id="search_button">搜索</button>
      						</span>
          </div>
        </div>
      </div>
      <hr>
      <div class="am-g">
        <div class="am-u-lg-12">
          <button class="am-btn am-btn-sm am-btn-primary cbk_all"><i class="am-icon-check-circle"></i> 全选</button>
          <button class="am-btn am-btn-sm am-btn-primary cbk_no_all"><i class="am-icon-check-circle-o"></i> 反选</button>
          <button class="am-btn am-btn-sm am-btn-success cbk_on_all"><i class="am-icon-level-up"></i> 批量上架</button>
          <button class="am-btn am-btn-sm am-btn-warning cbk_off_all"><i class="am-icon-level-down"></i> 批量下架</button>
          <button class="am-btn am-btn-sm am-btn-danger cbk_del_all"><i class="am-icon-trash"></i> 批量删除</button>
          <select id='sel_0' class="sel" level='0' style="display: none;">
          </select>
          <select id='sel_1' class="sel" level='1' style="display: none;">
          </select>
          <select id='sel_2' class="sel" level='2' style="display: none;">
          </select>
          <select id='sel_3' class="sel" level='3' style="display: none;">
          </select>
          <select id='sel_4' class="sel" level='4' style="display: none;">
          </select>
          <button id="sel_cat_all" class="am-btn am-btn-sm am-btn-primary"><i class="am-icon-list"></i> 设置分类</button>          
        </div>
      </div>
      <div class="am-g">
        <div class="am-u-lg-12">
          <table class="am-table am-table-striped am-table-hover table-main">
            <thead>
              <tr>
                <th width="3%"></th>
                <th width="5%">商品ID</th>                
                <th>商品名称</th>
                <th width="5%">价格</th>                
                <th width="5%">利润</th>
                <th width="5%">销量</th>
                <th width="5%">库存</th>
                <th width="8%">分类</th>
                <th width="8%">更新时间</th>
                <th width="10%" class="am-text-center">操作</th>
              </tr>
            </thead>
            <tbody>
              <!--{loop $rs $k $v}-->
              <tr>
                <td class="am-text-middle">
                  <span data-item-id="{$v->goods_id}" class="am-icon-check-square-o am-hide"></span>
                  <span class="am-icon-square-o"></span>
                </td>
                <td class="am-text-middle">{$v->goods_id}</td>       
                <td class="td_left">
                  <a href="{MOBILE_URL}goods/{$v->goods_id}.html" target="_blank" class="list_i_a l_txt rel">
											<img class="am-comment-avatar" width="60" height="60" src="{$v->goods_image_id}">{$v->goods_name}</a>
                  <!--{if !empty($goods_sku_array[$v->goods_id])}-->
                  <a class="a_sku">点此查看规格</a>
                  <div class="pre_sku">
                    <div class="div_sku">
                      <dl class="dl_header">
                        <dd>规格</dd>
                        <dd>售价</dd>                        
                        <dd>佣金</dd>
						            <dd>库存</dd>
                        <dd>销量</dd>
                      </dl>
                      <!--{loop $goods_sku_array[$v->goods_id] $goods_sku_object}-->
                      <dl>
                        <dd>{$goods_sku_object->sku_name}</dd>
                        <dd>{$goods_sku_object->price}</dd>									
                        <dd>{$goods_sku_object->commission_fee}</dd>
                        <dd>{$goods_sku_object->stock}</dd>
                        <dd>{$goods_sku_object->sales_volume}</dd>
                      </dl>
                      <!--{/loop}-->
                    </div>
                    <dl>
                      <dd style=" width: 100%;">
                        <label style=" width: 100%;" class="am-icon-level-down span_more">展开更多</label>
                      </dd>
                    </dl>
                  </div>
                  <!--{/if}-->
                </td>
                <td class="am-text-middle">{$v->goods_price}</td>				
                <td class="am-text-middle">{$v->commission_fee}</td>
                <td class="am-text-middle">{$v->sales_volume}</td>
                <td class="am-text-middle">{$v->goods_stock}</td>
                <td class="am-text-middle">
                  <!--{loop $v->goods_cat_id $goods_cat_id}-->
                  ${echo empty($goods_category_array[$goods_cat_id]) ? '' : $goods_category_array[$goods_cat_id];}
                  <!--{/loop}-->
                </td>
                <td class="am-text-middle">{$v->goods_modify_time}</td>
                <td class="am-text-middle am-text-center">
                  <a class="am-btn am-btn-default am-btn-xs am-text-secondary am-fl am-margin-right-xs" target="_blank" href="{PHP_SELF}?m=goods.add&id={$v->goods_id}">
                    <span class="am-icon-pencil-square-o am-padding-left-xs"></span>编辑
                  </a>
                  <a class="am-btn am-btn-default am-btn-xs am-text-danger am-hide-sm-only am-fl a_del" data-item-id='{$v->goods_id}'>
                    <span class="am-icon-trash-o am-padding-left-xs"></span>删除
                  </a>
                </td>
              </tr>
              <!--{/loop}-->
            </tbody>
            <tfoot>
              <tr>
                <td colspan="9" class="am-text-center">{$page}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="am-g">
        <div class="am-u-lg-12">
        <button class="am-btn am-btn-sm am-btn-primary cbk_all"><i class="am-icon-check-circle"></i> 全选</button>
          <button class="am-btn am-btn-sm am-btn-primary cbk_no_all"><i class="am-icon-check-circle-o"></i> 反选</button>
          <button class="am-btn am-btn-sm am-btn-success cbk_on_all"><i class="am-icon-level-up"></i> 批量上架</button>
          <button class="am-btn am-btn-sm am-btn-warning cbk_off_all"><i class="am-icon-level-down"></i> 批量下架</button>
          <button class="am-btn am-btn-sm am-btn-danger cbk_del_all"><i class="am-icon-trash"></i> 批量删除</button>
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
var sort = '{$sort}';
var status = '{$status}';
var query_string = '{$query_string}';
var goods_cat_id = '{$_goods_cat_id}';
var just_this_goods_cat_id = '{$just_this_goods_cat_id}';
var param = {
  sort: '{$sort}',
  status: '{$status}',
  search_keyword: '{$query_string}',
  goods_cat_id: '{$_goods_cat_id}',
  just_this_goods_cat_id: '{$just_this_goods_cat_id}',
  goods_cat_name: '{$goods_cat_name}'
}
</script>
<script type="text/javascript" src="{STATIC_URL}common/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="{STATIC_URL}common/assets/js/amazeui.js"></script>
<script type="text/javascript" src="{STATIC_URL}common/assets/js/app.js"></script>
<script type="text/javascript" src="{$BASE_V}js/common.js"></script>
<script src="{STATIC_URL}js/jquery-plugin/ui/minified/jquery.cookie-min.js" type="text/javascript"></script>
<script type="text/javascript" src="{$BASE_V}js/modal_html.js"></script>
<script type="text/javascript" src="{$BASE_V}js/supplier/goods_index_xiaobai.js?v=20160110"></script>
