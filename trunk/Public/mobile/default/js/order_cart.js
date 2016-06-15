var global_timer = 0;

$(function() {
	//判断登录
	if (global_member_info.mobile == '') { //未绑定		
		login.init();
	} else {
		$("#use_other_tele").click(function() { //切换用户
			$.ajax({ //先登出
				type: "get",
				url: mobile_url + 'account/login_out',
				dataType: "jsonp",
				cache:false,
				success: function(data) {
					if (data.success == true) {
						location.reload();
					} else {
						alert(data.message);
					}
				}
			});
		});
	}
	
	if (is_weixin()) {
		$("#weixinLogin").show();
	} else {
		$("#weixinLogin").hide();
	}
	//购物车方法初始化
	cart.init();

});

var login = {

	init: function() {
		var _this = this;
		$("#mycart_user").show();
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
						//_this.set_login(tel);
						$('#tel_notice').html('* 您的手机号码系统已经存在,请更换一个').attr({ style: "color:red" });
					} else { //新用户
						_this.set_password(tel, 2);
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
			verify_code: ''
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
					$("#tel_second").show();
					global_timer = 60;
					_this._timer_sendsms();
				} else {
					alert(data.message);
				}
			}
		});
	},
	_timer_sendsms: function() { //发短信倒记时
		if (global_timer > 0) {
			$("#tel_second").text(global_timer);
			global_timer--;
			setTimeout(this._timer_sendsms, 1000);
		} else {
			$("#resend_tel_code").removeAttr('sending');
			$("#tel_second").hide();
		}
	},
	set_password: function(mob, isreg) { //isreg为0为重置密码 1为新注册 2为绑定手机号
		var _this = this;
		var tmp = '<p class="mycart_tle">已发送验证码到你的手机  <a id="speak_code" class="right hide">收不到?</a></p>' +
			'<div class="mycart_user_content">' +
			'<p>手机号码&nbsp;&nbsp;&nbsp;&nbsp;{mobile} <span id="resend_tel_code" class="right">重新发送 <em id="tel_second" style="display: none;">1</em></span> </p>' +
			'<p class="mycart_input_p rel">' +
			'<label for="code">验证码</label>' +
			'<input type="tel" maxlength="6" id="code" name="code" placeholder="填写验证码"><input type="hidden" value="{isreg}"> </p>' +
			'<p class="mycart_input_p rel hide">' +
			'<label for="code_pwd">设置密码</label>' +
			'<input type="password" id="code_pwd" name="code_pwd" placeholder="下次可用手机号+密码登录"> </p><a id="submit_tel_code" class="btnok">确认</a></div>';
		tmp = tmp.replace('{mobile}', mob);
		tmp = tmp.replace('{isreg}', isreg);
		$("#mycart_user").html(tmp);
		if ( isreg == 0 ) {
			var send_type = 2;
		}  else if ( isreg ==1 ) {
			var send_type = 1;
		} else if ( isreg == 2 ) {
			var send_type = 6;
		}		

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
			if (pwd != '' && pwd.length < 6) {
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
			var url = mobile_url;
			if (isreg == 0) { //脰脴脰脙脙脺脗毛
				url += 'account/password';
			} else if ( isreg == 1 ) {
				url += 'account/register_do';
			} else if ( isreg == 2 ) {//绑定手机号
				url += 'account/bind_mobile';
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
						location.reload(); //鲁脡鹿娄脣垄脨脗脪鲁脙忙
					} else {
						alert(data.message);
					}
				}
			});

		});
	},
	set_login: function(mob) {
		var _this = this;
		var tmp = '<p class="mycart_tle">你的号码已注册，请输入密码登录</p>' +
			'<div class="mycart_user_content">' +
			'<p>手机号码&nbsp;&nbsp;&nbsp;&nbsp;{mobile} <span id="forgot_pwd" class="right">忘记密码</span> </p>' +
			'<p class="mycart_input_p rel">' +
			'<label for="pwd">登录密码</label>' +
			'<input type="password" id="pwd" name="pwd" placeholder="请输入你设置的银品惠密码"> </p><a id="submit_tel_pwd" class="btnok">确认</a></div>';
		tmp = tmp.replace('{mobile}', mob);
		$("#mycart_user").html(tmp);
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
						location.reload(); //登录成功刷新页面
					} else {
						alert(data.message);
					}
				}
			});
		});
	}
};

