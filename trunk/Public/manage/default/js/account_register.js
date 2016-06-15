var modal_dom_id = "my-alert";
var model_confrm_dom_id = "my-confrm";
var postField={};
$(function() {
	$('#signup-wrapper a').click(function(){
		var method = $(this).attr('data-type');
		var title =  $(this).attr('title');

		openWindow(mobile_url+'oauth/'+method+'?display=web',title,500,500);
	});
    //表单验证
    $('#form_register').validator({
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
        }
    });
    
     $("#btn_submit").click(function(){
            var that = $("#btn_submit");

            if (that.hasClass("isLoading")) {
                return false;
            }
            form_tools._disable_btn(that);
            postField.mobile = $('#mobilephone').val();
            postField.pwd = $('#account_pwd').val();
            postField.sms_captcha = $('#msg_code').val();
            var dataParam = postField;
            $.ajax({
                type: "POST",
                url: index_url + php_self + '?m=account.register_do',
                data: dataParam,
				dataType: "jsonp",
                cache:false,				
                success: function(data) {                    				
                    if (data.success == true) {
                        MODAL_HTML._alert(modal_dom_id, "操作成功", "您已注册成功！", "确定");
                        $("#" + modal_dom_id).modal();
                        form_tools._enable_btn(that);
                        location.href = index_url + 'manage.php?m=bill.home';
                    } else {
                        MODAL_HTML._alert(modal_dom_id, "操作失败", data.message, "确定");
                        $("#" + modal_dom_id).modal();
                        form_tools._enable_btn(that);
                        return false;
                    }
                }
            });
            return false;
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
            $.ajax({
                type: "POST",
                url: index_url + php_self + '?m=account.send_verify_code',
                dataType: "jsonp",
                data: {
                    mobile: $("#mobilephone").val(),
                    verify_code: $("#img_vcode").val()
                },
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
                                    //alert('啥都不干!');
                                }
                            });
                        } else if (data.status == -2) { //已经注册过
                            MODAL_HTML._confirm(model_confrm_dom_id, "操作失败", "这个手机号已经注册过！您可以直接登录或找回密码！", "找回密码", "直接登录");
                            $("#" + model_confrm_dom_id).modal({
                                closeViaDimmer: false,
                                relatedTarget: this,
                                onConfirm: function(e) {
                                    location.href = index_url + php_self + "?m=account.login";
                                },
                                onCancel: function(e) {
                                    location.href = index_url + php_self + "?m=account.forget";
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
            _this.removeClass('isLoading').html('<i class="am-icon-user am-icon-fw"></i> 确认注册').removeAttr('disabled');
        }
    }
};
function openWindow(url,name,iWidth,iHeight)
{
	var url;                                 //转向网页的地址;
	var name;                           //网页名称，可为空;
	var iWidth;                          //弹出窗口的宽度;
	var iHeight;                        //弹出窗口的高度;
	var iTop = (window.screen.availHeight-30-iHeight)/2;       //获得窗口的垂直位置;	
	var iLeft = (window.screen.availWidth-10-iWidth)/2;           //获得窗口的水平位置;	
	window.open(url,name,'height='+iHeight+'px,width='+iWidth+'px,top='+iTop+'px,left='+iLeft+'px,toolbar=no,menubar=no,scrollbars=auto,resizeable=no,location=no,status=no');	
}