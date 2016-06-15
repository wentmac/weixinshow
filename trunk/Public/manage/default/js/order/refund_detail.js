$(function() {
	$("#agree_refund").click(function() {

		data_builder.agree_refund();
	});
	$("#refused_refund").click(function() {
		data_builder.refused_refund();
	});
	$("#goods_ok").click(function() {
		data_builder.goods_ok();
	});
	$("#goods_no").click(function() {
		data_builder.goods_no();
	});
});
var data_builder = {
	agree_refund: function() {
		var _this = this;
		$('#my-confirm .am-modal-bd').html("请确认是否同意退款");
		$('#my-confirm').modal({
			relatedTarget: this,
			onConfirm: function(e) {
				$.ajax({
					type: "post",
					url: php_self + "?m=order.refund_yes",
					data: {
						order_refund_id: $("#agree_refund").attr("data_refund_id")
					},
					cache:false,
					success: function(data) {
						if (data.success) {
							$(_this).parents("p").html("");
							$(_this).parents("p").html(data.data.refund_status_text);
							M._alert("操作成功");
						} else {
							M._alert(data.message);
						}
					},
					error: function(data) {
						M._alert(data.message);
					}
				});
			},
			onCancel: function(e) {
				M._alert('好好考虑考虑!');
			}
		});
	},
	refused_refund: function() {
		$('#my-prompt .am-modal-bd').html("请确认是否同意退款");
		$('#my-prompt').modal({
			relatedTarget: this,
			onConfirm: function(e) {
				var _this = this;
				$.ajax({
					type: "post",
					url: php_self + "?m=order.refund_no",
					data: {
						order_refund_id: $("#refused_refund").attr("data_refund_id"),
						reason: e.data
					},
					cache:false,
					success: function(data) {
						if (data.success) {
							$(_this).parents("p").html("");
							$(_this).parents("p").html(data.data.refund_status_text);
							M._alert("操作成功");
						} else {
							M._alert(data.message);
						}
					},
					error: function(data) {
						M._alert(data.message);
					}
				});
			},
			onCancel: function(e) {
				M._alert('好好考虑考虑!');
			}
		});
	},
	goods_ok: function() {
		var _this = this;

		$('#my-confirm .am-modal-bd').html("请确认已经收到退货");
		$('#my-confirm').modal({
			relatedTarget: this,
			onConfirm: function(e) {
				$.ajax({
					type: "post",
					url: php_self + "?m=order.receipt_yes",
					data: {
						order_refund_id: $("#goods_ok").attr("data_refund_id")
					},
					cache:false,
					success: function(data) {
						if (data.success) {
							$("#goods_ok").parents("p").html(data.data.refund_status_text);
							M._alert("操作成功");
						} else {
							M._alert(data.message);
						}
					},
					error: function(data) {
						M._alert(data.message);
					}
				});
			},
			onCancel: function(e) {
				M._alert('好好考虑考虑!');
			}
		});


	},
	goods_no: function() {
		var _this = this;
		$('#my-confirm .am-modal-bd').html("请确认已经收到退货");
		$('#my-confirm').modal({
			relatedTarget: this,
			onConfirm: function(e) {
				$.ajax({
					type: "post",
					url: php_self + "?m=order.receipt_no",
					data: {
						order_refund_id: $("#goods_no").attr("data_refund_id")
					},
					cache:false,
					success: function(data) {
						if (data.success) {
							$("#goods_no").parents("p").html(data.data.refund_status_text);
							M._alert("操作成功");
						} else {
							M._alert(data.message);
						}
					},
					error: function(data) {
						M._alert(data.message);
					}
				});
			},
			onCancel: function(e) {
				M._alert('好好考虑考虑!');
			}
		});
	}
}