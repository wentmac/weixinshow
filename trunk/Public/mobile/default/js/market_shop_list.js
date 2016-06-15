var param = {

};
$(function() {
	$("#btn_search").click(function() {
		if ($("#txt_search").val() != "") {
			data_builder.init();
			param.query_string = $("#txt_search").val();
			data_builder.data();
		}
	});
	$("#header_nav li").click(function() {
		param.sort = $(this).attr("data_sort");
		data_builder.init();
		data_builder.data();
		$("#header_nav li").removeClass("cur");
		$(this).addClass("cur");
	});
	data_builder.init();
	data_builder.data();

	$(window).scroll(function() { //内容懒加载
		if ($(document).height() - 50 <= parseFloat($(window).height()) + parseFloat($(window).scrollTop()) && global_current_page < global_total_page) {
			if ($(".modal-loading").is(":hidden")) {
				data_builder.data();
			}
		}
	});
});
var global_current_page = 0;
var data_builder = {
	init: function() {
		global_current_page = 0;
		global_total_page = 0;
		$("#body_html").html("");
		$("#big_gray").show();
		$("#my-modal-loading").show();
	},
	data: function() {
		$(".modal-loading").show();
		global_current_page++;
		param.page = global_current_page;
		$.ajax({
			type: "get",
			url: mobile_url + "market/get_shop_list",
			data: param,
			success: function(data) {				
				if (data.success == true) {

					global_total_page = data.data.retHeader.totalpg;

					var list = data.data.reqdata;					
					var shop_list_html = $("#body_html").html();		
					var have_goods_count = 0;
					for (var i = 0; i < list.length; i++) {

						item = list[i];						
						if (item.shop_goods_array.length > 0) {
							shop_list_html += '<section class="shop_list am-u-lg-12 am-padding-xs am-margin-top-sm  bg_white">';
							shop_list_html += '		<div class="div_shop_list am-margin-xs">';
							shop_list_html += '			<p style="height: 43px; line-height: 43px;" class="am-margin-bottom-xs">';
							shop_list_html += '				<img class="shop_logo am-u-sm-2 am-padding-0" src="' + item.shop_image_url + '" />';
							shop_list_html += '				<span class="am-u-sm-5 am-padding-0 am-text-middle am-margin-left-xs overflow_no"><a style="color:#202020" href=' + mobile_url + 'market/shop_home?id=' + item.uid + '>';
							if (item.shop_name.length > 10) {
								shop_list_html += item.shop_name.substr(0, 8) + '...';
							} else {
								shop_list_html += item.shop_name;
							}
							shop_list_html += ' </span>';
							shop_list_html += '				<span class="am-u-sm-4 am-fl am-text-right am-block am-padding-right-xs am-margin-0 collect_count">' + item.seller_count + '人在卖</span>';
							shop_list_html += '			</p>';
							shop_list_html += '			<hr class="am-margin-xs am-margin-top-sm">';
							shop_list_html += '		<div class="am-u-sm-12 am-margin-0 am-padding-xs am-margin-bottom-sm ">';

							var goods_list = item.shop_goods_array;
							for (var j = 0; j < goods_list.length; j++) {
								var goods_item = goods_list[j];
								shop_list_html += '<a href=' + mobile_url + 'market/goods_detail?id=' + goods_item.goods_id + '><img class="am-u-sm-4 am-padding-xs" src="' + goods_item.goods_image_url + '" /></a>';
							}
							shop_list_html += '		</div>';
							shop_list_html += ' 	</div>';
							shop_list_html += '</section >';
							
							have_goods_count++;
						}
					}			
					$("#body_html").html(shop_list_html);
					$(".modal-loading").hide();
					
					if ( have_goods_count <= 1 ) {
						data_builder.data();
					}
				} else {
					alert(data.message);
				}
			}
		});
	},
}