var cart = {
	init: function() {
		this.bind_control();

	},
	bind_control: function() {
		var _this = this;
		$(".cart_seller_wrap").each(function(index) {
			var _wrap_this = this;
			var _wrap_index = index;
			var user_id = $(this).find('ul.cart_ul').attr('data-seller');
			var goods_uid = $(this).find('ul.cart_ul').attr('data-goods-uid');
			var $item_list = $(this).find('ul.cart_ul li');
			var $cart_seller_mask = $(this).find('.cart_seller_mask');
			var $cart_mask = $(this).find('.cart_mask');
			var $do_buy = $(this).find('.do_buy');
			$cart_mask.click(function() { //勾选
				$(this).toggleClass('already_mask');
				if ($cart_mask.filter('.already_mask').length > 0) {
					$cart_seller_mask.addClass('cart_seller_mask_already');
				} else if ($cart_mask.filter('.already_mask').length == 0) {
					$cart_seller_mask.removeClass('cart_seller_mask_already');
				}
				_this._shop_price_count(_wrap_this);
			});
			$cart_seller_mask.click(function() { //店内全选
				$(this).toggleClass('cart_seller_mask_already');
				if ($(this).hasClass('cart_seller_mask_already')) {
					$cart_mask.addClass('already_mask');
				} else {
					$cart_mask.removeClass('already_mask');
				}
				_this._shop_price_count(_wrap_this);
			});
			$do_buy.click(function() { //店内结算
				if (!_this._check_login()) {
					return false;
				}
				var count = parseInt($(this).find('.cash_count').text());
				if (count == 0) {
					alert('请至少勾选1个商品！');
					return false;
				}
				var cart_array = new Object();

				var goods_type_member_count = 0;
        		var goods_type_sale_count = 0;
        		var goods_type_other = 0;
        		var goods_type_member_monopoly_count = 0;
        		var goods_type_mall_count = 0;
        		var goods_count = 0;
				$item_list.each(function(i) {
					if ($(this).find('.cart_mask').hasClass('already_mask')) {
						cart_array[parseInt($(this).attr('data-cart-id'))] = parseInt($(this).attr('data-current-num'));
						var goods_type = $(this).attr('data-goods-type');						
						if ( goods_type == 2 ) {
							goods_type_member_count++;
						} else if ( goods_type == 3 ) {
							goods_type_sale_count++;
						} else if ( goods_type == 4 ) {//特惠商品
							goods_type_member_monopoly_count++;
						} else if ( goods_type == 5 ) {//商城商品
							goods_type_mall_count++;
						} else {
							goods_type_other++;
						}
						goods_count++;
					}
				});				
		        if ( goods_type_member_count > 0 && goods_type_other > 0 ) {
		            M._alert( '会员商品不能和其他商品一起结算哟~');
		            return false;
		        }
		        if ( goods_type_sale_count > 1 ) {
		            //M._alert( '特惠商品一次只让买一个~');
		            //return false;
		        }
		        if ( goods_type_sale_count > 0 && goods_type_member_count > 0 ) {
		            M._alert( '特惠商品不能和会员商品一起结算哟~');
		            //return false;
		        }		        
		        if ( goods_type_member_monopoly_count > 0 && goods_count > goods_type_member_monopoly_count ) {
		            M._alert( '会员专卖类型商品不能和其他商品一起结算哟~' );
		            return false;
		        }
		        if ( goods_type_mall_count > 0 && goods_count > goods_type_mall_count ) {
		            M._alert( '商城商品不能和其他商品一起结算哟~' );
		            return false;
		        }
				var dataParam = {
					item_uid: user_id,
					cart_array: cart_array
				};
				var url = mobile_url + "order/cart_batch_update";
				$.ajax({
					type: 'POST',
					url: url,
					data: dataParam,
					dataType: 'json',
					cache:false,
					success: function(data) {
						if (data.success == true) {
							location.href = mobile_url + 'order/confirm?item_uid=' + user_id + '&goods_uid=' + goods_uid;
						} else {
							alert(data.message);
						}
					}
				});
			});

			$item_list.each(function(i) {
				var _list_this = this;
				var $control_num_sub = $(this).find('.control_num_sub');
				var $control_num_add = $(this).find('.control_num_add');
				var $item_num = $(this).find('.item_num');
				var $control_count = $(this).find('.control_count');
				var item_stock = $control_count.attr('data-stock');
				var current_num = $control_count.attr('data-current-num');
				var goods_member_level = $control_count.attr('data-goods-member-level');
				var goods_type = $control_count.attr('data-goods-type');
				
				if ( goods_type==2||goods_type==3 ) {
						//会员商品 和 特惠商品一次只让买一个
			        	return ;//实现continue功能  
				}

				$control_num_sub.click(function() { //---数量					
					var num = parseInt($item_num.val());
					if (num > 1) {
						num--;
						$item_num.val(num);
						$(_list_this).attr('data-current-num', num);
						$control_count.attr('data-current-num', num);
						_this._shop_price_count(_wrap_this);
					}
				});

				$control_num_add.click(function() { //+++数量
					var num = parseInt($item_num.val());
					if (num < item_stock) {
						num++;
						$item_num.val(num);
						$(_list_this).attr('data-current-num', num);
						$control_count.attr('data-current-num', num);
						_this._shop_price_count(_wrap_this);
					} else {
						alert('此商品的库存只有' + num + '件了，不能再加了！');
					}
				});
			});
		});
		$("#hd_edit").click(function() { //删除
			var ids = "";
			var $cart_seller_mask = $('.cart_seller_mask.cart_seller_mask_already');
			var $cart_mask = $('.cart_mask.already_mask');
			$cart_mask.each(function(i) {
				ids += parseInt($(this).attr('data-cart-id'));
				if (i < $cart_mask.length - 1) {
					ids += ",";
				}
			});
			if (ids.length > 0 && ids.split(',').length > 0) {
				$.ajax({
					url: '/order/cart_delete',
					type: 'POST',
					dataType: 'json',
					data: {
						cart_id: ids
					},
					cache:false,
					success: function(data) {
						if (data.success == true) {
							$cart_mask.each(function(i) {
								$(this).parents("li.cart_li").remove();
							});

							$cart_seller_mask.each(function(ii) {
								if ($(this).parents(".cart_seller_wrap").eq(0).find("li.cart_li").length == 0) {
									$(this).parents(".cart_seller_wrap").remove();
								}
								_this._shop_price_count($(this).parents(".cart_seller_wrap").get(0));
							});

						} else {
							alert(data.message);
						}
					}
				});

			}
		});
	},
	_shop_price_count: function(shop) {
		var $money_count = $(shop).find('.money_count');
		var $use_available_integral_div = $(shop).find('.use_available_integral');
		var $total_amount_div = $(shop).find('.total_amount');
		var $cash_count = $(shop).find('.cash_count');
		var money = 0;
		var num_count = 0;
		var use_integral = false;//是否能使用积分抵扣
		var integral_value = 0;//可抵扣的积分
		var shipping_fee = 0;
		$(shop).find('ul.cart_ul li').each(function(i) {
			var $cart_mask = $(this).find('.cart_mask');
			if ($cart_mask.hasClass('already_mask')) {
				var num = parseInt($(this).attr('data-current-num'));
				var price = parseFloat($(this).attr('data-item-price'));
				var is_integral = parseInt($(this).attr('data-is-integral'));

				if ( is_integral == 1 ) {
                    use_integral = true;
                    integral_value += price * num;
                }
				money += price * num;				
				num_count += num;
			}
		});
		var money = new Number(money);				
		var use_available_integral = available_integral >= integral_value ? integral_value : available_integral;
        if ( use_integral ) {
            total_amount = (money - use_available_integral) + shipping_fee;
        } else {
            total_amount = money + shipping_fee;
        }		
        console.log(money);
        console.log(available_integral);
		console.log(total_amount);
		var use_available_integral = new Number(use_available_integral);		
		var total_amount = new Number(total_amount);		
		$money_count.text('¥' + money.toFixed(2));
		$use_available_integral_div.text('¥' + use_available_integral.toFixed(2));
		$total_amount_div.text('¥' + total_amount.toFixed(2));
		$cash_count.text(num_count);
	},
	_check_login: function() {
		if (global_member_info.uid == 0) {
			alert("亲！请先登录！");
			$(window).scrollTop(0);
			return false;
		}
		return true;
	}
};
//是否使用微信支付
function is_weixin() {
	var ua = navigator.userAgent.toLowerCase();
	if (ua.match(/MicroMessenger/i) == "micromessenger") {
		return true;
	} else {
		return false;
	}
}