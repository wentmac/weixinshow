$(function() {
	$("#confirm_order").click(function() {
		if ($("#uname").val() == "") {
			alert($("#uname").attr("data-msg"));
			return;
		}

		if ($("#umobile").val() == "") {
			alert($("#umobile").attr("data-msg"));
			return;
		}

		checkMobile($("#umobile").val());
		receivable.init();

	});
});
var receivable = {
	init: function() {
		$.ajax({
			type: "post",
			url: "/receivable/order_save",
			data: {
				id: global_receivable_info.receivable_id,
				realname: $("#uname").val(),
				mobile: $("#umobile").val()
			},
			cache:false,
			success: function(data) {
				if (data.success == true) {
					location.href = mobile_url + 'order/payment?sn=' + data.data;

				} else {
					alert(data.message);
				}
			}
		});
	}
}

function checkMobile(str) {
	var re = /^1\d{10}$/
	if (!re.test(str)) {
		alert("请输入正确的手机号");
		return;
	}
}