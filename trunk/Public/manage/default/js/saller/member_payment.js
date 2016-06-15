var modal_dom_id = "my-alert";

//页面加载完执行
$(function() {
    if (global_bank_id) {
        $("#bank_id").val(global_bank_id);
        $("#bank_id").trigger('changed.selected.amui');
    }

    pro_city.init();


    //提交按钮
    $("#btn_submit").click(function() {
        var that = $(this);
        //提交后必选校验
        if (!check_required_options.init($(this))) {
            return false;
        }
        var dataParam = postField;
        $.ajax({
            type: "POST",
            url: index_url + php_self + '?m=seller/member.payment_save',
            dataType: "json",
            data: dataParam,
            cache:false,
            success: function(data) {
                //console.log(data);
                if (data.success == true) {
                    MODAL_HTML._alert(modal_dom_id, "操作成功", "更新成功！", "确定");
                    $("#" + modal_dom_id).modal();
                    check_required_options._enable_btn(that);
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

//地区选择
var pro_city = {
    init: function() {
        this._pid_bind(global_bank_pid);
        this._city_bind(global_bank_pid, global_bank_cityid);
        this._register(global_bank_cityid);
    },
    _register: function(cityid) {
        var that = this;
        $("#bank_proid").on('change', function() {
            var pid = $(this).val();
            that._city_bind(pid, cityid);
        });
    },
    _pid_bind: function(pid) {
        $.ajax({
            url: index_url + php_self + '?m=tool.getRegion&id=1',
            type: 'GET',
            dataType: 'jsonp',
            cache:false,
            success: function(data) {
                if (!data.success) {
                    MODAL_HTML._alert(modal_dom_id, "错误提醒", data.message, "确定");
                    $("#" + modal_dom_id).modal();
                } else {
                    var $selected = $("#bank_proid");
                    $selected.find('option').remove();
                    $selected.append('<option value="0">省份</option>');
                    var list = data.data;
                    for (var i = 0; i < list.length; i++) {
                        var seled = "";
                        var region_id = list[i].region_id;
                        if (pid == region_id) seled = " selected";
                        $selected.append('<option value=' + list[i].region_id + seled + '>' + list[i].region_name + '</option>');
                    }
                    $selected.trigger('changed.selected.amui');
                }
            }
        });
    },
    _city_bind: function(pid, cityid) {
        if (pid > 0) {
            $.ajax({
                url: index_url + php_self + '?m=tool.getRegion&id=1',
                type: 'GET',
                data: {
                    id: pid
                },
                dataType: 'jsonp',
                cache:false,
                success: function(data) {
                    if (!data.success) {
                        MODAL_HTML._alert(modal_dom_id, "错误提醒", data.message, "确定");
                        $("#" + modal_dom_id).modal();
                    } else {
                        var $selected = $("#bank_cityid");
                        $selected.find('option').remove();
                        $selected.append('<option value="0">地区</option>');
                        var list = data.data;
                        for (var i = 0; i < list.length; i++) {
                            var seled = "";
                            var region_id = list[i].region_id;
                            if (cityid == region_id) seled = " selected";
                            $selected.append('<option value=' + list[i].region_id + seled + '>' + list[i].region_name + '</option>');
                        }
                        $selected.trigger('changed.selected.amui');
                    }

                }
            });
        } else {
            var $selected = $("#bank_cityid");
            $selected.find('option').remove();
            $selected.append('<option value="0">地区</option>');
        }
    }
}

//表单提交验证
var check_required_options = {
    _disable_btn: function(_this) {
        if (_this.hasClass("isLoading")) {
            //return false;
        } else {
            _this.addClass('isLoading').html('<i class="am-icon-spinner am-icon-spin"></i> 提交中').attr('disabled', 'disabled');
        }
    },
    _enable_btn: function(_this) {
        if (_this.hasClass("isLoading")) {
            _this.removeClass('isLoading').html('<i class="am-icon-check-circle"></i> 提　交').removeAttr('disabled');
        }
    },
    init: function(_this) {
        if (_this.hasClass("isLoading")) {
            return false;
        }
        this._disable_btn(_this);
        //bank_id
        var bank_id = $("#bank_id").val();
        console.log(bank_id);
        if (!bank_id) {
            MODAL_HTML._alert(modal_dom_id, "错误提醒", "请选择银行名称！", "确定");
            $("#" + modal_dom_id).modal();
            $('#bank_id').focus();
            this._enable_btn(_this);
            return false;
        }
        var bank_pid = $("#bank_proid").val();
        var bank_cityid = $("#bank_cityid").val();
        //银行卡号
        var bank_cardnum = $("#bank_cardnum").val();
        if (!bank_cardnum) {
            MODAL_HTML._alert(modal_dom_id, "错误提醒", "请填写银行卡号！", "确定");
            $("#" + modal_dom_id).modal();
            $('#bank_cardnum').focus();
            this._enable_btn(_this);
            return false;
        }
        //开户姓名
        var bank_account = $("#bank_account").val();
        if (!bank_account) {
            MODAL_HTML._alert(modal_dom_id, "错误提醒", "请填写开户姓名！", "确定");
            $("#" + modal_dom_id).modal();
            $('#bank_account').focus();
            this._enable_btn(_this);
            return false;
        }
        postField.bank_id = bank_id;
        postField.bank_pid = bank_pid;
        postField.bank_cityid = bank_cityid;
        postField.bank_cardnum = bank_cardnum;
        postField.bank_account = bank_account;

        return true;
    }
};
