$(function() {
    if ($("#tele").val() == '') {
        $("#tele").val(global_mobile);
    }
    region.init(); //初始化Region三级联动

    $("#new_adadress_btn").click(function() { //提交按钮
        var nam = $("#nam").val();
        if (!nam) {
            alert('请填写收货人姓名!');
            $("#nam").focus();
            return false;
        }
        var tele = $("#tele").val();
        if (!tele) {
            alert('请填写手机号码!');
            $("#tele").focus();
            return false;
        }
        var province = $("#province").val();
        if (!(province > 0)) {
            alert('请选择省份!');
            $("#province").focus();
            return false;
        }
        var city = $("#city").val();
        if (!(city > 0)) {
            alert('请选择城市!');
            $("#city").focus();
            return false;
        }
        var district = $("#district").val();
        if (!(district > 0)) {
            alert('请选择地区!');
            $("#district").focus();
            return false;
        }
        var detail_add = $("#detail_add").val();
        if (!detail_add) {
            alert('请填写详细街道地址!');
            $("#detail_add").focus();
            return false;
        }
        var dataParam = {
            address_id: global_address_id,
            consignee: nam,
            mobile: tele,
            province: province,
            city: city,
            district: district,
            address: detail_add
        }
        $.ajax({
            url: mobile_url + 'member/address.save',
            type: 'POST',
            dataType: 'json',
            data: dataParam,
            cache:false,
            success: function(data) {
                if (data.success == true) {
                    var address_id = data.data;
                    var item_uid = global_editinfo.uid;
                    var url = mobile_url;
                    if(global_backurl){
                        url = global_backurl+"&address_id="+address_id;
                    }else{
                        url = mobile_url+'member/address';
                    }
                    location.href = url;
                } else {
                    alert(data.message);
                }
            }
        });

    });
});

//地区选择
var region = {
    init: function() {
        if (global_order_address_pid == '') global_order_address_pid = 0;
        if (global_order_address_cityid == '') global_order_address_cityid = 0;
        if (global_order_address_disid == '') global_order_address_disid = 0;
        this._pid_bind(global_order_address_pid);
        this._city_bind(global_order_address_pid, global_order_address_cityid);
        this._district_bind(global_order_address_cityid, global_order_address_disid);
        this._register(global_order_address_cityid, global_order_address_disid);
    },
    _register: function(cityid, disid) {
        var that = this;
        $("#province").on('change', function() {
            var pid = $(this).val();
            that._set_cookie('global_order_address_pid', pid);
            that._city_bind(pid, cityid);
        });
        $("#city").on('change', function() {
            var cid = $(this).val();
            that._set_cookie('global_order_address_cityid', cid);
            that._district_bind(cid, disid);
        });
        $("#district").on('change', function() {
            var did = $(this).val();
            that._set_cookie('global_order_address_disid', did);
        });
    },
    _pid_bind: function(pid) {
        $.ajax({
            url: index_url + 'manage.php' + '?m=tool.getRegion&id=1',
            type: 'GET',
            dataType: 'jsonp',
            cache:false,
            success: function(data) {
                if (!data.success) {
                    alert(data.message);
                } else {
                    var $selected = $("#province");
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
                url: index_url + 'manage.php' + '?m=tool.getRegion&id=1',
                type: 'GET',
                data: {
                    id: pid
                },
                cache:false,
                dataType: 'jsonp',
                success: function(data) {
                    if (!data.success) {
                        alert(data.message);
                    } else {
                        var $selected = $("#city");
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
            var $selected = $("#city");
            $selected.find('option').remove();
            $selected.append('<option value="0">城市</option>');
            $selected.trigger('change');
        }
    },
    _district_bind: function(cityid, disid) {
        if (cityid > 0) {
            $.ajax({
                url: index_url + 'manage.php' + '?m=tool.getRegion&id=1',
                type: 'GET',
                data: {
                    id: cityid
                },
                cache:false,
                dataType: 'jsonp',
                success: function(data) {
                    if (!data.success) {
                        alert(data.message);
                    } else {
                        var $selected = $("#district");
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
            var $selected = $("#district");
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
