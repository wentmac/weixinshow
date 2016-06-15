var global_has_sku = global_goods_sku_array.length == 0 || Object.getOwnPropertyNames(global_goods_sku_array).length == 0 ? false : true;

if (global_has_sku) {
	var array_price = new Array();
	for (var item in global_goods_sku_array) {
		var sku = global_goods_sku_array[item];
		array_price.push(sku.price);
	}
	array_price.sort(function(a, b) {
		return a - b;
	});
	//console.log(array_price);
	var first_price = array_price[0];
	var last_price = array_price[array_price.length - 1];
	var show_price = "";
	if (first_price == last_price) {
		show_price = "￥" + first_price;
	} else {
		show_price = '￥' + first_price + ' - ￥' + last_price;
		$("#item_price,#select_sku_price").text(show_price);
	}
} else {
	var show_price = '￥' + gloabl_item_price;
	$("#item_price,#select_sku_price").text(show_price);
}


$(function() {
	/*
      $("#add_cart").click(function(){
          $("#btn_add2cart").show();
          $("#btn_buynow").hide();
      });
      $("#buy_now").click(function(){
          $("#btn_add2cart").hide();
          $("#btn_buynow").show();
      });
      $("#btn_select_sku").click(function(){
          $("#btn_add2cart,#btn_buynow").show();
      });
  */


	//延时加载图片
/*
	$("window").scroll(function() {
		$("img[data-lazyload]").lazyload({ threshold: 200, failurelimit: 100 });
	});
	$("img[data-lazyload]").lazyload({ threshold: 200, failurelimit: 100 });
*/



	$.each($('img[data-lazyload]'), function(i, n) {
		if ($(n).attr('src') != '') {
			var src = $(n).attr('data-lazyload');
			$(n).attr('src', src);
			//			var db=$(window).width()/$(n).attr('width');
			$(n).css('width', '100%');
			//			$(n).attr('height',$(n).attr('height')*db);
		}
	});

	collect.is_item_collect();


	$(".store").click(function() {
		if ($(this).hasClass("stored")) {
			collect.item_delete();
		} else {
			collect.item_save();
		}

	});



	$(".cart_btn").click(function() { //购物车
		location.href = mobile_url+'order/cart';
	});
	$.ajax({
		url: mobile_url + 'order/get_cart_count',
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
	$("#add_cart,#buy_now,#btn_select_sku").click(function() {
		sku_tool.init();
		$('#select_sku').modal({
			closeViaDimmer: 0,
			width: $(window).width() > 640 ? 640 : $(window).width()
		});
		return false;
	});
});
var collect = {
	is_item_collect: function() {
		$.ajax({
			type: "get",
			url: "/member/collect.check_item_collect",
			data: {
				item_id: gloabl_item_id
			},
			cache: false,
			success: function(data) {
				if (data.success) {
					if (data.data) {
						$(".store").addClass("stored");
					}
				}
			}
		});
	},
	item_save: function() {
		if ($.cookie("mobile") != null) {
			$(".store_tip").hide();
			$(".stored_tip").show();
			$.ajax({
				url: mobile_url + '/member/collect.item_save',
				type: 'post',
				dataType: 'json',
				data: {
					item_id: gloabl_item_id
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
		} else {
			location.href = mobile_url + "member/home";
		}
	},
	item_delete: function() {
		if ($.cookie("mobile") != null) {
			$(".store_tip").show();
			$(".stored_tip").hide();
			$.ajax({
				url: mobile_url + '/member/collect.item_delete',
				type: 'post',
				dataType: 'json',
				data: {
					item_id: gloabl_item_id
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
		} else {
			location.href = mobile_url + "member/home";
		}
	}
}

//SKU展示
var sku_tool = {
	init: function() {
		if (global_sku_init == 0) {
			if (global_has_sku) {
				for (var spec_i in global_goods_spec_array) {
					var spec_id = spec_i;
					var ary_spec_item = global_goods_spec_array[spec_i];
					var spec_name = ary_spec_item[0].spec_name;
					this.add_spec_item(spec_id, spec_name); //增加一个spec_item
					for (var i = 0; i < ary_spec_item.length; i++) {
						var spec_item = ary_spec_item[i];
						this.add_spec_list(spec_id, spec_item);
					}
				}
				this.bind_sku_btnclick();
			} else {
				$("#select_sku_num").show(100);
				$("#sku_stock").text('库存:' + gloabl_item_stock + "件").show();
				if (gloabl_item_stock > 0) {
					$("#buy_num").val(1);
					$("#select_sku_price").text('￥' + gloabl_item_price);
				}
			}
			global_sku_init = 1;
			this.bind_sotck_btn();
			this.bind_buy_btn();
		}
	},
	add_spec_item: function(id, name) {
		var tmp = '';
		tmp += '<div class="select_sku_item" valueid="[id]" id="spec_item_[id]">';
		tmp += '  <div class="sku_item_tit">[name]：</div>';
		tmp += '  <div class="sku_item_list"></div>';
		tmp += '  <hr>';
		tmp += '</div>';
		tmp = tmp.replace(new RegExp(/\[id\]/g), id);
		tmp = tmp.replace('[name]', name);
		$("#select_sku_item_list").append(tmp);
	},
	add_spec_list: function(specid, item) {
		var tmp = '<a href="#" issel="0" class="am-btn am-btn-default am-radius am-btn-sm" valueid="[id]" id="spec_valueid_[id]">[name]</a>';
		tmp = tmp.replace(new RegExp(/\[id\]/g), item.spec_value_id);
		tmp = tmp.replace('[name]', item.spec_value_name);
		$("#spec_item_" + specid + ">.sku_item_list").append(tmp);
	},
	bind_sku_btnclick: function() {
		var that = this;
		$(".sku_item_list").delegate('a', 'click', function() {
			var valueid = $(this).attr('valueid');
			var specid = $(this).parents('select_sku_item').attr('valueid');

			//样式部分
			if ($(this).attr('issel') == 0) {
				$(this).parent().find('a').removeClass('am-btn-danger').addClass('am-btn-default').attr('issel', 0);
				$(this).removeClass('am-btn-default').addClass('am-btn-danger');
			}
			$(this).attr('issel', 1);

			that._union_sku();
			return false;
		});
	},
	bind_sotck_btn: function() {
		if ( global_goods_type == 2 ) {
			//会员商品 一次只让买一个
			return true;
		}
		$("#btn_stock_minus").click(function() {
			var num = parseInt($("#buy_num").val());
			var price = 0.00;
			if (global_has_sku) {
				price = global_sku_current_obj.price;
			} else {
				price = gloabl_item_price;
			}
			if (num > 1) {
				var select_sku_price = new Number(parseFloat(price * (num - 1)));				
				$("#buy_num").val(num - 1);
				$("#select_sku_price").text('￥' + select_sku_price.toFixed(2));
			}
			return false;
		});
		$("#btn_stock_add").click(function() {
			var num = parseInt($("#buy_num").val());
			var price = 0.00;
			if (global_has_sku) {
				price = global_sku_current_obj.price;
			} else {
				price = gloabl_item_price;
			}
			var stock = 0;
			if (global_has_sku) {
				stock = global_sku_current_obj.stock;
			} else {
				stock = gloabl_item_stock;
			}
			if (num < stock) {
				var select_sku_price = new Number(parseFloat(price * (num + 1)));				
				$("#buy_num").val(num + 1);
				$("#select_sku_price").text('￥' + select_sku_price.toFixed(2));
			}
			return false;
		});

	},
	bind_buy_btn: function() {
		var that = this;
		$("#btn_add2cart").click(function() {
			that.add2cart("add");
		});
		$("#btn_buynow").click(function() {
			that.add2cart("buynow");

		});

	},
	add2cart: function(type) {
		var modal_dom_id = "modal_dom_id";
		var PostField = new Object();
		PostField.id = gloabl_item_id;
		if (global_has_sku && global_sku_current_obj.goods_sku_id == undefined) {
			M._alert('请先选择规格');
			return true;
		}
		if ($("#buy_num").val() > 0) {
			PostField.num = $("#buy_num").val();
			PostField.sid = global_sku_current_obj.goods_sku_id;
		}
		$.post(mobile_url + 'order/cart_save', PostField, function(data) {
			if (data.success) {
				$('#select_sku').modal('close');
				if (type == "buynow") {
					location.href = mobile_url + 'order/cart';
				}
				if (type == "add") {
					MODAL_HTML._confirm(modal_dom_id, "操作成功", "商品已经放入购物车！", "去结算", "再逛逛");
					$('#' + modal_dom_id).modal({
						relatedTarget: this,
						onConfirm: function(options) {

						},
						onCancel: function() {
							location.href = mobile_url + 'order/cart';
						}
					});
				}
			} else {
				alert(data.message);
			}
		}, 'json');

	},
	_union_sku: function() {
		var sku_code = [];
		var list_len = $(".sku_item_list").length;

		$(".sku_item_list").each(function(i) {
			$(this).find("a").each(function(j) {
				if ($(this).attr('issel') == 1) {
					sku_code.push($(this).attr('valueid'));
					return false;
				}
			});
		});

		if (sku_code.length == list_len) { //全部选好
			sku_code.sort(function(a, b) {
				return a - b
			});
			var sku_current = ""
			for (var i = 0; i < sku_code.length; i++) {
				sku_current += sku_code[i];
				if (i < sku_code.length - 1) {
					sku_current += "-";
				}
			}
			global_sku_current = sku_current;
			console.log(global_sku_current);
			for (var item in global_goods_sku_array) {
				var sku = global_goods_sku_array[item];
				global_sku_current_obj = sku;
				if (sku.goods_sku == global_sku_current) {
					$("#select_sku_num").show(100);
					$("#sku_stock").text('库存:' + sku.stock + "件").show();
					if (sku.stock > 0) {
						$("#buy_num").val(1);
						$("#select_sku_price").text('￥' + sku.price);
					}
					break;
				}
			}
		}

	}
}

// JavaScript Document 记录推荐人id
var locationHash = window.location.hash;
var agentRegexp = /agent=([\d]+)/g;
var agentMatch = agentRegexp.exec(locationHash);
if(agentMatch){
	$.cookie('agent_id', agentMatch[1], { expires: 7, path: '/'}); //���ô�ʱ���cookie
}