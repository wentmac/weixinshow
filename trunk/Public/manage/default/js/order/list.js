/*
			Create By wentmac @2015

                   _ooOoo_
                  o8888888o
                  88" . "88
                  (| -_- |)
                  O\  =  /O
               ____/`---'\____
             .'  \\|     |//  `.
            /  \\|||  :  |||//  \
           /  _||||| -:- |||||-  \
           |   | \\\  -  /// |   |
           | \_|  ''\---/''  |   |
           \  .-\__  `-`  ___/-. /
         ___`. .'  /--.--\  `. . __
      ."" '<  `.___\_<|>_/___.'  >'"".
     | | :  `- \`.;`\ _ /`;.`/ - ` : | |
     \  \ `-.   \_ __\ /__ _/   .-` /  /
======`-.____`-.___\_____/___.-`____.-'======
                   `=---='
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
			佛祖保佑       永无BUG

*/

var search = new function() {
	var searchSelf = this;
	this.pageDefault = 0;

	this.bindParam = function() {
		$("#condition_list a").click(function() {
			searchParameter.status = $(this).attr("data_status");
			search.getOrderList();
			$("#condition_list a").removeClass("am-btn-primary").addClass("am-btn-default");
			$(this).addClass("am-btn-primary");

		});
		$("#search_button").click(function() {
			if ($('#txt_keyword').val() != "") {
				searchParameter.query = $('#txt_keyword').val();				
			}
			searchParameter.start_date = $('#start_date').val();
			searchParameter.end_date = $('#end_date').val();
			searchParameter.query_id = $('#query_id').val();
			searchParameter.query_id_type = $('#query_id_type').val();
			search.getOrderList();
		});
		$('#export_button').click(function(){
			var export_start_page = $('#export_start_page').val();
			var export_end_page = $('#export_end_page').val();
			var url = php_self + "?m=order.export_order_list&status="+searchParameter.status+"&query="+searchParameter.query+"&export_start_page="+export_start_page+"&export_end_page="+export_end_page;
			window.open(url);
		});		
	}

	/**
	 * 分页后回调函数
	 *
	 * @param {int}page_index New Page index
	 * @param {jQuery} jq the container with the pagination links as a jQuery object
	 */
	this.pageselectCallback = function(page_index, jq) {
		var dataInfo = searchParameter;
		dataInfo.page = page_index + 1;
		if (page_index == 0 && searchSelf.pageDefault == 0) {
			return false;
		}
		$('#export_start_page').val(dataInfo.page);
		$('#tbody_order_list').html('');
		$('#order_list_loading').show();
		$.ajax({
			type: "GET",
			url: php_self + "?m=order.get_list&r=" + Math.random(),
			dataType: 'json', //接受数据格式            
			data: dataInfo,
			cache: false,
			success: function(result) {
				if (result.success == false) {
					M._alert(result.message);
				} else {
					$('#order_list_loading').hide();
					$('#tbody_order_list').html(searchSelf.buildOrderList(result.data.reqdata));
					searchSelf.agree_refund_yes_no(); //同意退款，不同意退款
					searchSelf.bindSearchList(result.data);
				}
			}
		});
		return false;
	}

	/**
	 * 绑定一些 list dom生成后的操作
	 */
	this.bindSearchList = function(param) {
		var page = param.retHeader.page;
		var count = param.retHeader.totalput;
		$('html,body').animate({
			scrollTop: $('#condition_list').offset().top
		}, 'fast');
		searchSelf.pageDefault == 0 && searchSelf.pageDefault++;
	}

	this.agree_refund_yes_no = function() {
		$(".agree_refund").bind("click", function() {
			var _this = this;
			$('#my-confirm .am-modal-bd').html("是否同意退款<br>退款类型：" + $(this).attr("data_refund_service_status_text") + "&nbsp;&nbsp;&nbsp;&nbsp;退款金额：￥" + $(this).attr("data_money"));
			$('#my-confirm').modal({
				relatedTarget: this,
				onConfirm: function(e) {
					
					$.ajax({
						type: "post",
						url: php_self + "?m=order.refund_yes",
						data: {
							order_refund_id: $(_this).attr("data_refund_id")
						},
						cache: false,
						success: function(data) {
							if (data.success) {
								$(_this).parents("td").html(data.data.refund_status_text);
								M._alert("操作成功");
							} else {
								M._alert(data.message);
							}
						},
						error: function(data) {
							M._alert(data.message);
						}
					});
				},
				onCancel: function(e) {
					M._alert('好好考虑考虑!');
				}
			});
		});
		$(".agree_refund_no").bind("click", function() {
			var _this = this;
			$('#my-prompt .am-modal-bd').html("是否拒绝退款<br>退款类型：" + $(this).attr("data_refund_service_status_text") + "&nbsp;&nbsp;&nbsp;&nbsp;退款金额：￥" + $(this).attr("data_money"));
			$('#my-prompt').modal({
				relatedTarget: this,
				onConfirm: function(e) {
					$.ajax({
						type: "post",
						url: php_self + "?m=order.refund_no",
						data: {
							order_refund_id: $(_this).attr("data_refund_id")
						},
						cache: false,
						success: function(data) {
							if (data.success) {
								$(_this).parents("td").html(data.data.refund_status_text);
								M._alert("操作成功");
							} else {
								M._alert(data.message);
							}
						},
						error: function(data) {
							M._alert(data.message);
						}
					});
				},
				onCancel: function(e) {
					M._alert('好好考虑考虑!');
				}
			});
		});
	}

	this.getOrderList = function() {
		var dataInfo = searchParameter;
		dataInfo.page = 1;
		//菊花转
		$('#tbody_order_list').html('');
		$('#order_list_loading').show();
		$('#order_list_nofund').hide(); //隐藏order_list_nofund先        
		$.ajax({
			type: "GET",
			url: php_self + "?m=order.get_list",
			dataType: 'json', //接受数据格式            
			data: dataInfo,
			cache: false,
			success: function(result) {
				if (result.success == false) {
					alert(result.message);
				} else {
					$('#order_list_loading').hide();
					$('#tbody_order_list').html(searchSelf.buildOrderList(result.data.reqdata));
					searchSelf.agree_refund_yes_no(); //同意退款，不同意退款
					searchSelf.bindSearchList(result.data);
					//隐藏loading

					if (result.data.retHeader.totalput == 0) {
						$('#tbody_order_list').html('');
						$('#order_list_nofund').show();
					}
					var optInit = {
						items_per_page: searchParameter.pagesize,
						num_display_entries: searchParameter.pagesize,
						num_edge_entries: 1,
						link_to: "#",
						prev_text: "上一页",
						next_text: "下一页",
						callback: search.pageselectCallback
					};
					$("#roomListPages").pagination(result.data.retHeader.totalput, optInit);
				}
			}
		});
		return true;
	}

	this.buildOrderList = function(orderList) {
		var tbody_html = "";
		for (var i = 0; i < orderList.length; i++) {
			var order_info = orderList[i];
			tbody_html += '<tr><td class="td_left">'
			var goods_array = order_info.order_goods_array;
			for (var j = 0; j < goods_array.length; j++) {
				var order_goods = goods_array[j];
				tbody_html += '<div class="am-g">';
				tbody_html += '<div class="am-u-sm-9 am-padding-right-xs">';
				tbody_html += '<img src="' + order_goods.goods_image_url + '" width="80" height="80">';
				tbody_html += '<a class="line-clamp am-text-sm" target="_blank" href="' + mobile_url + 'goods/' + order_goods.goods_id + '.html">' + order_goods.item_name+ '</a>';
				tbody_html += '</div>';
				tbody_html += '<div class="am-u-sm-3 am-text-right am-text-sm am-padding-left-0" style="color:#AAA">';
				var sku_name = order_goods.goods_sku_name;
				if (sku_name == "") {
					sku_name = "无";
				}

				tbody_html += '' + sku_name + '';
				tbody_html += '<br>';
				tbody_html += '￥' + order_goods.item_price + '';
				tbody_html += '<br>';
				tbody_html += '<h3 style="color:red">x ' + order_goods.item_number + '</h3>';
				tbody_html += '<br>';
				tbody_html += order_goods.outer_code;
				tbody_html += '</div>';

				tbody_html += '</div>';
				if (j != goods_array.length - 1) {
					tbody_html += '<hr>';
				}
			}
			tbody_html += '	</td>';
			tbody_html += '	<td class="am-text-middle am-text-sm">' + order_info.uid + '</td>';
			tbody_html += '	<td class="am-text-middle am-text-sm">' + order_info.consignee + '</td>';
			tbody_html += '	<td class="am-text-middle am-text-sm">' + order_info.order_sn + '</td>';
			tbody_html += '	<td class="am-text-middle am-text-sm">' + order_info.order_item_count + '</td>';
			tbody_html += '	<td class="am-text-middle am-text-sm">' + order_info.commission_fee + '</td>';
			tbody_html += '	<td class="am-text-middle am-text-sm">' + order_info.shipping_fee + '</td>';
			if ( order_info.coupon_code == '' ) {
				tbody_html += '	<td class="am-text-middle am-text-sm">' + order_info.order_amount + '</td>';
			} else {
				var order_amount = parseInt(order_info.order_amount)+parseInt(order_info.coupon_money);
				tbody_html += '	<td class="am-text-middle am-text-sm">' + order_amount+'<br>代:'+order_info.coupon_money + '</td>';
			}			
			tbody_html += '	<td class="am-text-middle am-text-sm am-text-danger">' + order_info.order_status_text + '</td>';
			tbody_html += '	<td class="am-text-middle am-text-sm">' + order_info.create_time + '</td>';
			tbody_html += '	<td class="am-text-middle">';

			if (order_info.order_status == 2) {
				if (order_info.supplier_status == true) {
					tbody_html += '<a href="' + php_self + '?m=order.detail&order_id=' + order_info.order_id + '" class="am-btn am-btn-secondary am-btn-xs">发货</a></br>';
				} else {
					tbody_html += '<font>联系供应商:' + order_info.supplier_mobile + '</font>';
				}
			}
			if (order_info.have_return_service > 0) {
				tbody_html += '<font>维权订单</font></br>';
			} else if (order_info.refund_status == 1) {
				if (order_info.supplier_status == true) {
					tbody_html += '<font>退款中</font>';
					tbody_html += '<a data_money="'+order_info.money+'" class="am-btn am-btn-secondary am-btn-xs agree_refund" data_refund_service_status_text="'+order_info.refund_service_status_text+'"  data_refund_id="' + order_info.order_refund_id + '">同意退款</a></br>';
					tbody_html += '<a data_money="'+order_info.money+'" class="am-btn am-btn-danger am-btn-xs agree_refund_no" data_refund_service_status_text="'+order_info.refund_service_status_text+'" data_refund_id="' + order_info.order_refund_id + '">拒绝退款</a></br>';
				} else {
					tbody_html += '<font>联系供应商:' + order_info.supplier_mobile + '</font>';
				}
			} else if (order_info.refund_status == 2 || order_info.refund_status == 3) {
				tbody_html += '<font>有退款</font></br>';
			}

			tbody_html += '<a target="_blank" href="' + php_self + '?m=order.detail&order_id=' + order_info.order_id + '">查看详细</a></br>';
			tbody_html += '</td>';
			tbody_html += '	</tr>';
		}
		return tbody_html;
	}
}