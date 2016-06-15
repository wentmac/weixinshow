$(function() {
    data_builder.init(param);
    $("#search_button").click(function() {
        param.search_keyword = $('#txt_keyword').val();
        param.goods_type = $('#goods_type').val();
        data_builder.getlist(param);
    });
    if (just_this_goods_cat_id == "1") {
        $("#cbk_is_just_this_cat").attr("checked", "checked");
    }
    $("#_sel_lable").click(function() {
        $(this).html("无");
        $(this).attr("data_value", "0");
        param.goods_cat_id = 0;
    });
    $(".a_del").click(function() {
        var _this = $(this);
        if (confirm("确认要删除吗？")) {
            $.ajax({
                type: "post",
                url: php_self + "?m=goods.batch",
                data: {
                    status: "del",
                    id: _this.attr("data-item-id")
                },
                cache: false,
                success: function(data) {
                    if (data.success == true) {
                        _this.parents("tr").hide();
                    } else {
                        M._alert(data.message);
                    }
                }
            });
        }
    });
    //执行产品下架操作
    $("#goods_down").click(function() {
        param.sort = "";
        param.status = "off";
        data_builder.getlist(param);
    });
    $(".cbk_del_all").click(function() {
        if (confirm("确认要批量删除吗？")) {
            var _this = $(this);
            var ids = data_builder.getids();
            if (ids != false) {
                $.ajax({
                    type: "post",
                    url: php_self + "?m=goods.batch",
                    data: {
                        status: "del",
                        id: ids
                    },
                    cache: false,
                    success: function(data) {
                        if (data.success == true) {
                            _this.parents("tr").hide();
                        } else {
                            M._alert(data.message);
                        }
                    }
                });
            }
        }
    });
    $(".cbk_all").click(function() {
        $("table .am-icon-check-square-o").removeClass("am-hide");
        $("table .am-icon-square-o").addClass("am-hide");

    });
    $(".cbk_no_all").click(function() {
        var cbk = $("table .am-icon-check-square-o");
        for (var i = 0; i < cbk.length; i++) {
            var cbk_no = $(".am-icon-check-square-o").eq(i);
            var cbk_ed = $(".am-icon-square-o").eq(i);
            if (cbk_no.hasClass("am-hide")) {
                cbk_no.removeClass("am-hide");
                cbk_ed.addClass("am-hide");
            } else {
                cbk_no.addClass("am-hide");
                cbk_ed.removeClass("am-hide");
            }
        }
    });
    $(".am-icon-square-o").click(function() {
        $(this).addClass("am-hide");
        $(this).prev(".am-icon-check-square-o").removeClass("am-hide");
    });
    $(".am-icon-check-square-o").click(function() {
        $(this).addClass("am-hide");
        $(this).next(".am-icon-square-o").removeClass("am-hide");
    });

    $(".cbk_on_all").click(function() {
        var ids = data_builder.getids();
        if (ids != false) {
            $.ajax({
                type: "post",
                url: php_self + "?m=goods.batch",
                data: {
                    status: "on",
                    id: ids
                },
                cache: false,
                success: function(data) {
                    if (data.success == true) {
                        M._alert("上架成功");
                    } else {
                        M._alert(data.message);
                    }
                }
            });
        }
    });
    $(".cbk_off_all").click(function() {
        var ids = data_builder.getids();
        if (ids != false) {
            $.ajax({
                type: "post",
                url: php_self + "?m=goods.batch",
                data: {
                    status: "off",
                    id: ids
                },
                cache: false,
                success: function(data) {
                    if (data.success == true) {
                        M._alert("下架成功");
                    } else {
                        M._alert(data.message);
                    }
                }
            });
        }
    });
    $(".span_more").click(function() {

        if ($(this).hasClass("am-icon-level-down")) {
            $(this).parents(".td_left").find(".div_sku").css("height", "auto");
            $(this).html("收起内容");
            $(this).removeClass("am-icon-level-down");
            $(this).addClass("am-icon-level-up");
        } else {
            $(this).parents(".td_left").find(".div_sku").css("height", "160px");
            $(this).html("展开更多");
            $(this).removeClass("am-icon-level-up");
            $(this).addClass("am-icon-level-down");
            $(this).parents(".pre_sku").hide("slow");
        }
    });
    $(".a_sku").click(function() {
        $(this).next(".pre_sku").show("slow");
    });

    $(".sel").change(function() {
        data_builder.sel_change($(this), $(this).attr("level"));
    });
    $("._sel").change(function() {
        data_builder._sel_change($(this), $(this).attr("level"));
    });
    $("#sel_cat_all").click(function() {
        var sel_val = 0;
        for (var i = 4; i >= 0; i--) {
            if (!$("#sel_" + i).is(":hidden")) {
                if ($("#sel_" + i).val() != "") {
                    sel_val = $("#sel_" + i).val();
                    break;
                }
            }
        }
        if (sel_val != 0) {
            var ids = data_builder.getids();
            if (ids != false) {
                $.ajax({
                    type: "post",
                    url: php_self + "?m=goods.batch_category",
                    data: {
                        goods_cat_id: sel_val,
                        goods_id: ids
                    },
                    cache: false,
                    success: function(data) {
                        if (data.success == true) {
                            M._alert("设置分类成功");
                        } else {
                            M._alert(data.message);
                        }
                    }
                });
            }
        } else {
            M._alert("还没有选择分类");
        }
    });
    //批量调价的
    $('#set_price').click(function() {
        //弹出调价的浮动层		
        var ids = data_builder.getids();
        if (ids != false) {
            $('#my-popup').modal();
        } else {
            M._alert('先选择要调价的商品哟');
        }
    });
    $('#set_price_button').click(function() {
        var ids = data_builder.getids();
        if (ids != false) {
            var set_price_type = $("input[name='set_price_type']:checked").val();
            var set_price_class = $("input[name='set_price_class']:checked").val();
            var fixed_value = $('#fixed_value').val();
            var percent_value = $('#percent_value').val();

            if (set_price_class == 'fixed' && fixed_value == '') {
                alert('请填写需要调整的金额！');
                $('#fixed_value').focus();
                return false;
            }
            if (set_price_class == 'percent' && percent_value == '') {
                alert('请填写需要调整的百分比！');
                $('#percent_value').focus();
                return false;
            }

            if (set_price_class == 'fixed') {
                var price_value = fixed_value;
            } else if (set_price_class == 'percent') {
                var price_value = percent_value;
            } else {
                return false;
            }
            /**
            console.log(set_price_type);
            console.log(set_price_class);
            console.log(fixed_value);
            console.log(percent_value);
            */

            $.ajax({
                type: "post",
                url: php_self + "?m=goods.custom_price",
                data: {
                    id: ids,
                    price_type: set_price_type,
                    price_class: set_price_class,
                    price_value: price_value,
                },
                cache: false,
                success: function(data) {
                    $('#my-popup').modal('close');
                    if (data.success == true) {
                        M._alert('操作成功');
                    } else {
                        M._alert(data.message);
                    }
                }
            });
        }
    });
});

