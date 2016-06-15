$(function() {
	refundlist.init();
	refundlist.get_refund_list();

	$(window).scroll(function() { //内容懒加载
		if ($(document).height() - 50 <= parseFloat($(window).height()) + parseFloat($(window).scrollTop()) && global_crrent_page < global_total_page) {
			refundlist.get_refund_list();
		}
	});
	$("#seller_confirm").click(function() {
		refundlist.refund_type_click("seller_confirm", $(this));
	});
	$("#buyer_confirm").click(function() {
		refundlist.refund_type_click("buyer_confirm", $(this));
	});
	$("#customer_confirm").click(function() {
		refundlist.refund_type_click("customer_confirm", $(this));
	});
	$("#complete").click(function() {
		$("#li_refund_list").hide();
		refundlist.refund_type_click("complete", $(this));
	});
	$("#close").click(function() {
		refundlist.refund_type_click("close", $(this));
		$("#li_refund_list").hide();
	});
	$("#a_refund_list").click(function(){
		$("#li_refund_list").show();
		
		refundlist.refund_type_click("seller_confirm", $("#seller_confirm"));
		
	});
	
	$("#tabPlus ul li a").click(function(){
		$("#tabPlus ul li a").removeClass("cur");
		$(this).addClass("cur");
	});
	$("#li_refund_list ul li a").click(function(){
		$("#li_refund_list ul li a").removeClass("cur2");
		$(this).addClass("cur2");
	});

});

