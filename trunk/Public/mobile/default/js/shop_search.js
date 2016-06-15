$(function() {
	cate.init();
	cate.get_category_list();
	
    $(window).scroll(function() { //内容懒加载
        if ($(document).height() - 50 <= parseFloat($(window).height()) + parseFloat($(window).scrollTop()) && global_crrent_page < global_total_page) {
            cate.get_item_list();
        }
    });
	$("#search_button").click(function() {
		$("#shop_classes_wrap").hide();
		$("#search-content").removeClass("hide");
		search_keyword = $("#tb_search2").val();
		if (search_keyword != "") {
			global_crrent_page = 0
			$("#hot_ul").html("");
			cate.get_item_list();
		} else {
			alert("请输入搜索关键词");
		}
		//$("#hot_ul").html($("#hot_ul").html() + "<li id='search_more'><a href='/plaza/searchAll.html?tb_search=1&amp;type=item' id='search_more_a' class='for_gaq btncancel' data-for-gaq='搜索更多银品惠商品'>搜索更多银品惠商品</a></li>")
	});
});

var search_keyword = "";
var global_crrent_page = 0;
var global_total_page = 1;
var cate = {
	init: function() {
		$.template('cate_template', this._cate_template);
		$.template('item_template', this._item_template);
		this.get_category_list();
	},
	item_pagesize: 3,
	get_item_list: function() {
		var _this = this;
		global_crrent_page++;

		if (global_crrent_page <= global_total_page) {
			var id = gloabl_shop_info.shop_id;
			$("#scroll_loading_txt").show();
			$.ajax({
				url: mobile_url + '/shop/get_item_list',
				type: 'GET',
				dataType: 'json',
				data: {
					id: id,
					query: search_keyword,
					pagesize: _this.item_pagesize,
					page: global_crrent_page,
					r: Math.random()
				},
				cache:false,
				success: function(data) {
					if (data.success == true) {
						var list = data.data.reqdata;
						global_total_page = data.data.retHeader.totalpg;
						$.tmpl('item_template', list).appendTo('#hot_ul');
					} else {
						alert(data.message);
					}
					$("#scroll_loading_txt").hide();
				}

			});
		}
	},
	get_category_list: function() {
		var _this = this;
		var id = gloabl_shop_info.shop_id;
		var url = mobile_url + '/shop/get_category_list';
		$.ajax({
			url: url,
			type: 'GET',
			dataType: 'json',
			data: {
				id: id
			},
			cache:false,
			success: function(data) {
				if (data.success) {
					var cate = data.data;
					$.tmpl('cate_template', cate).appendTo('#shop_classes_ul');
					$("#shop_classes_ul li").click(function() {
						window.location.href = "/shop/itemlist?id=" + gloabl_shop_info.shop_id + "&item_cat_id=" + $(this).attr("data-id");
					});
				} else {
					alert(data.message);
				}
			}
		});

	},
	_cate_template: '' +
		'<li class="shop_classes_li rel">' +
		'<a class="block over_hidden ellipsis" href="/shop/itemlist?id=${gloabl_shop_info.shop_id}&item_cat_id=${item_cat_id}">${cat_name}</a>' +
		'</li>',
	_item_template: '' +
		'<li class="i_li left">' +
		'<a href="/item/${item_id}.html">' +
		'	<img src="' + base_v + 'v1/images/placeholder_list.png">' +
		'	<div class="i_li_img_div abs wrap">' +
		'		<div class="i_li_img_div_inner"><span class="sellerOut"><img src="${goods_image_url}"></span>' +
		'		</div>' +
		'	</div>' +
		'	<p class="i_txt">${item_name}</p>' +
		'	<p class="i_pri_wrap"><span class="i_pri">¥${item_price}</span>' +
		'	</p>' +
		'	<p class="i_li_bottom abs"><em class="i_li_soldOut">${sales_volume}</em>' +
		'	</p>' +
		'</a>' +
		'</li>'

}