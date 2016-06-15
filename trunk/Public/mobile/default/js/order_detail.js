$(function() {
	$("#tk").hide();
	if (global_have_return_service == 0) {
		if (global_refund_status == 1) {
			$("#tk").show();
			$("#refunding").show();
		} else if (global_refund_status == 2) {
			$("#tk").show();
			$("#refund_ok").show();

		} else if (global_refund_status == 3) {
			$("#tk").show();
			$("#refund_pass").show();
		}
		$(".b_txt").hide();
	}

	if (global_order_status == 1) {
		$(".b_txt").hide();
		$("#displaybtn").show();
	} else if (global_order_status == 2) {
		$(".b_txt").html("退款");
		$(".b_txt").eq(i).show();
	} else if (global_order_status == 3) {
		$(".b_txt").html("确认收货");
		$(".b_txt").eq(i).show();
	} else if (global_order_status == "5") {
		if (global_comment_status == 0) {
			$(".b_txt").html("评价");
		}		
		for (var i = 0; i < $(".o_li").length; i++) {
			var status_all = $(".hid_status_all").eq(i);
			if (status_all.attr("data_return_service_status") == "1") {
				$(".b_txt").eq(i).attr("href", mobile_url + "member/order.refund?sn=" + global_order_sn + "&order_goods_id=" + status_all.attr("data_order_goods_id"));
				$(".b_txt").eq(i).html("申请售后");
				$(".b_txt").eq(i).show();
			}
			if (parseInt(status_all.attr("data_service_status")) > 0) {
				$(".b_txt").eq(i).attr("href", mobile_url + "member/refund.detail?order_refund_id=" + status_all.attr("data_order_refund_id"));
				$(".b_txt").eq(i).html(status_all.attr("data_service_status_text"));
				$(".b_txt").eq(i).addClass("a_txt");
				$(".b_txt").eq(i).show();
			}
		}
	}

	$("#a_express").click(function() {
		data_builder.get_express_info();
	});
});

var data_builder = {
	get_express_info: function() {
		$("#a_express").html("正在查询中....");
		$.ajax({
			type: "get",
			url: mobile_url + "member/order.get_express_info",
			data: {
				express_id: global_express_id,
				express_no: global_express_no
			},
			cache:false,
			success: function(data) {
				if (data.success == true) {
					var info = data.data.express_detail;
					if (info == "") {
						info = "还没有查询到任何信息!";
					}
					$("#div_express").html(info);
					$("#a_express").html("点击查看");
				} else {
					$("#a_express").html("查询失败");
				}
			},
			error: function(data) {
				$("#a_express").html("系统正忙请稍后查询...");
			}
		});
	}
}