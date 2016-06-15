var param = {

}
$(function() {
	data_builder.get_shop_list();
	$(".subclass").click(function() {
		param.goods_cat_id = $(this).attr("data_id");
		data_builder.init();
		data_builder.get_shop_list();
		$("#cate2 li a div img").removeClass("am-btn-warning");
		$("#cate2 li a div img").addClass("am-btn-default");
		$(this).find("div img").addClass("am-btn-warning");

	});
	$(window).scroll(function() { //内容懒加载
		if ($(document).height() - 50 <= parseFloat($(window).height()) + parseFloat($(window).scrollTop()) && global_current_page < global_total_page) {
			if ($(".modal-loading").is(":hidden")) {
				data_builder.get_shop_list();
			}
		}
	});


	$("#btn_back").click(function() {
		window.history.go(-1);
	});
	$("#btn_search").click(function() {

		if ($("#txt_search").val() != "") {
			data_builder.init();
			param.query_string = $("#txt_search").val();
			data_builder.get_shop_list();
		}
	});
});
var global_current_page = 0;
var global_total_page = 0;
var data_builder = {
	init: function() {
		global_current_page = 0;
		global_total_page = 0;
		$("#goods_list").html("");
	},
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
			url: mobile_url + "market/get_goods_list?is_coud_product=2",
			data: param,
			success: function(data) {
				if (data.success == true) {
					var str_html = "";
					global_total_page = data.data.retHeader.totalpg;
					var list = data.data.reqdata;
					for (var i = 0; i < list.length; i++) {
						var item = list[i];
						str_html += '<li class="bg-white am-u-sm-12 am-margin-top-sm am-padding-xs">';
						str_html += '<div class="am-u-sm-4 am-padding-xs">';
						str_html += '<img class="am-u-sm-12 am-margin-0 am-padding-0" src="' + item.goods_image_url + '">';
						str_html += '</div>';
						str_html += '<div class="am-u-sm-8 am-padding-0">';
						str_html += '<p class="am-text-xs am-margin-0">' + ((item.goods_name.length > 30) ? item.goods_name.substr(0, 30) + '...' : item.goods_name) + '</p>';
						str_html += '<p class="am-text-xs am-u-sm-12 am-margin-0 am-padding-left-0">';
						str_html += '<span class="am-u-sm-6 am-padding-0 am-text-xs">现金:<em class="am-text-danger am-text-xs">￥' + item.goods_price + '</em></span>';
						if(item.commission_seller_different==0){
						str_html += '<span class="am-u-sm-6 am-padding-0 am-text-xs">佣金:<em class="am-text-danger am-text-xs">￥' + item.commission_fee + '</em></span>';
						}else{
						str_html += '<span '+member_class_stype0+' class="am-u-sm-6 am-padding-0 am-text-xs">普通佣金:<em class="am-text-danger am-text-xs">￥' + item.commission_fee_array.commission_seller_free_fee + '</em></span>';	
						str_html += '<span '+member_class_stype1+' class="am-u-sm-6 am-padding-0 am-text-xs">vip佣金:<em class="am-text-danger am-text-xs">￥' + item.commission_fee_array.commission_seller_vip_fee + '</em></span>';	
						str_html += '<span '+member_class_stype2+' class="am-u-sm-6 am-padding-0 am-text-xs">svip佣金:<em class="am-text-danger am-text-xs">￥' + item.commission_fee_array.commission_seller_svip_fee + '</em></span>';	
							
						}
						str_html += '<p class=" am-u-sm-12 am-margin-0 am-padding-left-0">';
						str_html += '<span class="am-u-sm-6 am-padding-left-0"><a href="' + mobile_url + 'market/goods_detail?id=' + item.goods_id + '" class="am-btn am-block am-text-xs am-u-sm-11 am-btn-default am-radius btn_detail" data_goods_id=' + item.goods_id + ' >货源详细 </a></span>';
						str_html += '<span class="am-u-sm-6 am-padding-left-0"><a class="am-btn am-block am-text-xs am-u-sm-11 am-btn-danger am-radius btn_create" data_goods_id="' + item.goods_id + '" data_status="' + item.wholesale + '">' + ((item.wholesale == true) ? '立即下架' : '立即上架') + '</a></span>';
						str_html += '</p>';
						str_html += ' </div>';
						str_html += '</li >';
					}
					$("#goods_list").html($("#goods_list").html() + str_html);
					$(".modal-loading").hide();

					data_builder.good_create();
				}

			}
		});
	}
}