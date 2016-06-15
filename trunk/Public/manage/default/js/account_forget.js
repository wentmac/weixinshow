var modal_dom_id = "my-alert";
var model_confrm_dom_id = "my-confrm";



$(function() {
    //表单验证
    $('#form_forget').validator({
        onValid: function(validity) {
            $(validity.field).removeClass('has_err').addClass('validate_success').closest('.am-form-group').find('.am-alert').hide();
        },

        onInValid: function(validity) {
            var $field = $(validity.field);
            var $group = $field.closest('.am-form-group');
            var $alert = $group.find('.am-alert');
            // 使用自定义的提示信息 或 插件内置的提示信息
            var msg = $field.data('validationMessage') || this.getValidationMessage(validity);

            if (!$alert.length) {
                $alert = $('<div class="am-alert am-alert-danger"></div>').hide().appendTo($group);
            }

            $alert.html(msg).show();

            $field.removeClass('validate_success').addClass('has_err');
            //return false;
        },
        submit: function(e) {
            if (this.isFormValid() === false) return false;
            //return false;
            var that = $("#btn_submit");

            if (that.hasClass("isLoading")) {
                return false;
            }
            form_tools._disable_btn(that);


            postField.mobile = $('#mobilephone').val();
            postField.pwd = $('#account_pwd').val();
            postField.sms_captcha = $('#msg_code').val();
            postField.sms_type = 2;

            var dataParam = postField;

            if (global_step == 1) { //第一步先验短信码

                $.ajax({
                    type: "POST",
                    url: index_url + php_self + '?m=account.check_sms_code',
                    dataType: "jsonp",
                    data: dataParam,
                    success: function(data) {
                        //console.log(data);
                        if (data.success == true) {
                            global_step = 2;
                            set_step(2); //切换到第二步;
                            form_tools._enable_btn(that);
                        } else {
                            MODAL_HTML._alert(modal_dom_id, "操作失败", data.message, "确定");
                            $("#" + modal_dom_id).modal();
                            form_tools._enable_btn(that);
                            return false;
                        }
                    }
                });
            } else if (global_step == 2) { //第二步设置密码(这里还是会验短信码的，大黑阔请绕道:)
                $.ajax({
                    type: "POST",
                    url: index_url + php_self + '?m=account.password',
                    dataType: "jsonp",
                    data: dataParam,
                    success: function(data) {
                        //console.log(data);
                        if (data.success == true) {
                            MODAL_HTML._alert(modal_dom_id, "操作成功", "您已注册成功！", "确定");
                            $("#" + modal_dom_id).modal();
                            form_tools._enable_btn(that);
                            location.href = index_url + php_self + "?m=account.login";
                        } else {
                            MODAL_HTML._alert(modal_dom_id, "操作失败", data.message, "确定");
                            $("#" + modal_dom_id).modal();
                            form_tools._enable_btn(that);
                            return false;
                        }
                    }
                });
            }
            return false;
        }

    });
    //短信验证码
    $("#btn_sendsms").click(function() {
        if (!$("#btn_sendsms").hasClass('isLoading')) {
            var reg = /^1([3]|[5]|[8]|[4]|[7])[0-9]{9}$/;
            if (!reg.test($("#mobilephone").val())) { //手机号是否验证通过
                MODAL_HTML._alert(modal_dom_id, "操作失败", "请输入正确的11位手机号码", "确定");
                $("#" + modal_dom_id).modal();
                return false;
            }
            var dataParam = {
                sms_type: 2,
                mobile: $("#mobilephone").val(),
                verify_code: $("#img_vcode").val()
            };
            $.ajax({
                type: "POST",
                url: index_url + php_self + '?m=account.send_verify_code',
                dataType: "jsonp",
                data: dataParam,
                success: function(data) {
                    if (data.success == true) {
                        $("#btn_sendsms").attr('disabled', 'disabled').addClass('isLoading');
                        global_sendsms++;
                        global_sendsms_time = 30 * global_sendsms;
                        send_wait();
                        $("#msg_code").focus();
                    } else {
                        if (data.status == -1) {
                            MODAL_HTML._alert(modal_dom_id, "操作失败", data.message, "确定");
                            $("#" + modal_dom_id).modal();
                        } else if (data.status == 0) {
                            $("#vcodeimg").attr("src", index_url + php_self + "?m=account.verifyimg&rnd=" + new Date().getTime()); //取新的图片验证码
                            $('#my-prompt').modal({
                                closeViaDimmer: false,
                                relatedTarget: this,
                                onConfirm: function(e) {
                                    var vcode = e.data;
                                    $("#img_vcode").val(vcode);
                                    setTimeout(function() {
                                        $("#btn_sendsms").click();
                                    }, 350);
                                },
                                onCancel: function(e) {
                                    //alert('不想说!');
                                }
                            });
                        }
                    }
                }
            });
        }
    });

});


function send_wait() {
    global_sendsms_time--;
    if (global_sendsms_time > 0) {
        $("#btn_sendsms").text(global_sendsms_time + '秒后重新发送');
        setTimeout(send_wait, 1000);
    } else {
        $("#btn_sendsms").text('重发短信验证码').removeAttr('disabled').removeClass('isLoading');
    }
}

function set_step(step) {
    if (step == 2) {
        $(".step2").show(300);
        $(".step1").hide(300);
    } else if (step == 1) {
        $(".step1").show(300);
        $(".step2").hide(300);
    }
}

var form_tools = {
    _disable_btn: function(_this) {
        if (_this.hasClass("isLoading")) {
            //return false;
        } else {
            _this.addClass('isLoading').html('<i class="am-icon-spinner am-icon-spin am-icon-fw"></i> 提交中').attr('disabled', 'disabled');
        }
    },
    _enable_btn: function(_this) {
        if (_this.hasClass("isLoading")) {
            _this.removeClass('isLoading').html('<i class="am-icon-user am-icon-fw"></i> 下 一 步').removeAttr('disabled');
        }
    }
};
