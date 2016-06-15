$(function() {
	goods.init();
	if (gloabl_shop_info.goods_show_type == 1) {
		goods.get_All_list();
		$("#top_i_title_p").html("最新上架");
	} else {
		shops.is_shop_collect();
	}
	//goods.get_category_list();


	$(window).scroll(function() { //内容懒加载
		if (gloabl_shop_info.goods_show_type == 1) {
			if ($(document).height() <= parseFloat($(window).height()) + parseFloat($(window).scrollTop()) && gloabl_category.current_index < gloabl_category.totalpage) {				
				goods.get_All_list();
			}	        
		} else {
			if ($(document).height() <= parseFloat($(window).height()) + parseFloat($(window).scrollTop()) && gloabl_category.current_index < gloabl_category.cat_count) {
				goods.get_item_list();
			}
		}
	});

	$(".contact").click(function() {
		if ($(".contact_div").is(":hidden")) {
			$(".contact_div").show();
		} else {
			$(".contact_div").hide();
		}


	});


	$('.classify').click(function() {
		$("#classifyPanel").fadeIn(300, function() {
			if ($(this).hasClass("hide_panel")) {
				$(this).removeClass("hide_panel")
			}
			$(this).addClass('show_panel');
		});
	});
    

	$('.footer_cart').click(function() {
        location.href = mobile_url + "order/cart";
	});    
    
	$('.footer_order').click(function() {
        location.href = mobile_url + "member/order";
	});
    
	$('.mask_area').click(function() {
		$("#classifyPanel").removeClass('show_panel').addClass('hide_panel').fadeOut(300);
	});
	$(".store").click(function() {
		if ($(this).hasClass("stored")) {
			shops.collect_shop_delete();
		} else {
			shops.collect_shop_save();
		}
	});
	$('#search_btn').click(function(){
		var query_string = $('#query').val();
		if ( query_string != '' ) {
			window.location.href = mobile_url+"shop/goodslist?id=" + gloabl_shop_info.shop_id + "&query=" + query_string;
		}
	});
	$('#index_search_btn').click(function(){
		var query_string = $('#search_query').val();
		if ( query_string != '' ) {
			window.location.href = mobile_url+"shop/goodslist?id=" + gloabl_shop_info.shop_id + "&query=" + query_string;
		}
	});
});

var gloabl_category = {
	category_list: [],
	cat_count: 1,
	current_index: 0,
	totalpage: 1
};
var shops = {
	is_shop_collect: function() {
		$.ajax({
			type: "get",
			url: "/member/collect.check_shop_collect",
			data: {
				item_uid: gloabl_shop_info.shop_id
			},
			cache: false,
			success: function(data) {
				if (data.success) {
					if (data.data == true) {
						$(".store").addClass("stored");
					}
				}
			}
		});
	},
	collect_shop_save: function() {
		$(".store_tip").hide();
		$(".stored_tip").show();
		$.ajax({
			url: mobile_url + 'member/collect.shop_save',
			type: 'post',
			dataType: 'json',
			data: {
				item_uid: gloabl_shop_info.shop_id
			},
			cache: false,
			success: function(data) {
				if (data.success) {
					if (data.data == true) {
						$(".store").addClass("stored");
						alert("收藏成功");
					} else {
						alert("系统繁忙，请稍后再试");
					}
				} else {
					alert(data.message);
				}
			}
		});
	},
	collect_shop_delete: function() {
		$(".store_tip").show();
		$(".stored_tip").hide();
		$.ajax({
			url: mobile_url + 'member/collect.shop_delete',
			type: 'post',
			dataType: 'json',
			data: {
				item_uid: gloabl_shop_info.shop_id
			},
			cache: false,
			success: function(data) {
				if (data.success) {
					if (data.data == true) {
						$(".store").removeClass("stored");
						alert("成功取消收藏");
					} else {
						alert("系统繁忙，请稍后再试");
					}
				} else {
					alert(data.message);
				}
			}
		});
	}

}


