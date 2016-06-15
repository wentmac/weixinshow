var param = {

};
$(function() {
	data_builder.init();
	param.goods_cat_id = goods_cat_id;
	data_builder.get_list();

	$(window).scroll(function() { //内容懒加载
		if ($(document).height() - 50 <= parseFloat($(window).height()) + parseFloat($(window).scrollTop()) && global_current_page < global_total_page) {

			if ($(".modal-loading").is(":hidden")) {

				data_builder.get_list();
			}
		}
	});
	$("#tab_nav a").click(function() {
		$("#tab_nav a").removeClass("active");
		$(this).addClass("active");
		param.goods_cat_id = $(this).attr("data_id");
		data_builder.init();
		data_builder.get_list();
	});
});
var global_current_page = 0;
var global_total_page = 0;
var data_builder = {
	init: function() {
		global_current_page = 0;
		global_total_page = 0;
		param.is_cloud_product = is_cloud_product;
		$("#goods-list").html("");
	},
	good_create: function() {
		$(".add").bind("click", function() {
			var _this = this;
			$.ajax({
				type: "post",
				url: mobile_url + "market/create",
				data: {
					goods_id: $(_this).attr("data_goods_id")
				},
				success: function(data) {
					if (data.success == true) {
						$(_this).attr("data_status", "true");
						$(_this).removeClass("add").addClass("added");
						
					} else {
						alert(data.message);
					}
				}
			});
		});
	},
	get_list: function() {
		global_current_page++;
		param.page = global_current_page;
		$(".modal-loading").show();
		if (goods_category_more == 1) {
			$.ajax({
				type: "get",
				url: mobile_url + "market/get_goods_list",
				data: param,
				success: function(data) {
					if (data.success == true) {
						global_total_page = data.data.retHeader.totalpg;
						var list = data.data.reqdata;
						var str_html = $("#goods-list").html();
						str_html += '<div class="js-list clearfix">';
						for (var i = 0; i < list.length; i++) {
							var item = list[i];
							str_html += '<div class="block-item name-card js-goto-detail" data-alias="pfosrbl9" data-seller_goods_alias="" data-average_profit="" data-kdt_goods_id="78572842">';
							str_html += '		<div class="thumb">';
							str_html += '			<a href="' + mobile_url + 'market/goods_detail?id=' + item.goods_id + '"><img src=' + item.goods_image_url + ' class="js-lazy"></a>';
							str_html += '		</div>';
							str_html += '		<div class="detail clearfix">';
							str_html += '			<h3 class="ellipsis"><a href="' + mobile_url + 'market/goods_detail?id=' + item.goods_id + '">' + item.goods_name + '</a></h3>';
							str_html += '			<p class="c-gray">库存' + item.goods_stock + '件，'+item.seller_count+'人正在卖</p>';
							str_html += '			<p class="c-gray">价格：￥' + item.goods_price + '</p>';
							if(item.commission_seller_different==0){
							str_html += '			<p class="c-gray-dark">佣金：<span class="c-red">￥' + item.commission_fee + '</span> / 件</p>';
							}else{
								str_html += '			<p '+member_class_stype0+' class="c-gray-dark">佣金：<span class="c-red">￥' + item.commission_fee_array.commission_seller_free_fee + '</span> / 件</p>';
								str_html += '			<p '+member_class_stype1+' class="c-gray-dark">佣金：<span class="c-red">￥' + item.commission_fee_array.commission_seller_vip_fee + '</span> / 件</p>';
								str_html += '			<p '+member_class_stype2+' class="c-gray-dark">佣金：<span class="c-red">￥' + item.commission_fee_array.commission_seller_svip_fee + '</span> / 件</p>';
								
							}
							str_html += '		</div>';
							if (item.wholesale == true) {
								str_html += '	<a href="javascript:;" class="opt js-opt added"></a>';
							} else {
								str_html += '	<a href="javascript:;" data_goods_id="'+item.goods_id+'" class="opt js-opt add"></a>';
							}
							str_html += '</div>';
						}
						str_html += '</div>';

						$("#goods-list").html(str_html);
						$(".modal-loading").hide();
						data_builder.good_create();
					}
				}
			});
		} else {
			$.ajax({
				type: "get",
				url: mobile_url + "market/get_goods_more_pic_list",
				data: param,
				success: function(data) {
					if (data.success == true) {
						var list = data.data.reqdata;

						global_total_page = data.data.retHeader.totalpg;

						var str_html = $("#goods-list").html();
						for (var i = 0; i < list.length; i++) {
							var item = list[i];
							str_html += '	<div class="js-list clearfix">';
							str_html += '		<div class="block-item name-card js-goto-detail" data-alias="1esmhlwe3" data-seller_goods_alias="" data-average_profit="" data-kdt_goods_id="2536015">';
							str_html += '			<h3 class="ellipsis header"><a href="' + mobile_url + 'market/goods_detail?id=' + item.goods_id + '">' + item.goods_name + '</a></h3>';
							str_html += '			<div class="clearfix extra">';
							str_html += '				<span class="icon icon-cheng"></span>';
							str_html += '				<span class="icon icon-qi"></span>';
							str_html += '				<span class="icon icon-bao"></span>';
							str_html += '				<p class="c-gray-dark pull-right">';
							str_html += '					<span>' + item.seller_count + '</span>人正在卖';
							str_html += '				</p>';
							str_html += '			</div>';
							str_html += '			<ul class="clearfix">';
							var arr = item.goods_image_urls;
							var arr_length = arr.length;
							if (arr.length >= 3) {
								arr_length = 3;
							}
							for (var j = 0; j < arr_length; j++) {
								str_html += '				<li>';
								str_html += '					<a href="' + mobile_url + 'market/goods_detail?id=' + item.goods_id + '"><img class="js-lazy" src="' + arr[j] + '" style=""></a>';
								str_html += '				</li>';
							}
							str_html += '			</ul>';
							str_html += '			<p class="c-gray-darker desc js-desc">' + item.goods_brief + '</p>';
							str_html += '			<dl class="c-gray-dark">';
							str_html += '				<dt>价格：</dt>';
							str_html += '				<dd>￥' + item.goods_price + '</dd>';
							str_html += '			</dl>';
							str_html += '			<dl>';
							str_html += '				<dt>佣金：</dt>';
							str_html += '				<dd><span class="c-red">￥' + item.commission_fee + '</span> / 件</dd>';
							str_html += '			</dl>';
							if (item.wholesale == true) {
								str_html += '	<a href="javascript:;" class="opt js-opt added"></a>';
							} else {
								str_html += '	<a href="javascript:;" class="opt js-opt add" data_goods_id="'+item.goods_id+'" ></a>';
							}
							str_html += '</div>';
							str_html += '		</div>';
							str_html += '	</div>';
						}
						$("#goods-list").html(str_html);
						$(".modal-loading").hide();
						data_builder.good_create();
					}

				}
			});
		}

	}
}