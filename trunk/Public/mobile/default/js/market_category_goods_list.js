var param = {};
$(function() {
	param.goods_cat_id = goods_cat_id;
	$(".cate2").click(function() {
		$(".cate3").hide();
		$("#cate3_" + $(this).attr("data_cate_id") + "").show();
	});
	$("#btn_search").click(function() {

		$(".goods_list").html("");
		param.query_string = $("#txtSearch").val();
		data_builder.get_hot_goods();
	});
	data_builder.get_hot_goods();
	$(window).scroll(function() { //内容懒加载
		if ($(document).height() - 50 <= parseFloat($(window).height()) + parseFloat($(window).scrollTop()) && global_current_page < global_total_page) {
			if ($(".modal-loading").is(":hidden")) {
				data_builder.get_hot_goods();
			}

		}
	});
	$(".btn_cate3").click(function() {
		data_builder.init();
		param.goods_cat_id = $(this).attr("data_cat_id");
		data_builder.get_hot_goods();
		$("#nav_cate").html($(this).html());
	});
	$("#btn_sort li").click(function() {
		data_builder.init();
		param.sort = $(this).find("a").attr("data-sort");
		data_builder.get_hot_goods();
	});

	$("#nav_cate").click(function() {
		$(".am-tabs-nav li").eq(0).addClass("am-active");
		$(".am-tabs-nav li").eq(1).removeClass("am-active");
		$(".data-tab-panel-1").removeClass("am-in").removeClass("am-active").hide();
		if ($(".data-tab-panel-0").is(":hidden")) {
			$(".data-tab-panel-0").addClass("am-in").addClass("am-active").show();

		} else {
			$(".data-tab-panel-0").removeClass("am-in").removeClass("am-active").hide();
		}
	});
	$("#nav_sort").click(function() {
		$(".am-tabs-nav li").eq(1).addClass("am-active");
		$(".am-tabs-nav li").eq(0).removeClass("am-active");
		$(".data-tab-panel-0").removeClass("am-in").removeClass("am-active").hide();
		if ($(".data-tab-panel-1").is(":hidden")) {
			$(".data-tab-panel-1").addClass("am-in").addClass("am-active").show();
		} else {
			$(".data-tab-panel-1").removeClass("am-in").removeClass("am-active").hide();
		}
	});


});
var global_current_page = 0;
var global_total_page = 0;

var data_builder = {
	init: function() {
		$(".goods_list").html("");
		global_current_page = 0;
		global_total_page = 0;
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
	get_hot_goods: function() {
		$(".modal-loading").show();
		global_current_page++;
		param.page = global_current_page;
		$.ajax({
			type: "get",
			url: mobile_url + "market/get_goods_list",
			data: param,
			success: function(data) {
				global_total_page = data.data.retHeader.totalpg;
				if (data.success == true) {
					var str_html = $(".goods_list").html();
					var list = data.data.reqdata;
					for (var i = 0; i < list.length; i++) {
						var item = list[i];
						str_html += '<li>';
						str_html += '<div class="am-g">';
						str_html += '<div class="am-u-sm-4" style="padding-right: 0;">';
						str_html += '<div class="goods_imgbox">';
						str_html += '<img src=' + item.goods_image_url + ' width="100%" class="goods_img">';
						str_html += '</div>';
						str_html += '</div>';
						str_html += '<div class="am-u-sm-8" style="padding-left:0.5rem;">';
						str_html += '<div class="goods_tit">' + item.goods_name + '</div>';
						str_html += '<div class="goods_attr">';
						str_html += '<span class="agent_price_tit">价格</span><span class="agent_price am-text-danger">￥' + item.goods_price + '</span>';
						if(item.commission_seller_different==0){
						str_html += '&nbsp;&nbsp;<span class="agent_price_tit">佣金</span><span class="agent_price am-text-danger">￥' + item.commission_fee + '</span>';
						}else{
						str_html += '&nbsp;&nbsp;<span '+member_class_stype0+'><span class="agent_price_tit">普通佣金</span><span class="agent_price am-text-danger">￥' + item.commission_fee_array.commission_seller_free_fee  + '</span></span><br>';
						
						str_html += '<span '+member_class_stype1+'><span class="agent_price_tit">vip佣金</span><span class="agent_price am-text-danger">￥' + item.commission_fee_array.commission_seller_vip_fee + '</span></span>';
						str_html += '&nbsp;&nbsp;<span '+member_class_stype2+'><span class="agent_price_tit">svip佣金</span><span class="agent_price am-text-danger">￥' +  item.commission_fee_array.commission_seller_svip_fee + '</span></span>';
						}
						str_html += '</div>';
						str_html += '<div class="goods_btn am-g am-padding-top-xs">';
						str_html += '<div class="am-u-sm-6">';
						str_html += '<a href="' + mobile_url + 'market/goods_detail?id=' + item.goods_id + '" class="am-btn am-btn-default am-btn-block am-radius am-btn-sm">查看详情</a>';
						str_html += '</div>';
						str_html += '<div class="am-u-sm-6">';
						str_html += '<a class="am-btn am-btn-danger am-btn-block am-radius am-btn-sm btn_create am-btn-danger" data_status="' + item.wholesale + '" data_goods_id="' + item.goods_id + '">' + ((item.wholesale == true) ? '立即下架' : '立即上架') + '</a>';
						str_html += '</div>';
						str_html += '</div>';
						str_html += '</div>';
						str_html += '</div>';
						str_html += '</li>';
					}
					$(".goods_list").html(str_html);
					$(".modal-loading").hide();
					data_builder.good_create();


				}
			}

		});

	}
}