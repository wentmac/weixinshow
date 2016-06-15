var param = {

}
$(function() {
	param.id = shop_id;
	data_builder.get_shop_list();
	$(".sort-btn").click(function() {
		param.sort = $(this).find("a").attr("data_sort");
		$(".sort-btn").removeClass("cur");
		$(this).addClass("cur");
		
		$("#shop_list").html("");
		global_current_page = 0;
		//		$.AMUI.progress.start();
		data_builder.get_shop_list();
		return false;
	});
	$(window).scroll(function() { //内容懒加载
		if ($(document).height() - 50 <= parseFloat($(window).height()) + parseFloat($(window).scrollTop()) && global_current_page < global_total_page) {
			if ($(".modal-loading").is(":hidden")) {
				data_builder.get_shop_list();
			}
		}
	});
});

var global_total_page = 0;
var global_current_page = 0;
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
				$("#div_qun").html("<iframe src='" + mobile_url + "market/native?action=qun&qunid=" + join_id + "&key=" + join_key + "'></iframe>");
			}
		});
		$("#confirm_no").bind("click", function() {
			$.cookie('join', 'no', {
				expires: 1
			});
		});
	},
	good_create: function() {
		$(".btn_create").bind("click", function() {
			var _this = this;
			$(_this).html("请稍等..");
			if (!$(_this).hasClass("loading")) {
				$(_this).addClass("loading");
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
									MODAL_HTML._confirm("join", "银品惠提醒-上架成功", join_title, "<a class='am-block' id='confirm_yes'>确定</a>", "<a class='am-block' id='confirm_no'>取消</a>");
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
								$(_this).removeClass("loading");
								$(_this).html("立即上架");
								$(_this).attr("data_status", "false");
							} else {
								alert(data.message);
							}
						}
					});
				}
			}
		});
	},
	get_shop_list: function() {
		$(".modal-loading").show();
		global_current_page++;
		param.page = global_current_page;
		$.ajax({
			type: "get",
			url: mobile_url + "market/get_shop_goods_list",
			data: param,
			success: function(data) {
				if (data.success == true) {
					var str_html = '';
					global_total_page = data.data.retHeader.totalpg;
					global_current_page = data.data.retHeader.page;
					var list = data.data.reqdata;
					for (var i = 0; i < list.length; i++) {
						var info = list[i];
						str_html += '<li class="am-margin-top-sm am-padding-sm border_bottom">';
						str_html += '<div>';
						str_html += '<img width="100%" class="am-margin-bottom-xs" src="' + info.goods_image_url + '">';
						str_html += '<div>';
						if (info.goods_name.length > 15) {
							str_html += info.goods_name.substr(0, 15) + '...';
						} else {
							str_html += info.goods_name;
						}
						str_html += '</div>'
						str_html += '<div class="am-text-xs">';
						str_html += '<span class="am-text-left gray">价格: </span><span class="">¥' + info.goods_price + '</span><br>';
						str_html += '<span class="am-text-left gray">佣金: </span><span class="am-text-danger">¥' + info.commission_fee + '</span>';
						str_html += '<ul class="am-avg-sm-2">';
						str_html += '  <li class="am-padding-xs am-padding-left-0">';
						str_html += '    <a class="am-btn am-btn-block am-btn-primary am-btn-xs am-radius" href="' + mobile_url + 'market/goods_detail?id=' + info.goods_id + '">预览</a>'
						str_html += '  </li>';
						str_html += '  <li class="am-padding-xs am-padding-right-0">';
						str_html += '    <a class="am-btn am-btn-block am-btn-warning am-btn-xs am-radius btn_create" data_goods_id="' + info.goods_id + '" data_status="' + info.wholesale + '">';
						if (info.wholesale == true) {
							str_html += '下架';
						} else {
							str_html += '上架';
						}
						str_html += '    </a>'
						str_html += '  </li>';
						str_html += '</ul>';
						str_html += '</div>';
						str_html += '</li>';
					}
					$("#shop_list").html($("#shop_list").html() + str_html);
					$(".modal-loading").hide();
					data_builder.good_create();
				} else {
					alert(data.message);
				}
			}
		});
	}
}