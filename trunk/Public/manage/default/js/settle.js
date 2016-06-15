/*
			Create By wentmac @2015

                   _ooOoo_
                  o8888888o
                  88" . "88
                  (| -_- |)
                  O\  =  /O
               ____/`---'\____
             .'  \\|     |//  `.
            /  \\|||  :  |||//  \
           /  _||||| -:- |||||-  \
           |   | \\\  -  /// |   |
           | \_|  ''\---/''  |   |
           \  .-\__  `-`  ___/-. /
         ___`. .'  /--.--\  `. . __
      ."" '<  `.___\_<|>_/___.'  >'"".
     | | :  `- \`.;`\ _ /`;.`/ - ` : | |
     \  \ `-.   \_ __\ /__ _/   .-` /  /
======`-.____`-.___\_____/___.-`____.-'======
                   `=---='
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
			佛祖保佑       永无BUG

*/
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

function showError(error){
	$('#error_info').html(error);
}

var postField={};
function send_wait() {
    global_sendsms_time--;
    if (global_sendsms_time > 0) {
        $("#btn_sendsms").text(global_sendsms_time + '秒后重新发送');
        setTimeout(send_wait, 1000);
    } else {
        $("#btn_sendsms").text('重发短信验证码').removeAttr('disabled').removeClass('isLoading');
    }
}

$(document).ready(function() {
	settle_account.account_type_select();
	$("input[name='account_type']").change(function(){		
		var account_type=$(this).val();
		if ( account_type == 1 ) {
			//siblings()
			$('#bank_div').show();
			$('#alipay_div').hide();
		} else if ( account_type == 2 ) {
			$('#bank_div').hide();
			$('#alipay_div').show();
		}				
	});
	
    //短信验证码
    $("#btn_sendsms").click(function() {
        if ( $("#btn_sendsms").hasClass('isLoading') ) {
			return false;
		}
		$.ajax({
			type: "POST",
			url: index_url + php_self + '?m=settle.send_verify_code',
			dataType: "json",
			data: {},
			success: function(data) {
				if ( data.success == true ) {
					$("#btn_sendsms").attr('disabled', 'disabled').addClass('isLoading');
					global_sendsms++;
					global_sendsms_time = 30 * global_sendsms;
					send_wait();
					$("#msg_code").focus();
				} else {					
					showError(data.message);					
				}
			}
		});        
    });	

     $("#btn_submit").click(function(){	
		$('#error_info').html('');
		var check = settle_account.form_check();
		if ( check == false ) {
			return false;
		}
					
		var that = $("#btn_submit");
		if (that.hasClass("isLoading")) {
			return false;
		}                        
		var dataParam = postField;		
		$.ajax({
			type: "POST",
			url: index_url + php_self + '?m=settle.bank_card_save',
			data: dataParam,
			dataType: "json",
			cache:false,				
			success: function(data) {                    				
				if ( data.success == true ) {
					showError("您已注册成功！");					
					location.href = index_url + php_self + '?m=settle.apply';
				} else {
					showError(data.message);					
					return false;
				}			
			}
		});
		return false;
    });	
	

     $("#apply_btn_submit").click(function(){	
		$('#error_info').html('');
		
		var money = $('#money').val();
		if ( money == 0 || money == '' ) {
			showError("请输入提现金额！");		
			$('#money').focus();
			return false;
		}
		if ( money > global_current_money ) {
			showError("提现金额大于可提现的余额！");		
			$('#money').focus();
			return false;
		}
					
		var that = $("#apply_btn_submit");
		if (that.hasClass("isLoading")) {
			return false;
		}    
		postField.money = money;
		postField.account_type = global_account_type;
		var dataParam = postField;		
		$.ajax({
			type: "POST",
			url: index_url + php_self + '?m=settle.create',
			data: dataParam,
			dataType: "json",
			cache:false,				
			success: function(data) {                    				
				if ( data.success == true ) {
					showError("您的提现申请成功！");					
					location.href = index_url + php_self + '?m=bill.home';
				} else {
					showError(data.message);					
					return false;
				}			
			}
		});
		return false;
    });		
});

var settle_account = {
	init: function() {
		var that = this;		
	},
	
	account_type_select: function(){
		settle_account.init();
		var account_type=$("input[name='account_type']:checked").val();
		if ( account_type == 1 ) {
			//siblings()
			$('#bank_div').show();
			$('#alipay_div').hide();
		} else if ( account_type == 2 ) {
			$('#bank_div').hide();
			$('#alipay_div').show();
		}
	},
	
	form_check: function(){		
		var account_type=$("input[name='account_type']:checked").val();	
		if ( account_type == 0 ) {
			showError('请选择绑定的账号类型');			
			return false;
		}
		postField.account_type = account_type;
		
		if ( account_type == 1 ) {
			//发卡银行 bank_id
			var bank_id = $('#bank_id').val();			
			if ( bank_id == '0' ) {
				showError('请选择发卡银行');				
				$('#bank_id').focus();
				return false;
			}
			postField.bank_id = bank_id;
			//银行卡号 bank_cardnum
			var bank_cardnum = $('#bank_cardnum').val();
			if ( bank_cardnum == '' ) {
				showError('请输入银行卡号');				
				$('#bank_cardnum').focus();
				return false;
			}
			postField.bank_cardnum = bank_cardnum;		
			//持卡人姓名 bank_account
			var bank_account = $('#bank_account').val();
			if ( bank_account == '' ) {
				showError('请输入持卡人姓名');				
				$('#bank_account').focus();
				return false;
			}
			postField.bank_account = bank_account;			
		} else if ( account_type == 2 ) {
			//支付宝账号 alipay_account
			var alipay_account = $('#alipay_account').val();
			if ( alipay_account == '' ) {
				showError('请输入支付宝账号');				
				$('#alipay_account').focus();
				return false;
			}
			postField.alipay_account = alipay_account;			
			//支付宝姓名 alipay_name
			var alipay_username = $('#alipay_username').val();
			if ( alipay_username == '' ) {
				showError('请输入支付宝姓名');				
				$('#alipay_username').focus();
				return false;
			}
			postField.alipay_username = alipay_username;				
		} else {
			showError('请选择绑定的账号类型');						
			return false;
		}
		
		var sms_captcha = $('#sms_captcha').val();
		if ( sms_captcha == '' ) {
			showError('请输入验证码');			
			$('#sms_captcha').focus();
			return false;
		}
		var reg = /^([0-9]){6}$/;
		if (!reg.test(sms_captcha)) { //手机号是否验证通过
			showError("请输入正确的验证码格式");			
			return false;
		}
		postField.sms_captcha = sms_captcha;		
		return true;
	}
}