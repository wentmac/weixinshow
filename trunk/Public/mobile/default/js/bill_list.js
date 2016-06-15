$(function() {
	bill_list.init();
	bill_list.get_bill_list();	
	$(window).scroll(function() { //内容懒加载
		if ($(document).height() <= parseFloat($(window).height()) + parseFloat($(window).scrollTop()) && global_crrent_page < global_total_page) {		
			bill_list.get_bill_list();

		}
	});
	//current tab
	$("#tabPlus ul li a").removeClass('cur');
	$('#'+global_status).addClass('cur');

	$("#all").click(function() {
		bill_list.bill_click("all", $(this));
	});
	$("#in").click(function() {
		bill_list.bill_click("in", $(this));
	});
	$("#waiting_confirm").click(function() {
		bill_list.bill_click("waiting_confirm", $(this));
	});
	$("#expense_withdrawals_ing").click(function() {
		bill_list.bill_click("expense_withdrawals_ing", $(this));
	});
	$("#expense_withdrawals_success").click(function() {
		bill_list.bill_click("expense_withdrawals_success", $(this));
	});

});
var _status = global_status;
var global_crrent_page = 0; //当前为第几页面
var global_total_page = 1; //总页数
var bill_list = {
	bill_click: function(status, _this) {
		_status = status;
		bill_list.init();
		bill_list.get_bill_list("");
		$("#tabPlus ul li a").removeClass('cur');
		$('#'+_status).addClass('cur');
	},
	init: function() {
		global_crrent_page = 0; //当前为第几页面
		global_total_page = 1; //总页数
		$('#js_bill_list').html("");
		$("#scroll_loading_txt").show();
	},
	//获取全部订单列表
	get_bill_list: function() {		
		global_crrent_page++;
		if (global_crrent_page > global_total_page) {                        
			$("#scroll_loading_txt").hide();
			return true;
		}
		$.ajax({
			type: "get",
			url: mobile_url + "member/bill.get_bill_list",
			data: {
				page: global_crrent_page,
				status: _status
			},
			cache: false,
			success: function(data) {
				if (data.success == true) {
					if ( data.data.retHeader.totalput == 0 ) {
						$("#scroll_loading_txt").hide();
						return true;
					}
					global_total_page = data.data.retHeader.totalpg;
					var list = data.data.reqdata;
					var _order_list = "";
					for (var i = 0; i < list.length; i++) {

						_order_list += '<nav class="shopList">';
						_order_list += '	<nav class="probody maxheight">';
						_order_list += '	     <a class="product" href="'+mobile_url+'member/order.detail?id='+list[i].order_id+'">';
						_order_list += '	     <div class="flex">';
						_order_list += '	     	<div class="flex-item"><img class="p-img" src="'+list[i].bill_image_id+'"></div>';
						_order_list += '	     	<div class="flex-auto p-details">';
						_order_list += '	     		<div class="flex">';
						_order_list += '	     			<div class="flex-auto"><span class="p-name color-dark">'+list[i].bill_note+'</span></div>';
						_order_list += '	     			<div class="flex-item">';
						_order_list += '	     			    <div class="color-dark p-desc">￥'+list[i].money+'</div>';
						_order_list += '	     			    <div class="color-grey p-desc">'+list[i].bill_status+'</div>';
						_order_list += '	     			</div>';
						_order_list += '	     		</div>';
						_order_list += '	     	</div>';
						_order_list += '	     </div>';
						_order_list += '	     </a>';
						_order_list += '	</nav>';
						_order_list += '	<p class="p-sum p-actions clearfix">';
						_order_list += '		<span>'+list[i].bill_time+'</span>';
						_order_list += '		<span class="btn btncancel refundHb"><a href="'+mobile_url+'member/order.detail?id='+list[i].order_id+'"><em>查看详细</em></a></span>';
						_order_list += '	</p>';
						_order_list += '</nav>';
					}

				}				
				$("#js_bill_list").append(_order_list);
				$("#scroll_loading_txt").hide();									
			},
			error: function() {
				$("#scroll_loading_txt").hide();
				alert("系统正忙,请稍后再试...")
			}
		});

	}
};