var sel_html = "";
var data_builder = {
    sel_change: function(_control, _level) {
        if (_control.val() != '') {
            var level = _level;
            var _this = this;
            $.ajax({
                type: "get",
                url: php_self + "?m=goods.get_goods_category_array",
                data: {
                    goods_cat_id: _control.val()
                },
                cache: false,
                success: function(data) {
                    level++;
                    if (data.success == true) {
                        var list = data.data;
                        if (list.length > 0) {
                            sel_html = "<option value=''>-请选择-</option>";
                            for (var i = 0; i < list.length; i++) {
                                sel_html += "<option value='" + list[i].goods_cat_id + "'>" + list[i].cat_name + "</option>";
                            }
                            $("#sel_" + level).html(sel_html);
                            $("#sel_" + level).show();
                            for (var j = level + 1; j < 5; j++) {
                                $("#sel_" + j).hide();
                            }
                            //							$("#sel_" + level).selected();
                        } else {
                            $("#sel_" + level).hide();
                        }
                    }
                }
            });
        }
    },
    _sel_change: function(_control, _level) {
        if (_control.val() != '') {
            var level = _level;
            var _this = this;
            param.goods_cat_id = _control.val();
            $.ajax({
                type: "get",
                url: php_self + "?m=goods.get_goods_category_array",
                data: {
                    goods_cat_id: _control.val()
                },
                cache: false,
                success: function(data) {
                    level++;
                    if (data.success == true) {
                        var list = data.data;
                        if (list.length > 0) {
                            sel_html = "<option value=''>-请选择-</option>";
                            for (var i = 0; i < list.length; i++) {
                                sel_html += "<option value='" + list[i].goods_cat_id + "'>" + list[i].cat_name + "</option>";
                            }
                            $("#_sel_" + level).html(sel_html);
                            $("#_sel_" + level).show();
                            for (var j = level + 1; j < 5; j++) {
                                $("#_sel_" + j).hide();
                            }
                        } else {
                            $("#_sel_" + level).hide();
                        }
                    }
                }
            });
        }
    },
    init: function(param) {

        $("#_sel_lable").html(param.goods_cat_name);
        $("#_sel_lable").attr("data_value", param.goods_cat_id);

        //执行按分类查询
        $("#btn_cate_sel").click(function() {
            data_builder.getlist(param);
        });
        $("#sel_cate_param").change(function() {
            if ($("#sel_cate_param").val() != null) {
                param.item_cat_id = $("#sel_cate_param").val();
            } else {
                param.item_cat_id = "";
            }
        });
        //判断显示批量 上架 或者是 下架
        if (param.status == "off") {
            $(".cbk_on_all").show();
            $(".cbk_off_all").hide();
        } else {
            $(".cbk_on_all").hide();
            $(".cbk_off_all").show();
        }
        //排序默认加载样式
        if (sort != 1) {
            $("#sort_addtime").removeClass("am-btn-primary");
            $("#sort_inventory").addClass("am-btn-default");
        }
        if (sort == 3 || sort == 2) {
            if (sort == 3) {
                $("#sort_sales_count i").removeClass("am-icon-long-arrow-down");
                $("#sort_sales_count i").addClass("am-icon-long-arrow-up");
            }
            $("#sort_sales_count").removeClass("am-btn-default");
            $("#sort_sales_count").addClass("am-btn-primary");
        }
        if (sort == 5 || sort == 4) {
            if (sort == 5) {
                $("#sort_inventory i").removeClass("am-icon-long-arrow-down");
                $("#sort_inventory i").addClass("am-icon-long-arrow-up");
            }
            $("#sort_inventory").removeClass("am-btn-default");
            $("#sort_inventory").addClass("am-btn-primary");

        }
        if (sort == 7 || sort == 6) {
            if (sort == 7) {
                $("#sort_price i").removeClass("am-icon-long-arrow-down");
                $("#sort_price i").addClass("am-icon-long-arrow-up");
            }
            $("#sort_price").removeClass("am-btn-default");
            $("#sort_price").addClass("am-btn-primary");
        }

        //查询框  搜索关键词 赋值
        if (query_string != "") {
            $("#txt_keyword").val(query_string);
        }

        //执行排序操作
        $("#btns_sort button").click(function() {
            var value = 0;
            if ($(this).attr("data_id") == "sort_addtime") {
                value = 1;
            }
            if ($(this).attr("data_id") == "sort_sales_count") {
                if (!$(this).find("i").hasClass("am-icon-long-arrow-down")) {
                    value = 2;
                } else {
                    value = 3;
                }
            }
            if ($(this).attr("data_id") == "sort_inventory") {
                if (!$(this).find("i").hasClass("am-icon-long-arrow-down")) {
                    value = 4;
                } else {
                    value = 5;
                }
            }
            if ($(this).attr("data_id") == "sort_price") {
                if (!$(this).find("i").hasClass("am-icon-long-arrow-down")) {
                    value = 6;
                } else {
                    value = 7;
                }
            }
            param.sort = value;
            data_builder.getlist(param);
        });

        /*获取一级分类列表
         */
        $.ajax({
            type: "get",
            url: php_self + "?m=goods.get_goods_category_array",
            data: {
                goods_cat_id: "0"
            },
            cache: false,
            success: function(data) {
                if (data.success == true) {
                    var list = data.data;
                    var sel_html = "";
                    sel_html = "<option value=''>-请选择-</option>";
                    for (var i = 0; i < list.length; i++) {
                        sel_html += "<option value='" + list[i].goods_cat_id + "'>" + list[i].cat_name + "</option>";
                    }
                    $("#sel_0").html(sel_html);
                    $("#_sel_0").html(sel_html);
                    $("#_sel_0").show();
                    $("#sel_0").show();
                    //					$("#sel_0").selected();

                }
            }
        });

        //get_goods_category_array()
    },

    //获取所选中的checkbox中的 item_ID
    getids: function() {
        var cbk = $("table .am-icon-check-square-o");
        var ids = "";
        for (var i = 0; i < cbk.length; i++) {
            var cbk_no = $(".am-icon-check-square-o").eq(i);
            var cbk_ed = $(".am-icon-square-o").eq(i);
            if (cbk_ed.hasClass("am-hide")) {
                ids += cbk_no.attr("data-item-id") + ",";
            }
        }
        if (ids == "") {
            M._alert("还没有选择产品");
            return false;
        }
        return ids.substr(0, ids.length - 1);
    },
    //type 为参数类型，value 是值    然后重写url
    getlist: function(param) {
        if ($("#cbk_is_just_this_cat").is(":checked")) {
            param.just_this_goods_cat_id = 1;
        } else {
            param.just_this_goods_cat_id = 2;
        }
        var str = "";
        if (param.sort != "") {
            str += "&sort=" + param.sort;
        }
        if (param.search_keyword != "") {
            str += "&search_keyword=" + param.search_keyword;
        }
        if (param.sort == "") {
            if (param.status != "") {
                str += "&status=" + param.status;
            }
        }
        if (param.goods_cat_id != "") {
            str += "&goods_cat_id=" + param.goods_cat_id;
        }
        if (param.just_this_goods_cat_id != "") {
            str += "&just_this_goods_cat_id=" + param.just_this_goods_cat_id;
        }
        if (param.goods_type != "0") {
            str += "&goods_type=" + param.goods_type;
        }


        location.href = php_self + "?m=goods.index" + str;
    }
}