var global_crrent_page = 0; //当前为第几页面
var global_total_page = 0; //总页数
var _status = global_status;
var refundlist = {
	refund_type_click: function(status, _this) {
		_status = status;
		refundlist.init();
		refundlist.get_refund_list();
		
	},
	init: function() {
		global_crrent_page = 0; //当前为第几页面
		global_total_page = 0; //总页数
		$('#js_refundList').html("");
	},
	//获取全部订单列表
	get_refund_list: function() {
		$("#scroll_loading_txt").show();
		global_crrent_page++;
		$.ajax({
			type: "get",
			url: mobile_url + "/member/refund.get_list",
			data: {
				page: global_crrent_page,
				status: _status
			},
			cache:false,
			success: function(data) {
				if (data.success == true) {
					global_total_page = data.data.retHeader.totalpg;
					var list = data.data.reqdata;
					var _refund_list = "";
					for (var i = 0; i < list.length; i++) {
						_refund_list += ' <nav class="shopList">';
						_refund_list += '   <h2><a class="left chevron-left" href="/shop/' + list[i].item_uid + '" refundid="' + list[i].refund_id + '">' + list[i].shop_name + '</a><span class="right color-red">' + list[i].status_text + ' </span></h2>';
						var _goods_array = list[i].order_goods_array;

						for (var j = 0; j < _goods_array.length; j++) {
							_refund_list += '   <nav class="probody maxheight">';
							_refund_list += '     <a class="product" href="' + mobile_url + 'member/order.detail?sn=' + list[i].order_sn + '">';
							_refund_list += '       <div class="flex">';
							_refund_list += '         <div class="flex-item"><img class="p-img" src="' + _goods_array[j].goods_image_url + '"></div>';
							_refund_list += '         <div class="flex-auto p-details">';
							_refund_list += '           <div class="flex">';
							_refund_list += '             <div class="flex-auto"><span class="p-name color-dark">' + _goods_array[j].item_name + '' + _goods_array[j].goods_sku_name + '</span></div>';
							_refund_list += '             <div class="flex-item">';
							_refund_list += '               <div class="color-dark p-desc">¥' + _goods_array[j].item_price + '</div>';
							_refund_list += '               <div class="color-grey p-desc">× ' + _goods_array[j].item_number + '</div>';
							_refund_list += '             </div>';
							_refund_list += '         </div></div></div></a>';
							_refund_list += '   </nav>';
						}
						_refund_list += '   <p class="p-sum"><span>共<em class="color-grey">' + _goods_array.length + '</em>件商品</span><span class="p-fee right">总价：<em class="color-dark">¥' + list[i].money + '</em></span><span class="p-fee p-express right"> </span></p>';
						_refund_list += '   <p class="p-actions clearfix ">';
						_refund_list += '<span class="btn btncancel refundHb"><a href="' + mobile_url + 'member/order.detail?sn=' + list[i].order_sn + '"><em>查看详细</em></a></span>';
						var service_status = list[i].service_status;
						var refund_status = list[i].refund_status;
						var return_status = list[i].return_status;
						console.log(return_status);
						if (service_status == 2 && refund_status == 2 && return_status == 1) {
							_refund_list += '<span class="btn btncancel refundHb"><a href="' + mobile_url + 'member/order.detail?sn=' + list[i].order_sn + '"><em>退货</em></a></span>';

						}
						if (service_status == 2 ) {
							_refund_list += '<span class="btn btncancel refundHb"><a class="cancel" data-order-refund-id="'+list[i].order_refund_id+'" ><em>取消退款</em></a></span>';							
						}
						if (service_status == 2 && refund_status == 3) {							
							_refund_list += '<span class="btn btncancel refundHb"><a class="kf1" data-order-refund-id="'+list[i].order_refund_id+'" ><em>客服介入</em></a></span>';

						}
						if (service_status == 2 && refund_status == 2 && return_status == 4) {

							_refund_list += '<span class="btn btncancel refundHb"><a href="' + mobile_url + 'member/order.detail?sn=' + list[i].order_sn + '"><em>修改退货信息</em></a></span>';
							_refund_list += '<span class="btn btncancel refundHb"><a class="kf2"  data-order-refund-id="'+list[i].order_refund_id+'"   onclick="refund_cancel(\'' + list[i].order_sn + '\')"><em>客服介入</em></a></span>';
						}

						_refund_list += '</p>';
						_refund_list += ' </nav>';
						
					}

				}

				$('#js_refundList').html($('#js_refundList').html() + "<div style='display:block'>" + _refund_list + "</div>");
				$("#scroll_loading_txt").hide();
				refundlist.button_fuction_click();
			},
			error: function() {
				$("#scroll_loading_txt").hide();
				alert("系统正忙,请稍后再试...")
			}
		});
	},
	button_fuction_click: function() {
		$(".btn .cancel").bind("click", function() {
				if (confirm("请确认要取消订单吗？")) {
					var _this = $(this);
					var shopList = _this.parents().find('.shopList');
					$.ajax({
						type: "post",
						url: mobile_url + "member/refund.returned_cancel",
						data: {
							order_refund_id: _this.attr("data-order-refund-id")
						},
						cache:false,
						success: function(data) {
							if (data.success == true) {
								_this.parent("span").hide();
								shopList.find("h2 span").html(data.data.refund_status_text);
								shopList.find(".kf1").parent("span").hide();
								
								
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
			$(".btn .kf1").bind("click", function() {
				var _this = $(this);
				var shopList = _this.parents().find('.shopList');
				$.ajax({
					type: "post",
					url: mobile_url + "member/refund.intervene_refund",
					data: {
						order_refund_id: _this.attr("data-order-refund-id")
					},
					cache:false,
					success: function(data) {
						if (data.success == true) {
							alert("已经成功申请客服介入");
							_this.parent("span").hide();
							shopList.find(".kf1").parent("span").hide();
							shopList.find("h2 span").html(data.data.refund_status_text);
						} else {
							alert(data.message);
						}
					},
					error: function(data) {
						alert("系统正忙，请稍后再试...")
					}
				});
			}),
			$(".btn .kf2").bind("click", function() {
				var _this = $(this);
				var shopList = _this.parents().find('.shopList');
				$.ajax({
					type: "post",
					url: mobile_url + "member/refund.intervene_return",
					data: {
						order_refund_id:  _this.attr("data-order-refund-id")
					},
					cache:false,
					success: function(data) {
						if (data.success == true) {
							alert("已经成功申请客服介入");
							
							shopList.find("h2 span").html(data.data.refund_status_text);
						} else {
							alert(data.message);
						}
					},
					error: function(data) {
						alert("系统正忙，请稍后再试...")
					}
				});
			})
	}

};