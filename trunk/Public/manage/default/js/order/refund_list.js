var pageDefault = 0;
var data_builder = {
	bindParam: function() {
		$("#btns_list button").click(function() {
			if ($(this).attr("data_id") == "seller_confirm") {
				param.status = "seller_confirm";
			}
			if ($(this).attr("data_id") == "buyer_confirm") {
				param.status = "buyer_confirm";
			}
			if ($(this).attr("data_id") == "customer_confirm") {
				param.status = "customer_confirm";
			}
			if ($(this).attr("data_id") == "complete") {
				param.status = "complete";
			}
			if ($(this).attr("data_id") == "close") {
				param.status = "close";
			}
			if ($(this).attr("data_id") == "all") {
				param.status = "";
			}
			$("#btns_list button").removeClass("am-btn-primary");

			$(this).addClass("am-btn-primary");
			data_builder.get_order_list(param);
		});
		$("#txt_keyword").blur(function() {
			if ($(this).val() != "") {
				param.query = $(this).val();
				data_builder.get_order_list(param);
			}
		});
	},
	/**
	 * 分页后回调函数
	 *
	 * @param {int}page_index New Page index
	 * @param {jQuery} jq the container with the pagination links as a jQuery object
	 */
	pageselectCallback: function(page_index, jq) {
		var dataInfo = param;
		dataInfo.page = page_index + 1;
		dataInfo.pagesize = 5;
		if (page_index == 0 && pageDefault == 0) {
			return false;
		}
		$('#tbody_refund_order_list').html('');
		$('#order_list_nofund').show();
		$.ajax({
			type: "GET",
			url: php_self + "?m=order.get_refund_list",
			dataType: 'json', //接受数据格式            
			data: dataInfo,
			cache:false,
			success: function(result) {
				if (result.success == false) {
					M._alert(result.message);
				} else {
					$('#order_list_loading').hide();

					$('#tbody_refund_order_list').html(data_builder.builder_refund_list(result.data.reqdata));
					data_builder.btns_do();
					data_builder.bindSearchList(result.data);
				}
			}
		});
		return false;
	},
	/**
	 * 绑定一些 list dom生成后的操作
	 */
	bindSearchList: function(param) {
		var page = param.retHeader.page;
		var count = param.retHeader.totalput;
		$('html,body').animate({
			scrollTop: $('#btns_list').offset().top
		}, 'fast');
		pageDefault == 0 && pageDefault++;
	},
	get_order_list: function(param) {
		var dataInfo = param;
		dataInfo.page = 1;
		dataInfo.pagesize = 5;
		$('#tbody_refund_order_list').html('');
		$('#order_list_loading').show();
		$('#order_list_nofund').hide(); //隐藏order_list_nofund先      
		$.ajax({
			type: "get",
			url: php_self + "?m=order.get_refund_list",
			data: dataInfo,
			cache:false,
			success: function(data) {
				if (data.success == true) {
					var list = data.data.reqdata;
					$('#order_list_loading').hide();

					$('#tbody_refund_order_list').html(data_builder.builder_refund_list(list));
					data_builder.btns_do();
					data_builder.bindSearchList(data.data);
					if (data.data.retHeader.totalput == 0) {
						$('#tbody_refund_order_list').html('');
						$('#order_list_nofund').show();
					}
					var optInit = {
						items_per_page: dataInfo.pagesize,
						num_display_entries: dataInfo.pagesize,
						num_edge_entries: 1,
						link_to: "#",
						prev_text: "上一页",
						next_text: "下一页",
						callback: data_builder.pageselectCallback
					};
					$("#roomListPages").pagination(data.data.retHeader.totalput, optInit);
				} else {
					M._alert(result.message);
				}
			}

		});
	},
	builder_refund_list: function(list) {
		var tbody_html = "";
		for (var i = 0; i < list.length; i++) {
			var order_info = list[i];
			tbody_html += '<tr><td class="td_left">'
			var goods_array = order_info.order_goods_array;
			for (var j = 0; j < goods_array.length; j++) {
				var order_goods = goods_array[j];
				tbody_html += '<div class="am-g">';
				tbody_html += '<div class="am-u-sm-8">';
				tbody_html += '<img src="' + order_goods.goods_image_url + '" width="80" height="80">';
				tbody_html += '<a target="_blank" href="'+mobile_url+'/item/'+order_goods.item_id+'.html" >' + order_goods.item_name + '</a>';
				tbody_html += '</div>';
				tbody_html += '<div class="am-u-sm-4 am-text-right am-text-sm am-padding-left-0" style="color:#AAA">';
				tbody_html += '' + order_goods.goods_sku_name + '';
				tbody_html += '<br>';
				tbody_html += '￥' + order_goods.item_price + '';
				tbody_html += '<br>';
				tbody_html += 'X' + order_goods.item_number + '';
				tbody_html += '</div>';
				tbody_html += '</div>';
				if (j != goods_array.length - 1) {
					tbody_html += '<hr>';
				}
			}
			tbody_html += '	</td>';
			tbody_html += '	<td class="am-text-middle">' + order_info.uid + '</td>';
			tbody_html += '	<td class="am-text-middle">' + order_info.consignee + '</td>';
			tbody_html += '	<td class="am-text-middle">' + order_info.order_sn + '</td>';
			tbody_html += '	<td class="am-text-middle">' + order_info.order_item_count + '</font></td>';
			tbody_html += '	<td class="am-text-middle">' + order_info.commission_fee + '</font></td>';
			tbody_html += '	<td class="am-text-middle">' + order_info.money + '</font></td>';
			tbody_html += '	<td class="am-text-middle"><font class="am-text-danger">' + order_info.status_text + '</font></td>';
			tbody_html += '	<td class="am-text-middle">';

			if (order_info.service_status == 1 && order_info.refund_status == 1) {
				if (order_info.supplier_status == true) {
					tbody_html += '<a style="margin-bottom:1px;" data_refund_id="' + order_info.order_refund_id + '" data_name="' + order_info.consignee + '" data_money="' + order_info.money + '"  class="am-btn am-btn-secondary am-btn-xs agree_refund">同意退款</a>';
					tbody_html += '<a style="margin-bottom:1px;" data_name="' + order_info.consignee + '" data_money="' + order_info.money + '" data_refund_id="' + order_info.order_refund_id + '"  class="am-btn am-btn-danger am-btn-xs refused_refund">拒绝退款</a>';
				} else {
					tbody_html += '联系供应商' + order_info.supplier_mobile;
				}
			}
			if (order_info.service_status == 1 && order_info.refund_status == 2 && order_info.return_status == 2) {
				if (order_info.supplier_status == true) {
					tbody_html += '<a data_refund_id="' + order_info.order_refund_id + '" style="margin-bottom:1px;"  class="am-btn am-btn-secondary am-btn-xs received_return">收到退货</a>';
					tbody_html += '<a data_refund_id="' + order_info.order_refund_id + '" style="margin-bottom:1px;" class="am-btn am-btn-danger am-btn-xs received_return_no">没有收到退货</a>';
				} else {
					tbody_html += '联系供应商' + order_info.supplier_mobile;
				}
			}
			tbody_html += '<br><a target="_blank" href="' + php_self + '?m=order.detail&order_id=' + order_info.order_id + '">查看详细</a></br>';
			tbody_html += '</td>';
			tbody_html += '	</tr>';
		}
		return tbody_html;
	},
	btns_do: function() {		
		$(".agree_refund").bind("click", function() {
			var _this = this;						
			$('#my-confirm .am-modal-bd').html("请确认是否要同意" + $(this).attr("data_name") + " ￥" + $(this).attr("data_money") + "的退款申请");
			$('#my-confirm').modal({
				relatedTarget: this,
				onConfirm: function(e) {					
					var order_refund_id = $(this.relatedTarget).attr("data_refund_id");			
					var _this = $(this.relatedTarget);
					$.ajax({
						type: "post",
						url: php_self + "?m=order.refund_yes",
						data: {
							order_refund_id: order_refund_id
						},
						cache:false,
						success: function(data) {
							if (data.success) {
								_this.parents("td").html(data.data.refund_status_text);
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

		$(".refused_refund").bind("click", function() {
			var _this = this;
			$('#my-prompt .am-modal-bd').html("请确认是否要拒绝" + $(this).attr("data_name") + " ￥" + $(this).attr("data_money") + "的退款申请");
			$('#my-prompt').modal({
				relatedTarget: this,
				onConfirm: function(e) {
					var order_refund_id = $(this.relatedTarget).attr("data_refund_id");
					var _this = $(this.relatedTarget);
					$.ajax({
						type: "post",
						url: php_self + "?m=order.refund_no",
						data: {
							order_refund_id: order_refund_id,
							reason: e.data
						},
						cache:false,
						success: function(data) {
							if (data.success) {
								_this.parents("td").html(data.data.refund_status_text);
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

		$(".received_return").bind("click", function() {
			var _this = this;
			$('#my-confirm .am-modal-bd').html("请确认已经收到退货");
			$('#my-confirm').modal({
				relatedTarget: this,
				onConfirm: function(e) {
					var order_refund_id = $(this.relatedTarget).attr("data_refund_id");
					var _this = $(this.relatedTarget);
					$.ajax({
						type: "post",
						url: php_self + "?m=order.receipt_yes",
						data: {
							order_refund_id: order_refund_id
						},
						cache:false,
						success: function(data) {
							if (data.success) {
								_this.parents("td").html("");
								_this.parents("td").html(data.data.refund_status_text);
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
		$(".received_return_no").bind("click", function() {
			var _this = this;
			$('#my-confirm .am-modal-bd').html("请确认已经收到退货");
			$('#my-confirm').modal({
				relatedTarget: this,
				onConfirm: function(e) {
					var order_refund_id = $(this.relatedTarget).attr("data_refund_id");
					var _this = $(this.relatedTarget);
					$.ajax({
						type: "post",
						url: php_self + "?m=order.receipt_no",
						data: {
							order_refund_id: order_refund_id
						},
						cache:false,
						success: function(data) {
							if (data.success) {
								_this.parents("td").html("");
								_this.parents("td").html(data.data.refund_status_text);
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
}