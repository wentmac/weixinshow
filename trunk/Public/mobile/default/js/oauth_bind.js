var global_timer = 0;

$(function() {
	$("#user_tele").focus();
    $("#user_tele").val(global_mobile);
    login.init();
});

var login = {

    init: function() {
        var _this = this;
        _this.check_mobile();
    },
    check_mobile: function() {
        var _this = this;
        $("#login_next").click(function() {
            var tel = $("#user_tele").val();
            if (!/^1([3]|[5]|[8]|[4]|[7])[0-9]{9}$/.test(tel)) {
                alert('手机号码格式不正确！');
                return false;
            }
            var dataParam = {
                mobile: tel
            };
            $.ajax({
                type: "get",
                url: index_url + 'account/check_mobile_isreg',
                data: dataParam,
                dataType: "jsonp",
                cache:false,
                success: function(data) {
                    if (data.success == false) { //已经注册过
                        _this.set_bind(tel, 3);
                    } else { //新用户
                        _this.set_bind(tel, 1);
                    }
                }
            });
        });
    },
    _send_sms: function(mob, send_type) { //发短信验证码
        var _this = this;
        var sending = $("#resend_tel_code").attr('sending');
        if (sending == 1) {
            return;
        }
        var dataParam = {
            sms_type: send_type,
            mobile: mob,
            verify_code: $("#imgvcode").val()
        };
        $.ajax({
            type: "POST",
            url: mobile_url + 'account/send_verify_code',
            dataType: "jsonp",
            data: dataParam,
            cache:false,
            success: function(data) {
                if (data.success == true) {
                    $("#catch_code_btn").attr('sending', '1');
                    $("#vcodeimg_wraper").hide();
                    $("#catch_times").css('z-index','12');
                    $("#safe_code_input").focus();
                    global_timer = 60;
                    _this._timer_sendsms();
                } else {
                    if (data.status == -1) {
                        alert(data.message);
                    } else if (data.status == 0) {
                        alert(data.message);
                        $("#vcodeimg").attr("src", mobile_url+ "account/verifyimg?rnd=" + new Date().getTime()); //取新的图片验证码
                        $("#vcodeimg_wraper").show();
                        $("#imgvcode").focus();
                    } else if (data.status == -2) { //已经注册过
                        if(confirm('操作失败! 这个手机号已经注册过！您可以直接登录或找回密码！点击确定进行找回密码！')){
                            _this.set_bind(mob,3);
                        }
                    }

                }
            }
        });
    },
    _timer_sendsms: function() { //发短信倒记时
        if (global_timer > 0) {
            $("#honey_times").text(global_timer);
            global_timer--;
            setTimeout(login._timer_sendsms, 1000);
        } else {
            $("#catch_code_btn").removeAttr('sending');
            $("#catch_times").css('z-index','10');
        }
    },
    set_bind: function(mob, isreg) { //isreg为3为已经注册过的手机号绑定 1为新注册
        var _this = this;
        var send_type = isreg;

        $("#vcodeimg").off('click').on('click',function(){  //更换图片验证码
            $(this).attr("src", mobile_url + "account/verifyimg?rnd=" + new Date().getTime());
        });
        $("#catch_code_btn").off('click').on('click', function() { //发短信验证码方法
            _this._send_sms(mob, send_type);
        });

        $("#success").hide();
        $("#login_wrap").show(); //界面切换

        $("#catch_code_btn").trigger('click'); //自动发1条短信验证码

        $("#login_form_submit").off('click').on('click', function() { //提交
            var code = $.trim($("#safe_code_input").val());
            if (!/^\d{6}$/.test(code)) {
                alert('短信验证码应该是6位纯数字！');
                $("#code").focus();
                return false;
            }
            var dataParam = {
                mobile: mob,
                sms_captcha: code
            };
            var do_str = ""
            var url = mobile_url;
            if (isreg == 3) { //已注册过的手机绑定
                url += 'oauth/bind_account';
            } else {    //新手机号
                url += 'oauth/bind_new_account';
            }
            $.ajax({
                type: "POST",
                url: url,
                dataType: "jsonp",
                data: dataParam,
                cache:false,
                success: function(data) {
                    //console.log(data);
                    if (data.success == true) {
						if ( display == 'web' ) {
							window.opener.location.href=domain;
							window.close();
						} else {
							location.href = referer_url; //操作成功跳转到来源页
						}
                    } else {
                        alert(data.message);
                    }
                }
            });

        });
    },
};
