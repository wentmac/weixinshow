$(function() {
  goods_category.init(); //修改时初始化

  $("#btn_addcat").click(function() { //新增项
    var item = new Object();
    item.cate_id = 0;
    item.cate_name = "";
    item.cate_sort = "0";
    goods_category.additem(item);
  });
  $("#btn_save").click(function() { //保存修改
    var that = $(this);
    //提交后必选校验
    if (!check_required_options.init($(this))) {
      return false;
    }
    var dataParam = postField;
    $.ajax({
      type: "POST",
      url: index_url + php_self + "?m=seller/category.batch_save",
      dataType: "json",
      data: dataParam,
      cache:false,
      success: function(data) {
        var modal_dom_id = "my-alert";
        if (data.success == true) {
          MODAL_HTML._alert(modal_dom_id, "操作成功", "保存成功！", "确定");
          $("#" + modal_dom_id).modal();
          check_required_options._enable_btn(that);
          $.get(index_url + php_self + '?m=seller/category.getList', function(d) {
            if (d.success == true) {
              global_category_list = d.data;
              goods_category.init();
            } else {
              MODAL_HTML._alert(modal_dom_id, "操作失败", data.message, "确定");
            }
          }, "json");
        } else {
          MODAL_HTML._alert(modal_dom_id, "操作失败", data.message, "确定");
          $("#" + modal_dom_id).modal();
          check_required_options._enable_btn(that);
          return false;
        }
      }
    });
  });
});

var check_required_options = {
  _disable_btn: function(_this) {
    if (_this.hasClass("isLoading")) {
      //return false;
    } else {
      _this.addClass('isLoading').html('<i class="am-icon-spinner am-icon-spin"></i>保存中…').attr('disabled', 'disabled');
    }
  },
  _enable_btn: function(_this) {
    if (_this.hasClass("isLoading")) {
      _this.removeClass('isLoading').html('<i class="am-icon-fw am-icon-save"></i>保存修改').removeAttr('disabled');
    }
  },
  init: function(_this) {
    var that = this;
    if (_this.hasClass("isLoading")) {
      return false;
    }
    this._disable_btn(_this);

    var modal_dom_id = 'my-alert';

    var error_message_status = false;
    var error_message_msg = "";

    var ary_category = new Object();
    ary_category.create = [];
    ary_category.modify = [];
    $("#category_list tr.cateitem").each(function(i) {
      var cateid = $(this).attr('catid');
      var catename = $(this).find("input.cate_name").val();
      var catesort = $(this).find("input.cate_sort").val();
      if (catename != "") {
        if (!$.isNumeric(catesort)) {
          error_message_status = true;
          error_message_msg += catename + "的排序项必须填写数字！<br>";
        } else {
          if (cateid > 0) {
            ary_category.modify.push({
              cat_id: cateid,
              cat_name: catename,
              sort_num: catesort
            });
          } else {
            ary_category.create.push({
              cat_name: catename,
              sort_num: catesort
            });
          }
        }
      } else {
        $(this).remove();
      }
    });
    if (error_message_status) {
      MODAL_HTML._alert(modal_dom_id, error_message_msg, "确定");
      $("#" + modal_dom_id).modal();
      this._enable_btn(_this);
      return false;
    }
    postField.param = JSON.stringify(ary_category);
    return true;
  }
}
var goods_category = {
  init: function() {
    $("#category_list tr.cateitem").remove();
    if (global_category_list.length > 0) {
      for (var i = 0; i < global_category_list.length; i++) {
        var obj = global_category_list[i];
        var item = new Object();
        item.cate_id = obj.item_cat_id;
        item.cate_name = obj.cat_name;
        item.cate_sort = obj.cat_sort;
        this.additem(item);
      }
    }
  },
  additem: function(item) {
    var tmp = '';
    tmp += '<tr class="cateitem" catid="[cate_id]">';
    tmp += '  <td class="input_td"><input type="text" class="cate_name am-form-field invisible_input" value="[cate_name]"></td>';
    tmp += '  <td class="input_td am-text-middle"><input type="text" class="cate_sort am-form-field invisible_input" value="[cate_sort]"></td>';
    tmp += '  <td class="am-text-middle am-text-center"><a href="javascript:void(0)" onclick="goods_category.delitem(this)">删除</a></td>';
    tmp += '</tr>';
    tmp = tmp.replace('[cate_id]', item.cate_id);
    tmp = tmp.replace('[cate_name]', item.cate_name);
    tmp = tmp.replace('[cate_sort]', item.cate_sort);
    $("#category_list table tbody").append(tmp);
  },
  delitem: function(obj) {
    var obj_tr = $(obj).parents("tr.cateitem").eq(0);
    var cate_id = obj_tr.attr('catid');
    //console.log(cate_id);
    if (cate_id > 0) { //删除库里已存在的
      var modal_dom_id = 'my-confirm';
      MODAL_HTML._confirm(modal_dom_id, '删除确认', '您是否要将该分类下的商品一起删除？', '只删分类', '商品也删');
      $('#' + modal_dom_id).modal({
        relatedTarget: obj_tr,
        onConfirm: function(options) {
          var that = this;
          $.get(index_url + php_self, {
              m: "seller/category.delete",
              type: "1",
              id: that.relatedTarget.attr('catid')
            },
            function(data) {
              var modal_dom_id = "my-alert";
              if (data.success == true) { //删除成功！
                if (data.goods_count > 0) {
                  MODAL_HTML._alert(modal_dom_id, "删除成功", "已经将该分类下" + data.goods_count + "个商品一起删除！<br>如果您是误操作删除，请联系在线客服恢复！", "确定");
                  $("#" + modal_dom_id).modal();
                }
                console.log(that.relatedTarget);
                that.relatedTarget.remove();
              } else {
                MODAL_HTML._alert(modal_dom_id, "删除失败", data.message, "确定");
                $("#" + modal_dom_id).modal();
              }
            });
        },
        onCancel: function() {
          var that = this;
          $.get(index_url + php_self, {
              m: "seller/category.delete",
              type: "2",
              id: that.relatedTarget.attr('catid'),
            },
            function(data) {
              var modal_dom_id = "my-alert";
              if (data.success == true) {
                console.log(that.relatedTarget);
                that.relatedTarget.remove();
              } else {
                MODAL_HTML._alert(modal_dom_id, "删除失败", data.message, "确定");
                $("#" + modal_dom_id).modal();
              }
            });
        }
      });
    } else {
      obj_tr.remove();
    }

  }
}
