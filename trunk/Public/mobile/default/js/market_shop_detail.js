$(function() {
	$("#good_create").click(function() {
		data_builder.good_create(this);
	});
	/**
	$('.js-res-load').each(function(i,n){
		var n = $(this);
		var img_url = $(n).attr('data-src');		
		loadImage(n,img_url);
	});*/
});

function loadImage(t, e) {
	$("<img>").attr("src", e).on("load",
		function() {
			$(this).remove(),
				"img" == t.prop("tagName").toLowerCase() ? t.attr("src", e) : t.css("background-image", "url(" + e + ")")
		});
}

var data_builder = {


	confirm_click: function() {
		var browser = navigator.userAgent
		$("#confirm_yes").bind("click", function() {
			$.cookie('join', 'yes', {
				expires: 30
			});
			if (browser.indexOf('Android') > -1 || browser.indexOf('Linux') > -1) {
				window.stub.jsMethod(mobile_url + "market/native?action=qun&key=" + join_key_android);
			}

			if (browser.indexOf('iPhone') > -1 || browser.indexOf('iPad') > -1) {
				MODAL_HTML._confirm("join", "银品惠提醒-上架成功", join_title, "<a class='am-block' id='confirm_yes'>确定</a>", "<a class='am-block' id='confirm_no'>取消</a>");
			}
		});
		$("#confirm_no").bind("click", function() {
			$.cookie('join', 'no', {
				expires: 1
			});
		});
	},
	good_create: function(_this) {
		if ($(_this).attr("data_status") == "false") {
			$.ajax({
				type: "post",
				url: mobile_url + "market/create",
				data: {
					goods_id: $(_this).attr("data_goods_id")
				},
				success: function(data) {
					if (data.success == true) {
						$(_this).attr("data_status", "true");
						if (show_qqqun_status == "1") {
							if ($.cookie('join') == null) {
								MODAL_HTML._confirm("join", "银品惠提醒-上架成功", "" + join_title + "", "<a id='confirm_yes'>确定</a>", "<a id='confirm_no'>取消</a>");
								data_builder.confirm_click();
							}
						}
						$(_this).html("产品下架");
						$(_this).removeClass("loading");
						$("#join").modal();
					} else {
						alert(data.message);
					}
				}
			});
		} else {
			$.ajax({
				type: "post",
				url: mobile_url + "market/delete",
				data: {
					goods_id: $(_this).attr("data_goods_id")
				},
				success: function(data) {
					if (data.success == true) {
						alert("下架成功");
						$(_this).html("产品上架");
						$(_this).attr("data_status", "false");
					} else {
						alert(data.message);
					}
				}
			});
		}

	}

}