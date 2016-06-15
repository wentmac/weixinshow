$(document).ready(function() {
    $('#mall_type').change(function() {
        var mall_type = $(this).val();
        if (mall_type == 1 || mall_type == 2) {
            $('#mall_goods_cat_id_div').show();
        } else {
            $('#mall_goods_cat_id_div').hide();
        }
    });


    $('#member_mall_submint').click(function() {
        var mall_domain = $('#mall_domain').val();
        var mall_type = $('#mall_type').val();
        var goods_cat_id = $('#goods_cat_id').val();

        var paramField = {
            'mall_domain': mall_domain,
            'mall_type': mall_type,
            'goods_cat_id': goods_cat_id
        };

        $("#loading").show();
        $.ajax({
            type: "post",
            url: php_self + "?m=orderf.get_member_mall_array",
            data: paramField,
            cache: false,
            success: function(data) {
                if (data.success == true && data.data.length > 0) {
                    var mall_string = '';
                    $(data.data).each(function(i, n) {
                        mall_string += '<input type="checkbox" name="mall_uid[]" value="' + n.uid + '" id="mall_uid_' + n.uid + '"><label for="mall_uid_' + n.uid + '">' + n.mall_name + '</label>';
                    });
                    mall_string += '<input type="checkbox" id="CheckAll" name="CheckAll" value="all"><label for="CheckAll" style="color: Blue; cursor: pointer;" id="CheckStatus">全选</label>';
                    $('#mall_div').show();
                    $('#member_mall_div').html(mall_string);

                    $('#CheckAll').bind('click', function() {
                        var checked = $(this).attr('checked');
                        if (checked == 'checked') {
                            $("input[name='mall_uid[]']").each(function() { //遍历所有的name为selectFlag的 checkbox
                                $(this).attr("checked", true);
                            })
                        } else { //反之 取消全选
                            $("input[name='mall_uid[]']").each(function() { //遍历所有的name为selectFlag的 checkbox
                                $(this).attr("checked", false);
                                //alert("f");
                            })
                        }
                    });
                } else {
                    alert(data.message);
                }
                $("#loading").hide();
            }
        });
    });

    $("#goods_submint").click(function() {
        var goods_id = $("#goods_id").val();
        var goods_cat_id = $("#search_goods_cat_id").val();
        var commission_fee_min = $("#commission_fee_min").val();
        var commission_fee_max = $("#commission_fee_max").val();
        var pagesize = $("#pagesize").val();
        var is_rand = $("#is_rand").val();
        if (is_rand != '1') {
            is_rand = 0;
        }

        var paramField = {
            'goods_id': goods_id,
            'search_goods_cat_id': goods_cat_id,
            'commission_fee_min': commission_fee_min,
            'commission_fee_max': commission_fee_max,
            'pagesize': pagesize,
            'rand': is_rand
        }

        $("#loading").show();
        $.ajax({
            url: php_self + "?m=orderf.get_goods_array",
            type: 'POST',
            dataType: 'json',
            data: paramField,
            cache: false,
            success: function(data) {
                if (!data.success) {
                    alert(data.message);
                } else {
                    $("#goods_div,#selected_goods_div").show();
                    var $tbody = $("<tbody></tbody>");
                    $(data.data.rs).each(function(i, goods_item) {
                        var $tr = "";
                        if (data.data.goods_sku_array.hasOwnProperty(goods_item.goods_id)) {
                            var $tr = "<tr><td>" + goods_item.goods_id + "</td><td><a target='_blank' href='http://mall.090.cn/goods/" + goods_item.goods_id + ".html'>" + goods_item.goods_name + "</a></td><td>-</td><td>-</td><td>-</td></tr>";
                            $(data.data.goods_sku_array[goods_item.goods_id]).each(function(i, sku_item) {
                                $tr += "<tr><td></td><td><a target='_blank' href='http://mall.090.cn/goods/" + sku_item.goods_id + ".html'>" + sku_item.sku_name + "</a></td><td>" + sku_item.price + "</td><td>" + sku_item.commission_fee + "</td><td><a proid='" + sku_item.goods_id + "' itemid='" + goods_item.item_id + "' skuid='" + sku_item.goods_sku_id + "' price='" + sku_item.price + "' fee='" + sku_item.commission_fee + "' class='selectit' href='#'>选择</a></td></tr>";
                            });
                        } else {
                            $tr = $("<tr><td>" + goods_item.goods_id + "</td><td><a target='_blank' href='http://mall.090.cn/goods/" + goods_item.goods_id + ".html'>" + goods_item.goods_name + "</a></td><td>" + goods_item.goods_price + "</td><td>" + goods_item.commission_fee + "</td><td><a proid='" + goods_item.goods_id + "' itemid='" + goods_item.item_id + "' skuid='0' price='" + goods_item.goods_price + "' fee='" + goods_item.commission_fee + "' class='selectit' href='#'>选择</a></td></tr>");
                        }
                        $tbody.append($tr);
                    });
                    var $table = $("<table><thead><th>ID</th><th>商品</th><th>价格</th><th>利润</th><th>选择</th></thead></table>").append($tbody);
                    $("#goods_list_div").empty().append($table);
                    $("a.selectit").bind('click', function() {
                        var proid = $(this).attr("proid");
                        var price = $(this).attr("price");
                        var fee = $(this).attr("fee");
                        var skuid = $(this).attr("skuid");
                        var itemid = $(this).attr("itemid");
                        if ($("#selected_goods_list_div a[proid='" + proid + "'][skuid='" + skuid + "']").length == 0) {
                            var $a = $("<a href='#' proid='" + proid + "' itemid='" + itemid + "' skuid='" + skuid + "' price='" + price + "' fee='" + fee + "' style='display:block;padding:5px;border:1px #ccc solid; float:left;margin-right:5px;'>" + proid + ":" + skuid + "</a>");
                            $("#selected_goods_list_div").append($a);
                            count_money();
                            $("#selected_goods_list_div a").bind('click', function() {
                                $(this).remove();
                                count_money();
                                return false;
                            });
                        }
                        return false;
                    });
                }
                $("#loading").hide();
            }
        });


    });

    function count_money() {
        var total_money = 0.0;
        var total_money2 = 0.0;
        $("#selected_goods_list_div a").each(function(i) {
            total_money += parseFloat($(this).attr("price"));
            total_money2 += parseFloat($(this).attr("fee"));
        });
        $("#total_money").text(total_money);
        $("#total_money2").text(total_money2);
    }


    region.init(); //地区
    delivery.init(); //快递
    
    //生成订单
    $("#btn_ok").click(function() {
        var mall_uid = "";

        $('input[name="mall_uid[]"]:checked').each(function(i) {
            mall_uid += $(this).val();
            if (i < $('input[name="mall_uid[]"]:checked').length - 1) {
                mall_uid += ",";
            }
            console.log(mall_uid);
        });
        if (mall_uid == '') {
            alert("请先选择商城！");
            return;
        }

        if ($("#selected_goods_list_div a").length == 0) {
            alert("请选产品！！！");
            return;
        }
        //goods_id_json=[{"item_id":358995,"sku_id":0},{"item_id":7211,"sku_id":522526}]
        var goods_id_array = [];
        $("#selected_goods_list_div a").each(function(i) {
            goods_id_array.push({
                "item_id": $(this).attr("itemid"),
                "sku_id": $(this).attr("skuid")
            })
        });

        var mobile = $("#mobile_phone").val();
        if (mobile == '') {
            alert("手机号码没填！");
            return;
        }

        var consignee = $("#real_name").val();
        if (consignee == '') {
            alert("收件人没填！");
            return;
        }

        var province = $("#province").val();
        var city = $("#city").val();
        var district = $("#district").val();
        if (province == "0") {
            alert("省份要选呀！");
            return;
        }
        if (city == "0") {
            alert("市要选呀！！");
            return;
        }
        if (district == "0") {
            alert("地区要选呀！！！");
            return;
        }

        var address = $("#address").val();
        if (address == '') {
            alert("详细地址要填的呀！！！");
            return;
        }

        var express_id = $("#delivery").val();
        if (express_id == "0") {
            alert("快递公司要选呀！！！");
            return;
        }

        var express_no = $("#delivery_no").val();
        if (express_no == '') {
            alert("快递单号要填的呀！！！");
            return;
        }

        var order_time = $("#add_time").val();
        var shipping_time = $("#delivery_time").val();

        var paramField = {
            'mall_uid': mall_uid,
            'goods_id_json': JSON.stringify(goods_id_array),
            'mobile': mobile,
            'consignee': consignee,
            'province': province,
            'city': city,
            'district': district,
            'address': address,
            'express_id': express_id,
            'express_no': express_no,
            'order_time': order_time,
            'shipping_time': shipping_time
        }
        $("#loading").show();
        $(this).attr("disabled",true);
        $.ajax({
            url: php_self + "?m=orderf.save",
            type: 'POST',
            dataType: 'json',
            data: paramField,
            cache: false,
            success: function(data) {
                if (data.success) {
                    alert('生成成功！');
                } else {
                    alert(data.message);
                }
                $("#loading").hide();
                $("#btn_ok").removeAttr("disabled");

            }
        });
    });

    //替换单号
    $("#btn_replace").click(function(){
        var delivery_no_pre = $("#delivery_no_pre").val();
        var mobile = $("#mobile_phone2").val();
        var real_name = $("#real_name2").val();
        var province = $("#province2").val();
        var city = $("#city2").val();
        var district = $("#district2").val();
        var address = $("#address2").val();
        var delivery = $("#delivery2").val();
        var delivery_no_replace = $("#delivery_no_replace").val();

        if (delivery_no_pre == '') {
            alert("需要替换的单号是多少？");
            return;
        }
        if (delivery == '') {
            alert("新的快递公司不能为空!");
            return;
        }
        if (delivery_no_replace == '') {
            alert("新的快递单号不能为空!");
            return;
        }
        var paramField = {
            'mobile': mobile,
            'consignee': real_name,
            'province': province,
            'city': city,
            'district': district,
            'address': address,
            'express_id': delivery,
            'express_no': delivery_no_replace,
            'old_express_no': delivery_no_pre
        }
        $(this).attr("disabled",true);
        $("#loading").show();
        $.ajax({
            url: php_self + "?m=orderf.modify_express",
            type: 'POST',
            dataType: 'json',
            data: paramField,
            cache: false,
            success: function(data) {
                if (data.success) {
                    alert('替换成功！');
                } else {
                    alert(data.message);
                }
                $("#loading").hide();
                $("#btn_replace").removeAttr("disabled");
            }
        });
    });
});


