<?php if (!class_exists('template', false)) die('Access Denied');
0
|| self::check('default\order/fake.tpl', 'D:\Web\Work\www.090.cn\trunk\admin\application\View\default\order\fake.tpl', 1455457659)
;?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link href="<?php echo $BASE_V;?>layout.css" rel="stylesheet" type="text/css" />
  <title>快速购物</title>
  <script src="<?php echo STATIC_URL; ?>js/tools.js" type="text/javascript"></script>
  <style type="text/css">
  .t_list tr td {
    text-align: left;
  }
  </style>
</head>

<body>
  <div style="z-index: 1; right: 20px; top: 30px; color: rgb(255, 255, 255); position: fixed; display: none;" id="loading"><img width="100" height="100" src="<?php echo $BASE_V;?>images/loader.gif"></div>
  <div id="main">
    <div class="main_box">
      <h2>配置下单的商城</h2>
      <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
        <tbody>
          <tr>
            <td class="td_right_f00" width="150">商城名称：</td>
            <td class="td_left" colspan="2">
              <input type="text" size="100" value="" id="mall_domain" name="mall_domain" placeholder="域名,多个请用半角,号分割">
            </td>
          </tr>
          <tr>
            <td class="td_right">商城类别：</td>
            <td class="td_left">
              <select name="mall_type" id="mall_type">
                <?php echo $mall_type_option;?>
              </select>
            </td>
          </tr>
          <tr id="mall_goods_cat_id_div">
            <td class="td_right">商城商品分类：</td>
            <td class="td_left">
              <select name="goods_cat_id" id="goods_cat_id">
                <option value="0">--请选择--</option>
                <?php if(is_array($goods_category_array)) foreach($goods_category_array AS $goods_category) { ?>
                <option value="<?php echo $goods_category->goods_cat_id;?>"><?php echo $goods_category->cat_name;?></option>
                <?php } ?>
              </select>
            </td>
          </tr>
          <tr id="mall_div" style="display:none">
            <td class="td_right">聚店商城：</td>
            <td class="td_left" id="member_mall_div">
            </td>
            <td class="td_left">
            </td>
          </tr>
          <tr>
            <td class="td_right">&nbsp;</td>
            <td class="td_left">
              <input name="button" type="button" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'" id="member_mall_submint" value="查询" />
            </td>
          </tr>
        </tbody>
      </table>
      <h2>订单产品选择</h2>
      <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
        <tbody>
          <tr>
            <td class="td_right" width="150">商品ID</td>
            <td class="td_left_text" colspan="2">
              <input type="text" size="10" value="" id="goods_id" name="goods_id" placeholder="商品goods_id,多个商品用,号分割">
            </td>
          </tr>
          <tr>
            <td class="td_right" width="150">商品分类</td>
            <td class="td_left_text" colspan="2">
              <select name="search_goodssearch_goods_cat_id_cat_id" id="search_goods_cat_id">
                <option value="0">--请选择--</option>
                <?php if(is_array($goods_category_array)) foreach($goods_category_array AS $goods_category) { ?>
                <option value="<?php echo $goods_category->goods_cat_id;?>"><?php echo $goods_category->cat_name;?></option>
                <?php } ?>
              </select>
            </td>
          </tr>
          <tr>
            <td class="td_right" width="150">商品利润</td>
            <td class="td_left_text" colspan="2">
              <input type="text" size="2" value="" id="commission_fee_min" name="commission_fee_min"> 至
              <input type="text" size="2" value="" id="commission_fee_max" name="commission_fee_max">
            </td>
          </tr>
          <tr>
            <td class="td_right" width="150">商品数量</td>
            <td class="td_left_text" colspan="2">
              <input type="text" size="10" value="10" id="pagesize" name="pagesize" placeholder="最多10条">
              <input type="checkbox" id="is_rand" name="is_rand" value="1">随机
            </td>
          </tr>
          <tr>
            <td class="td_right">&nbsp;</td>
            <td class="td_left">
              <input name="button" type="button" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'" id="goods_submint" value="查询" />
            </td>
          </tr>
          <tr id="goods_div" style="display:none">
            <td class="td_right">商品</td>
            <td class="td_left" id="goods_list_div">
            </td>
            <td class="td_left">
            </td>
          </tr>
        </tbody>
      </table>
      <h2>订单买家选择</h2>
      <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
        <tbody>
          <tr>
            <td class="td_right" width="150">手机号</td>
            <td class="td_left_text" colspan="2">
              <input type="text" size="10" value="" id="mobile_phone" name="mobile_phone" placeholder="手机号">
            </td>
          </tr>
          <tr>
            <td class="td_right" width="150">姓名</td>
            <td class="td_left_text" colspan="2">
              <input type="text" size="10" value="" id="real_name" name="real_name" placeholder="收件人">
            </td>
          </tr>
          <tr>
            <td class="td_right" width="150">地区</td>
            <td class="td_left_text" colspan="2">
              <select id="province" name="province" class="block input" tabindex="3" data-region-id="2">
              </select>
              <select id="city" name="city" class="block input" tabindex="4" data-region-id="52">
              </select>
              <select id="district" name="district" class="block input" tabindex="5" data-region-id="500">
              </select>
            </td>
          </tr>
          <tr>
            <td class="td_right" width="150">地址</td>
            <td class="td_left_text" colspan="2">
              <input type="text" size="50" value="" id="address" name="address" placeholder="详细地址">
            </td>
          </tr>
        </tbody>
      </table>
      <h2>订单快速发货</h2>
      <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
        <tbody>
          <tr id="selected_goods_div" style="display:none">
            <td class="td_right">已选商品</td>
            <td class="td_left" id="selected_goods_list_div">
            </td>
            <td class="td_left">
            </td>
          </tr>
          <tr>
            <td class="td_right" width="150">汇总金额</td>
            <td class="td_left_text" colspan="2">
              总金额：<span id="total_money">0</span> 总利润：
              <span id="total_money2" style="color: #ff0000">0</span>
            </td>
          </tr>
          <tr>
            <td class="td_right">快递公司
            </td>
            <td class="td_left_text" colspan="2">
              <select id="delivery" name="delivery" class="block input" tabindex="5" data-region-id="500">
              </select>
            </td>
          </tr>
          <tr>
            <td class="td_right" width="150">快递单号</td>
            <td class="td_left_text" colspan="2">
              <input type="text" size="15" value="no<?php echo date('Ymd'); ?>090" id="delivery_no" name="delivery_no" placeholder="快递单号">
            </td>
          </tr>
          <tr>
            <td class="td_right" width="150">下单时间</td>
            <td class="td_left_text" colspan="2">
              <input type="text" size="15" value="<?php echo date('Y-m-d H:i:s'); ?>" id="add_time" name="add_time" placeholder="下单时间">
            </td>
          </tr>
          <tr>
            <td class="td_right" width="150">发货时间</td>
            <td class="td_left_text" colspan="2">
              <input type="text" size="15" value="<?php echo date('Y-m-d H:i:s'); ?>" id="delivery_time" name="delivery_time" placeholder="发货时间">
            </td>
          </tr>
          <tr>
            <td class="td_right">&nbsp;</td>
            <td class="td_left">
              <input name="button" type="button" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'" id="btn_ok" value="生成订单" />
            </td>
          </tr>
        </tbody>
      </table>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <h2>替换快递单号</h2>
      <table width="100%" cellspacing="0" cellpadding="0" border="0" class="t_list">
        <tbody>
          <tr>
            <td class="td_right" width="150">需要替换的单号</td>
            <td class="td_left_text" colspan="2">
              <input type="text" size="15" value="no<?php echo date('Ymd'); ?>090" id="delivery_no_pre" name="delivery_no_pre" placeholder="需要替换的单号">
            </td>
          </tr>
          <tr>
            <td class="td_right" width="150">手机号</td>
            <td class="td_left_text" colspan="2">
              <input type="text" size="10" value="" id="mobile_phone2" name="mobile_phone" placeholder="手机号">
            </td>
          </tr>
          <tr>
            <td class="td_right" width="150">姓名</td>
            <td class="td_left_text" colspan="2">
              <input type="text" size="10" value="" id="real_name2" name="real_name" placeholder="收件人">
            </td>
          </tr>
          <tr>
            <td class="td_right" width="150">地区</td>
            <td class="td_left_text" colspan="2">
              <select id="province2" name="province" class="block input" tabindex="3" data-region-id="2">
              </select>
              <select id="city2" name="city" class="block input" tabindex="4" data-region-id="52">
              </select>
              <select id="district2" name="district" class="block input" tabindex="5" data-region-id="500">
              </select>
            </td>
          </tr>
          <tr>
            <td class="td_right" width="150">地址</td>
            <td class="td_left_text" colspan="2">
              <input type="text" size="50" value="" id="address2" name="address2" placeholder="详细地址">
            </td>
          </tr>
          <tr>
            <td class="td_right">快递公司
            </td>
            <td class="td_left_text" colspan="2">
              <select id="delivery2" name="delivery2" class="block input" tabindex="5" data-region-id="500">
              </select>
            </td>
          </tr>
          <tr>
            <td class="td_right" width="150">真实快递单号</td>
            <td class="td_left_text" colspan="2">
              <input type="text" size="15" value="" id="delivery_no_replace" name="delivery_no_replace" placeholder="真实快递单号">
            </td>
          </tr>
          <tr>
            <td class="td_right">&nbsp;</td>
            <td class="td_left">
              <input name="button" type="button" class="btn05" onmouseover="this.className='btn06'" onmouseout="this.className='btn05'" id="btn_replace" value="替换" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</body>

</html>
<script src="<?php echo STATIC_URL; ?>js/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo STATIC_URL; ?>js/jquery-plugin/ui/minified/jquery.cookie-min.js" type="text/javascript"></script>
<script src="<?php echo STATIC_URL; ?>js/json2.js" type="text/javascript"></script>
<script language="javascript">
var mobile_url = '<?php echo MOBILE_URL; ?>';
var php_self = '<?php echo PHP_SELF; ?>';
var global_orderf_address_pid = $.cookie('global_orderf_address_pid');
var global_orderf_address_cityid = $.cookie('global_orderf_address_cityid');
var global_orderf_address_disid = $.cookie('global_orderf_address_disid');
</script>
<script src="<?php echo $BASE_V;?>orderf.js" type="text/javascript"></script>
