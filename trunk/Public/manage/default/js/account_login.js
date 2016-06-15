var modal_dom_id = "my-alert";

//页面加载完执行
$(function() {
	$('#signup-wrapper a').click(function(){
		var method = $(this).attr('data-type');
		var title =  $(this).attr('title');

		openWindow(mobile_url+'oauth/'+method+'?display=web',title,500,500);
	});
    //表单验证
    $('#form_login').validator({
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
            var that = $("#btn_submit");
            if (that.hasClass("isLoading")) {
                return false;
            }
            form_tools._disable_btn(that);
            var account_name = $("#account_name").val().trim();
            var account_pwd = $("#account_pwd").val().trim();
            postField.username = account_name;
            postField.password = account_pwd;
            postField.expries = $("#rember_pwd").attr("checked") == "checked" ? 1 : 0;

            var dataParam = postField;
            $.ajax({
                type: "POST",
                url: index_url + php_self + '?m=account.login_do',
                data: dataParam,
                dataType: "jsonp",
                success: function(data) {
                    //console.log(data);
                    if (data.success == true) {
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
        }
    });

});

var form_tools = {
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