//快递
var delivery = {
        init: function() {
            $.ajax({
                url: php_self + "?m=orderf.get_express",
                type: 'GET',
                cache: false,
                success: function(data) {
                    if (!data.success) {
                        alert(data.message);
                    } else {
                        var $selected = $("#delivery,#delivery2");
                        $selected.find('option').remove();
                        $selected.append('<option value="0">=请选择快递公司=</option>');
                        var list = data.data;
                        for (var i = 0; i < list.length; i++) {
                            var seled = "";
                            var region_id = list[i].express_id;
                            $selected.append('<option value=' + list[i].express_id + seled + '>' + list[i].express_name + '</option>');
                        }
                        $selected.trigger('change');
                    }
                }
            });
        }
    }
    //地区选择
var region = {
    init: function() {
        if (global_orderf_address_pid == '') global_orderf_address_pid = 0;
        if (global_orderf_address_cityid == '') global_orderf_address_cityid = 0;
        if (global_orderf_address_disid == '') global_orderf_address_disid = 0;
        this._pid_bind(global_orderf_address_pid);
        this._city_bind(global_orderf_address_pid, global_orderf_address_cityid);
        this._district_bind(global_orderf_address_cityid, global_orderf_address_disid);
        this._register(global_orderf_address_cityid, global_orderf_address_disid);
    },
    _register: function(cityid, disid) {
        var that = this;
        $("#province,#province2").on('change', function() {
            var pid = $(this).val();
            that._set_cookie('global_orderf_address_pid', pid);
            that._city_bind(pid, cityid);
        });
        $("#city,#city2").on('change', function() {
            var cid = $(this).val();
            that._set_cookie('global_orderf_address_cityid', cid);
            that._district_bind(cid, disid);
        });
        $("#district,#district2").on('change', function() {
            var did = $(this).val();
            that._set_cookie('global_orderf_address_disid', did);
        });
    },
    _pid_bind: function(pid) {
        $.ajax({
            url: php_self + '?m=tool.getRegion&id=1',
            type: 'GET',
            cache: false,
            success: function(data) {
                if (!data.success) {
                    alert(data.message);
                } else {
                    var $selected = $("#province,#province2");
                    $selected.find('option').remove();
                    $selected.append('<option value="0">省份</option>');
                    var list = data.data;
                    for (var i = 0; i < list.length; i++) {
                        var seled = "";
                        var region_id = list[i].region_id;
                        if (pid == region_id) seled = " selected";
                        $selected.append('<option value=' + list[i].region_id + seled + '>' + list[i].region_name + '</option>');
                    }
                    $selected.trigger('change');
                }
            }
        });
    },
    _city_bind: function(pid, cityid) {
        if (pid > 0) {
            $.ajax({
                url: php_self + '?m=tool.getRegion&id=1',
                type: 'GET',
                data: {
                    id: pid
                },
                cache: false,
                success: function(data) {
                    if (!data.success) {
                        alert(data.message);
                    } else {
                        var $selected = $("#city,#city2");
                        $selected.find('option').remove();
                        $selected.append('<option value="0">城市</option>');
                        var list = data.data;
                        for (var i = 0; i < list.length; i++) {
                            var seled = "";
                            var region_id = list[i].region_id;
                            if (cityid == region_id) seled = " selected";
                            $selected.append('<option value=' + list[i].region_id + seled + '>' + list[i].region_name + '</option>');
                        }
                        $selected.trigger('change');
                    }

                }
            });
        } else {
            var $selected = $("#city,#city2");
            $selected.find('option').remove();
            $selected.append('<option value="0">城市</option>');
            $selected.trigger('change');
        }
    },
    _district_bind: function(cityid, disid) {
        if (cityid > 0) {
            $.ajax({
                url: php_self + '?m=tool.getRegion&id=1',
                type: 'GET',
                data: {
                    id: cityid
                },
                cache: false,
                success: function(data) {
                    if (!data.success) {
                        alert(data.message);
                    } else {
                        var $selected = $("#district,#district2");
                        $selected.find('option').remove();
                        $selected.append('<option value="0">地区</option>');
                        var list = data.data;
                        for (var i = 0; i < list.length; i++) {
                            var seled = "";
                            var region_id = list[i].region_id;
                            if (disid == region_id) seled = " selected";
                            $selected.append('<option value=' + list[i].region_id + seled + '>' + list[i].region_name + '</option>');
                        }
                        $selected.trigger('change');
                    }

                }
            });
        } else {
            var $selected = $("#district,#district2");
            $selected.find('option').remove();
            $selected.append('<option value="0">地区</option>');
            $selected.trigger('change');
        }
    },
    _set_cookie: function(key, value) {
        var date = new Date();
        var minutes = 30;
        date.setTime(date.getTime() + (minutes * 60 * 1000));
        $.cookie(key, value, {
            expires: date,
            path: '/'
        });
    }
}