//产品查询类
var goods = {
	init: function() {
		$.template('cate_template', this._cate_template);
		$.template('item_template', this._item_template);
		$.template('cate_menu_template', this._cate_menu_template);
		$.template('item_list_template', this._item_list_template);
		if (gloabl_shop_info.goods_show_type == 2) {
			this.get_recommend_list();
			this.get_category_list(this.get_item_list);
		}else{
			this.get_category_list();
		}
		this.show_cart();
		$("#scroll_loading_txt").show();
	},
	show_cart: function() {
		$(".cart_btn").click(function() { //购物车
			location.href = '/order/cart';
		});
		$.ajax({
			url: mobile_url + '/order/get_cart_count',
			type: 'GET',
			dataType: 'json',
			cache: false,
			success: function(data) {
				if (data.success) {
					if (parseInt(data.data) > 0) {
						$(".cart_btn").addClass('active');
					} else {
						$(".cart_btn").removeClass('active');
					}
				} else {
					alert(data.message);
				}
			}
		});
	},
	get_category_list: function(callback) {
		var _this = this;
		var id = gloabl_shop_info.shop_id;
		var url = mobile_url + 'shop/get_category_list';
		$.ajax({
			url: url,
			type: 'GET',
			dataType: 'json',
			data: {
				id: id
			},
			cache: false,
			success: function(data) {
				if (data.success) {
					var cate_menu = '';
					for (var i = 0; i < data.data.length; i++) {
						var cate = data.data[i];
						cate.totalpage = _this._get_totalpage(_this.item_pagesize, cate.item_count);
						cate.current_page = 0;
						gloabl_category.category_list.push(cate);
						$.tmpl('cate_template', cate).appendTo('#hot_items');
						//$.tmpl('cate_menu_template', cate).appendTo('#cate_menu');
						cate_menu += '<li><em></em><span class="cat_click" data-name="'+cate.cat_name+'" data-id="'+cate.goods_cat_id+'">'+cate.cat_name+'</span><span class="fold" data-id="'+cate.goods_cat_id+'"><img src="'+base_v+'image/add.gif" title="展开/收缩"></span></li>';
						cate_menu += '<ul id="ul_parent_'+cate.goods_cat_id+'" class="hide">';
						var cat_son_length = cate.cat_son.length;
						for (var j = 0; j < cat_son_length; j++) {
							var cate_son = cate.cat_son[j];
							cate_menu += '<li><em></em><span class="cat_click" data-name="'+cate_son.cat_name+'" data-id="'+cate_son.goods_cat_id+'">'+cate_son.cat_name+'</span></li>';
						}
						cate_menu += '</ul>';

					}
					$('#cate_menu').html(cate_menu);
					$("#cate_menu li span.cat_click").click(function() {
						window.location.href = mobile_url+"shop/goodslist?id=" + gloabl_shop_info.shop_id + "&goods_cat_id=" + $(this).attr("data-id");
					});
					$("#cate_menu li span.fold").click(function() {
						var goods_cat_id = $(this).attr('data-id');
						if ( $('#ul_parent_'+goods_cat_id ).hasClass('hide') ) {
							$('#ul_parent_'+goods_cat_id ).removeClass('hide');
							$(this).children('img').attr('src',base_v+'image/desc.gif');
						} else {
							$('#ul_parent_'+goods_cat_id ).addClass('hide');
							$(this).children('img').attr('src',base_v+'image/add.gif');
						}
					});	
					gloabl_category.cat_count = gloabl_category.category_list.length;
					//console.log(gloabl_category);
					if ($.isFunction(callback)) {
						callback();
					}
				} else {
					alert(data.message);
				}
			}
		});
	},
	item_pagesize: pagesize,
	get_recommend_list: function() {
		var _this = this;
		$("#scroll_loading_txt").show();
		$.ajax({
			url: mobile_url + '/shop/get_item_list',
			type: 'GET',
			dataType: 'json',
			data: {
				id: gloabl_shop_info.shop_id,
				recommend: 1
			},
			cache: false,
			success: function(data) {
				if (data.success) {
					$.tmpl('item_template', data.data.reqdata).appendTo('#top_ul');
					$("#recommend_wrap").show()
				} else {
					alert(data.message);
				}
				$("#scroll_loading_txt").hide();
			}
		});
	},
	get_item_list: function() {
		var _this = this;
		var id = gloabl_shop_info.shop_id;
		if (gloabl_category.current_index < gloabl_category.cat_count) {
			var category = gloabl_category.category_list[gloabl_category.current_index];
			category.current_page++;
			if (category.current_page <= category.totalpage && category.item_count > 0) {
				$("#scroll_loading_txt").show();
				$.ajax({
					url: mobile_url + 'shop/get_item_list',
					type: 'GET',
					dataType: 'json',
					data: {
						id: id,
						goods_cat_id: category.goods_cat_id,
						page: category.current_page,
						pagesize: _this.item_pagesize
					},
					cache: false,
					success: function(data) {
						if (data.success) {
							if (data.data.reqdata.length > 0) {
								$.tmpl('item_template', data.data.reqdata).appendTo('#hot_ul_' + category.goods_cat_id);
								$('#i_wrap_' + category.goods_cat_id).show();
							} else {
								goods.get_item_list();
							}
						} else {
							alert(data.message);
						}
						
					}
				});
			} else {
				gloabl_category.current_index++;
				goods.get_item_list();
			}
		}
	},
	_get_totalpage: function(pagesize, count) {

		if (count <= pagesize) {
			return 1;
		} else {
			return parseInt(count / pagesize) + (count % pagesize == 0 ? 0 : 1);
		}
	},
	get_All_list: function() {		
		gloabl_category.current_index++;
		var title = '银品惠';
		var url = mobile_url+'shop/46?p='+gloabl_category.current_index;
		var state = {title:title,url:url};		
		if ( gloabl_category.current_index == 1 ) {
			//history.pushState(state, title, url);			
		} else {
			gloabl_category.current_index = gloabl_category.current_index < p ? p : gloabl_category.current_index;
			var url = mobile_url+'shop/46?p='+gloabl_category.current_index;
			var state = {title:title,url:url};		
			history.replaceState(state, title, url);
		}					
		var _this = this;		
		$("#recommend_wrap").show();		
		$.ajax({
			url: mobile_url + 'shop/get_item_list',
			type: 'GET',
			dataType: 'json',
			data: {
				id: gloabl_shop_info.shop_id,
				page: gloabl_category.current_index,
				pagesize: gloabl_category.current_index>1 ? 10 : _this.item_pagesize
			},
			cache: false,			
			success: function(data) {				
				if (data.success) {
					gloabl_category.totalpage = data.data.retHeader.totalpg;
					$.tmpl('item_list_template', data.data.reqdata).appendTo('#top_ul');					
					goods._bindAhref();
					//console.log(gloabl_category.current_index);
					if ( gloabl_category.current_index == 1 ) {
						//location.hash="li_"+last_id;		
						if ( y > 0 ) {
							$("html,body").animate({scrollTop: y}, 300);						
						}
						if ( p > 1 ) {
							gloabl_category.current_index = p;
						}						


					}
					if (gloabl_category.current_index >= gloabl_category.totalpage) {    
						$("#scroll_loading_txt").hide();
						return true;
					}
				} else {
					alert(data.message);
				}				
			}
		});					
	},
	_bindAhref: function(){		
		$('#top_ul a').bind('click',function(){
			var goods_id = $(this).attr('data-id');
			//console.log(goods_id);
			var top = $(document).scrollTop();//$(this).offset().top;
			//console.log(top);
			var title = '银品惠';
			var url = mobile_url+'shop/46?p='+gloabl_category.current_index+'&y='+top;
			var state = {title:title,url:url};		
			history.replaceState(state, title, url);		
			return true;
		});
	},
	_loading: $("#scroll_loading_txt"),
	_cate_template: '' +
		'<div class="i_wrap margin_auto rel" id="i_wrap_${goods_cat_id}" style="display: none;">' +
		'  <h3 class="i_title abs"><p class="i_title_p over_hidden ellipsis">${cat_name}</p></h3>' +
		'  <ul class="i_ul rel" id="hot_ul_${goods_cat_id}">' +
		'  </ul>' +
		'  <div class="clear"></div>' +
		'  <div class="i_list_bottom"></div>' +
		'</div>',
	_item_template: '' +
		'<li class="i_li left">' +
		'    <a href="'+mobile_url+'goods/${goods_id}.html"><img src="' + base_v + 'image/placeholder_list.png">' +
		'      <div class="i_li_img_div abs wrap">' +
		'        <div class="i_li_img_div_inner"><span class="sellerOut"><img src="${goods_image_url}"></span></div>' +
		'      </div>' +
		'      <p class="i_txt">${item_name}</p>' +
		'      <p class="i_pri_wrap"><span class="i_pri">¥${item_price}</span></p>' +
		'    </a>' +
		'</li>',
	_cate_menu_template: '' +
		'<li data-name="${cat_name}" data-id="${goods_cat_id}"><em></em><span>${cat_name}</span></li>',
	_item_list_template: '' +
		'<li class="i_li left" id="li_${goods_id}">' +
		'    <a href="'+mobile_url+'goods/${goods_id}.html" data-id="${goods_id}"><img src="' + base_v + 'image/placeholder_list.png">' +
		'      <div class="i_li_img_div abs wrap">' +
		'        <div class="i_li_img_div_inner"><span class="sellerOut"><img src="${goods_image_url}"></span></div>' +
		'      </div>' +
		'      <p class="i_txt">${item_name}</p>' +
		'      <p class="i_pri_wrap">'+
		'			<span class="i_pri">¥${item_price}</span>'+
		'			{{if price_source != item_price}}' +
		'			<span class="i_pri"><del>¥${price_source}</del></span>'+
		'			{{/if}}' +
		'		</p>' +
		'    </a>' +
		'</li>'
}