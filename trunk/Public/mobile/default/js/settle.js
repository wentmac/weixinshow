$(function() {
	settle_list.init();
	settle_list.get_settle_list();	
	$(window).scroll(function() { //内容懒加载
		if ($(document).height() <= parseFloat($(window).height()) + parseFloat($(window).scrollTop()) && global_crrent_page < global_total_page) {		
			settle_list.get_settle_list();

		}
	});
	//current tab
	$("#tabPlus ul li a").removeClass('cur');
	$('#'+global_status).addClass('cur');

	$("#all").click(function() {
		settle_list.bill_click("all", $(this));
	});
	$("#verify").click(function() {
		settle_list.bill_click("verify", $(this));
	});
	$("#success").click(function() {
		settle_list.bill_click("success", $(this));
	});
	$("#fail").click(function() {
		settle_list.bill_click("fail", $(this));
	});	

});
var _status = global_status;
var global_crrent_page = 0; //当前为第几页面
var global_total_page = 1; //总页数
var settle_list = {
	bill_click: function(status, _this) {		
		_status = status;
		settle_list.init();
		settle_list.get_settle_list("");
		$("#tabPlus ul li a").removeClass('cur');
		$('#'+_status).addClass('cur');
	},
	init: function() {
		global_crrent_page = 0; //当前为第几页面
		global_total_page = 1; //总页数
		$('#js_settle_list').html("");
		$("#scroll_loading_txt").show();
	},
	//获取全部订单列表
	get_settle_list: function() {				
		global_crrent_page++;
		if (global_crrent_page > global_total_page) {                        
			$("#scroll_loading_txt").hide();
			return true;
		}
		$.ajax({
			type: "get",
			url: mobile_url + "member/settle.get_list",
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
						_order_list += '	     <a class="product" href="#">';
						_order_list += '	     <div class="flex">';
						_order_list += '	     	<div class="flex-auto p-details">';
						_order_list += '	     		<div class="flex">';
						_order_list += '	     			<div class="flex-auto">';
						_order_list += '	     				<span class="p-name color-dark">';
			     		_order_list += '							<div>申请提现时间：'+list[i].settle_apply_time+'</div>';
			     		_order_list += '							<div><i>'+list[i].settle_note+'</i></div>';
						_order_list += '	     				</span>';
						_order_list += '	     			</div>';
						_order_list += '	     			<div class="flex-item">';
						_order_list += '	     			    <div class="color-dark p-desc">￥'+list[i].money+'</div>';
						_order_list += '	     			    <div class="color-grey p-desc">'+list[i].settle_status+'</div>';
						_order_list += '	     			</div>';
						_order_list += '	     		</div>';
						_order_list += '	     	</div>';
						_order_list += '	     </div>';
						_order_list += '	     </a>';
						_order_list += '	</nav>';
						_order_list += '	<p class="p-sum p-actions clearfix">';
						_order_list += '		<span>打款时间：'+list[i].settle_execute_time+'</span>';
						_order_list += '		<span class="nickname">微信账号：'+list[i].realname+'</span>';
						_order_list += '	</p>';
						_order_list += '</nav>';
					}

				}				
				$("#js_settle_list").append(_order_list);
				$("#scroll_loading_txt").hide();						
			},
			error: function() {
				$("#scroll_loading_txt").hide();
				alert("系统正忙,请稍后再试...")
			}
		});

	}
};