var global_timer = 0;

$(function() {
    $("#tel").val(global_mobile);
    login.init();
    if(is_weixin()){
        $("#weixinLogin").show();
    }
});

var login = {

    init: function() {
        var _this = this;
        _this.check_mobile();
    },
    check_mobile: function() {
        var _this = this;
        $("#submit_user_tel").click(function() {
            var tel = $("#tel").val();
            if (!/^1([3]|[5]|[8]|[4]|[7])[0-9]{9}$/.test(tel)) {
                alert('手机号码格式不正确！');
                return false;
            }
            var dataParam = {
                mobile: tel
            };
            $.ajax({
                type: "get",
                url: mobile_url + 'account/check_mobile_isreg',
                data: dataParam,
                dataType: "jsonp",
                cache:false,
                success: function(data) {
                    if (data.success == false) { //已经注册过
                        _this.set_login(tel);
                    } else { //新用户
                        _this.set_password(tel, 1);
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
                    $("#resend_tel_code").attr('sending', '1');
                    $("#vcodeimg_wraper").hide();
                    $("#tel_second").show();
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
                            _this.set_password(mob,0);
                        }
                    }

                }
            }
        });
    },
    _timer_sendsms: function() { //发短信倒记时
        if (global_timer > 0) {
            $("#tel_second").text(global_timer);
            global_timer--;
            setTimeout(login._timer_sendsms, 1000);
        } else {
            $("#resend_tel_code").removeAttr('sending');
            $("#tel_second").hide();
        }
    },
    set_password: function(mob, isreg) { //isreg为0为重置密码 1为新注册
        var _this = this;
        var tmp = '<p class="mycart_tle">已发送验证码到你的手机  <a id="speak_code" class="right hide">收不到?</a></p>' +
            '<div class="mycart_user_content">' +
            '<p>手机号码&nbsp;&nbsp;&nbsp;&nbsp;{mobile} <span id="resend_tel_code" class="right">重新发送 <em id="tel_second" style="display: none;">1</em></span>'+
            '<span id="vcodeimg_wraper" class="right hide"><img id="vcodeimg" width="80" height="30"> '+
            '<input type="tel" maxlength="6" id="imgvcode" name="imgvcode" placeholder="图片验证码" style="width:80px"></span> </p>' +
            '<p class="mycart_input_p rel">' +
            '<label for="code">验证码</label>' +
            '<input type="tel" maxlength="6" id="code" name="code" placeholder="填写验证码"><input type="hidden" value="{isreg}"> </p>' +
            '<p class="mycart_input_p rel">' +
            '<label for="code_pwd">设置密码</label>' +
            '<input type="password" id="code_pwd" name="code_pwd" placeholder="下次可用手机号+密码登录"> </p><a id="submit_tel_code" class="btnok">确认</a></div>';
        tmp = tmp.replace('{mobile}', mob);
        tmp = tmp.replace('{isreg}', isreg);
        $("#mycart_user").html(tmp);
        var send_type = isreg == 0 ? 2 : 1;
        var page_tit = isreg == 0 ? '重置密码' : '用户登录';
        $("#page_tit").text(page_tit);

        $("#vcodeimg").off('click').on('click',function(){  //更换图片验证码
            $(this).attr("src", mobile_url + "account/verifyimg?rnd=" + new Date().getTime());
        });
        $("#resend_tel_code").off('click').on('click', function() { //发短信验证码方法
            _this._send_sms(mob, send_type);
        });




        $("#resend_tel_code").trigger('click'); //自动发1条短信验证码

        $("#submit_tel_code").off('click').on('click', function() { //提交
            var code = $.trim($("#code").val());
            if (!/^\d{6}$/.test(code)) {
                alert('短信验证码应该是6位纯数字！');
                $("#code").focus();
                return false;
            }
            var pwd = $.trim($("#code_pwd").val());
            if (pwd.length < 6) {
                alert('密码至少6位以上！');
                $("#code_pwd").focus();
                return false;
            }
            var dataParam = {
                mobile: mob,
                pwd: pwd,
                sms_captcha: code,
                sms_type: send_type
            };
            var do_str = ""
            var url = mobile_url;
            if (isreg == 0) { //重置密码
                do_str ="重置密码";
                url += 'account/password';
            } else {
                do_str ="注册";
                url += 'account/register_do';
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
                        
                        location.href = referer_url; //操作成功跳转到来源页
                    } else {
                        alert(data.message);
                    }
                }
            });

        });
    },
    set_login: function(mob) {
        var _this = this;
        var tmp = '<p class="mycart_tle">请输入密码登录</p>' +
            '<div class="mycart_user_content">' +
            '<p>手机号码&nbsp;&nbsp;&nbsp;&nbsp;{mobile} <span id="forgot_pwd" class="right">忘记密码</span> </p>' +
            '<p class="mycart_input_p rel">' +
            '<label for="pwd">登录密码</label>' +
            '<input type="password" id="pwd" name="pwd" placeholder="请输入你设置的银品惠密码"> </p><a id="submit_tel_pwd" class="btnok">确认</a></div>';
        tmp = tmp.replace('{mobile}', mob);
        $("#mycart_user").html(tmp);
        $("#page_tit").text('用户登录');
        $("#forgot_pwd").off('click').on('click', function() { //忘记密码就发短信重置
            _this.set_password(mob, 0);
        });
        $("#submit_tel_pwd").off('click').on('click', function() {
            var account_name = mob;
            var account_pwd = $.trim($("#pwd").val());
            if (account_pwd.length < 6) {
                alert('密码至少6位以上!');
                $("#pwd").focus();
                return false;
            }
            var dataParam = {
                username: account_name,
                password: account_pwd,
                expries: 1
            };
            $.ajax({
                type: "POST",
                url: mobile_url + 'account/login_do',
                dataType: "jsonp",
                data: dataParam,
                cache:false,
                success: function(data) {
                    if (data.success == true) {
                        location.href = referer_url; //登录成功跳转到来源页
                    } else {
                        alert(data.message);
                    }
                }
            });
        });
    }
};


//是否使用微信支付
function is_weixin(){
    var ua = navigator.userAgent.toLowerCase();
    if(ua.match(/MicroMessenger/i)=="micromessenger") {
        return true;
    } else {
        return false;
    }
}