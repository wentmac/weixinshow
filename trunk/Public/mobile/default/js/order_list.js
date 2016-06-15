$(function() {
	orderlist.init();
	orderlist.get_order_list();
	setInterval("get_timer()", 1000);
	$(window).scroll(function() { //内容懒加载
		if ($(document).height() - 50 <= parseFloat($(window).height()) + parseFloat($(window).scrollTop()) && global_crrent_page < global_total_page) {
			orderlist.get_order_list();

		}
	});
	$("#waiting_payment").click(function() {
		orderlist.order_type_click("waiting_payment", $(this));
	});
	$("#wating_seller_delivery").click(function() {
		orderlist.order_type_click("wating_seller_delivery", $(this));
	});
	$("#wating_receiving").click(function() {
		orderlist.order_type_click("wating_receiving", $(this));
	});
	$("#wating_comment").click(function() {
		orderlist.order_type_click("wating_comment", $(this));
	});
	$("#complete").click(function() {
		orderlist.order_type_click("complete", $(this));
	});
	$("#close").click(function() {
		orderlist.order_type_click("close", $(this));
	});


});
var _status = global_status;
var global_crrent_page = 0; //当前为第几页面
var global_total_page = 0; //总页数
var orderlist = {
	order_type_click: function(status, _this) {
		_status = status;
		orderlist.init();
		orderlist.get_order_list("");
		$("#tabPlus ul li").eq($("#tabPlus ul li").find(".cus").index()).children("a").removeClass("cus");
		$("#tabPlus ul li a").removeClass("cur");
		_this.addClass("cur");
	},
	init: function() {
		global_crrent_page = 0; //当前为第几页面
		global_total_page = 0; //总页数
		$('#js_orderList').html("");
	},
	//获取全部订单列表
	get_order_list: function() {
		$("#scroll_loading_txt").show();
		global_crrent_page++;
		$.ajax({
			type: "get",
			url: mobile_url + "/member/order.get_list",
			data: {
				page: global_crrent_page,
				status: _status
			},
			cache: false,
			success: function(data) {
				if (data.success == true) {
					global_total_page = data.data.retHeader.totalpg;
					var list = data.data.reqdata;
					var _order_list = "";
					for (var i = 0; i < list.length; i++) {
						_order_list += ' <nav class="shopList">';
						_order_list += '   <h2><a class="left chevron-left" href="/shop/' + list[i].item_uid + '" orderid="' + list[i].order_id + '">' + list[i].shop_name + '</a><span class="right color-red">' + list[i].order_status_text + ' </span></h2>';
						var _goods_array = list[i].order_goods_array;

						for (var j = 0; j < _goods_array.length; j++) {
							_order_list += '   <nav class="probody maxheight">';
							_order_list += '     <a class="product" href="' + mobile_url + 'member/order.detail?sn=' + list[i].order_sn + '">';
							_order_list += '       <div class="flex">';
							_order_list += '         <div class="flex-item"><img class="p-img" src="' + _goods_array[j].goods_image_url + '"></div>';
							_order_list += '         <div class="flex-auto p-details">';
							_order_list += '           <div class="flex">';
							_order_list += '             <div class="flex-auto"><span class="p-name color-dark">' + _goods_array[j].item_name + '' + _goods_array[j].goods_sku_name + '</span></div>';
							_order_list += '             <div class="flex-item">';
							_order_list += '               <div class="color-dark p-desc">¥' + _goods_array[j].item_price + '</div>';
							_order_list += '               <div class="color-grey p-desc">× ' + _goods_array[j].item_number + '</div>';
							_order_list += '             </div>';
							_order_list += '         </div></div></div></a>';
							_order_list += '   </nav>';
						}
						_order_list += '   <p class="p-sum"><span>共<em class="color-grey">' + _goods_array.length + '</em>件商品</span><span class="p-fee right">总价：<em class="color-dark">¥' + list[i].order_amount + '</em></span><span class="p-fee p-express right"> </span></p>';
						_order_list += '   <p class="p-actions clearfix ">';
						_order_list += '<span class="btn btncancel refundHb"><a href="' + mobile_url + 'member/order.detail?sn=' + list[i].order_sn + '"><em>查看详细</em></a></span>';
						switch (parseInt(list[i].order_status)) {
							case 1:

								_order_list += '<span class="btn btncancel refundHb"><a class="cancel" data-index="' + i + '"  data-order-sn="' + list[i].order_sn + '"><em>取消订单</em></a></span>';

								_order_list += '<span class="btn btncancel refundHb"><a href="' + mobile_url + 'order/payment?sn=' + list[i].order_sn + '"><em>付款</em></a></span>';
								break;
							case 2:
								_order_list += '<span class="btn btncancel refundHb"><a href="' + mobile_url + 'member/order.refund?sn=' + list[i].order_sn + '"><em>退款</em></a></span>';
								break;
							case 3:
								_order_list += '<span class="btn btncancel refundHb"><a class="order_confirm" data-index="' + i + '"  data-order-sn="' + list[i].order_sn + '"><em>确认收货</em></a></span>';
								if (list[i].extend_confirm_deadline_time_status == true) {
									_order_list += '<span class="btn btncancel refundHb"><a class="order_extend" data-index="' + i + '"  data-order-sn="' + list[i].order_sn + '"><em>延迟收货</em></a></span>';
								}
								if (list[i].confirm_deadline_time != 0) {
									_order_list += "<small class='end_timer'>还有<em class=\"color-red timer\" data-s=" + list[i].confirm_deadline_time + " id='timer" + i + "'>" + showTime(list[i].confirm_deadline_time) + "</em>&nbsp;将自动收货</small>";

								}
								break;
							case 5:
								if (parseInt(list[i].order_type) == 0) {
									if (parseInt(list[i].comment_status) == 0) {
										_order_list += '<span class="btn btncancel refundHb"><a href="' + mobile_url + 'member/order.comment?sn=' + list[i].order_sn + '"><em>评价</em></a></span>';
									}
								}
								if (list[i].return_service_status == "1") {
									_order_list += '<span class="btn btncancel refundHb"><a href="' + mobile_url + 'member/order.detail?sn=' + list[i].order_sn + '"><em>申请售后</em></a></span>';
								}
								break;
						}

						_order_list += '</p>';
						_order_list += ' </nav>';

					}

				}

				$('#js_orderList').html($('#js_orderList').html() + "<div style='display:block'>" + _order_list + "</div>");
				$("#scroll_loading_txt").hide();
				orderlist.button_fuction_click();



			},
			error: function() {
				$("#scroll_loading_txt").hide();
				alert("系统正忙,请稍后再试...")
			}
		});

	},
	button_fuction_click: function() {



		$(".cancel").bind("click", function() {
				if (confirm("请确认要取消订单吗？")) {
					var _this = $(this);
					var shopList = _this.parents().find('.shopList');
					$.ajax({
						type: "post",
						url: mobile_url + "member/order.cancel",
						data: {
							sn: $(this).attr("data-order-sn")
						},
						cache: false,
						success: function(data) {
							if (data.success == true) {
								_this.parent("span").hide();
								shopList.find("h2 span").html(data.data.order_status_text);
							} else {
								alert(data.message);
							}
						},
						error: function(data) {
							alert("系统繁忙请稍后再试...");
						}
					});
				}
			}),

			//处理按钮事件---待完成
			$(".order_confirm").bind("click", function() {
				if (confirm("是否要确认收货吗？")) {
					var _this = $(this);
					var shopList = _this.parents().find('.shopList');
					$.ajax({
						type: "post",
						url: mobile_url + "member/order.confirm",
						data: {
							sn: $(this).attr("data-order-sn")
						},
						cache: false,
						success: function(data) {
							if (data.success == true) {
								_this.parent("span").hide();
								shopList.find("h2 span").html(data.data.order_status_text);
								_this.parents(".shopList").find(".end_timer").hide();
								
							} else {
								alert(data.message);
							}
						},
						error: function(data) {
							alert("系统繁忙请稍后再试...");
						}
					});
				}
			});
		$(".order_extend").bind("click", function() {
			if (confirm("是否要延迟收货吗？")) {
				var _this = $(this);
				var shopList = _this.parents().find('.shopList');
				$.ajax({
					type: "post",
					url: mobile_url + "member/order.extend_confirm",
					data: {
						sn: $(this).attr("data-order-sn")
					},
					cache: false,
					success: function(data) {
						if (data.success == true) {
							_this.parent("span").hide();
						} else {
							alert(data.message);
						}
					},
					error: function(data) {
						alert("系统繁忙请稍后再试...");
					}
				});
			}
		});
	}
};

function showTime(t) {
	t -= 1;
	if (t == 0) {
		return "已经自动收货";
	}
	//每秒执行一次,showTime()  

	return arrive_timer_format(t);
}

function arrive_timer_format(s) {
	var t;
	if (s > -1) {
		hour = Math.floor(s / 3600);
		min = Math.floor(s / 60) % 60;
		sec = s % 60;
		day = parseInt(hour / 24);
		if (day > 0) {
			hour = hour - 24 * day;
			t = day + "天 " + hour + "小时";
		} else t = hour + "小时";
		if (min < 10) {
			t += "0" + "分";
		} else {
			t += min + "分";
		}

		if (sec < 10) {
			t += "0" + "秒";
		} else {
			t += sec + "秒";
		}
	}

	return t;
}


function get_timer() {


	for (var i = 0; i < $(".timer").length; i++) {
		var t = arrive_timer_format($(".timer").eq(i).attr("data-s"));
		$(".timer").eq(i).html(t);
		$(".timer").eq(i).attr("data-s", parseInt($(".timer").eq(i).attr("data-s")) - 1);
	}
}