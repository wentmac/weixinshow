$(function() {
	if($("#a_express_name").html()=="无需物流"|| $("#a_express_name").html()=="")
	{$("#a_express").hide();}
	
	$("#a_express").click(function() {
		data_builder.get_express_info();
	});
	//执行发货按钮
	$("#delivery").click(function() {
		order.getOrderList();
	});
	//提交发货操作
	$("#btn_express").click(function() {
		data_builder.submit_express();
	});
	$("#close").click(function() {
		$("#div_delivery_list").hide("slow");
	});
	$("#order_close").click(function() {
		$("#order_delivery_list").hide("slow");
		$("#checkAll").unbind('click').prop("checked", false);
		$("#merge_delivery").unbind('click');
	});	
	//需要发货配置
	$("#need_delivery").click(function() {
		$("#need_delivery em").addClass("am-icon-check-circle");
		$("#need_delivery em").removeClass("am-icon-circle-thin");

		$("#need_delivery_no em").removeClass("am-icon-check-circle");
		$("#need_delivery_no em").addClass("am-icon-circle-thin");
		$("#div_delivery_list ul").show();
		$("#delivery_list").show();
		$("#fedexName").show();
		$("#hid_express").attr("data_id", "0");
		$("#hid_express").attr("data_name", "");
	});
	//不需要发货配置
	$("#need_delivery_no").click(function() {
		$("#hid_express").attr("data_id", "-1");
		$("#hid_express").attr("data_name", "");
		$("#need_delivery_no em").addClass("am-icon-check-circle");
		$("#need_delivery_no em").removeClass("am-icon-circle-thin");

		$("#need_delivery em").addClass("am-icon-circle-thin");
		$("#need_delivery em").removeClass("am-icon-check-circle");
		$("#div_delivery_list ul").hide();
		$("#delivery_list").hide();
		$("#fedexName").hide();
	});
	



});
var data_builder = {
	get_express_info: function() {
		$("#a_express").html("正在查询中....");
		$.ajax({
			type: "get",
			url: php_self + "?m=order.get_express_info",
			data: {
				express_id: $("#a_express_name").attr("data-id"),
				express_no: $("#a_express_no").attr("data-no"),
			},
			cache:false,
			success: function(data) {
				if (data.success == true) {
					var info = data.data.express_detail;
					if (info == "") {
						info = "还没有查询到任何信息!";
					}
					$("#div_express").html(info);
					$("#a_express").html("点击查看");
				} else {
					$("#a_express").html("查询失败");
				}
			},
			error: function(data) {
				$("#a_express").html("系统正忙请稍后查询...");
			}


		});
	},
	get_delivery: function() {

		$.ajax({
			type: "get",
			url: php_self + "?m=order.get_express",
			data: {},
			cache:false,
			success: function(data) {
				if (data.success == true) {
					$("#delivery_list").html("");
					var express = "";
					var list = data.data;
					for (var i = 0; i < list.length; i++) {

						express += '<li  data-express-id="' + list[i].express_id + '" data-express-name="' + list[i].express_name + '">' + list[i].express_name + '<span class="hide"><em class="am-icon-check red"></em></span>'
					}
					$("#delivery_list").html($("#delivery_list").html() + express);
					$("#delivery_list").show();
					$("#div_delivery_list").show();
					data_builder.li_click();
				} else {
					alert(data.message);
					return false;
				}
			},
			error: function(data) {
				alert(data.message);
			}

		});


	},
	li_click: function() {
		$("#delivery_list li").bind("click", function() {
			$("#delivery_list li").find("span").hide();
			$(this).find("span").show();
			$("#hid_express").attr("data_id", $(this).attr("data-express-id"));
			$("#hid_express").attr("data_name", $(this).attr("data-express-name"));
		});
	},
	submit_express: function() { //提交收获地址
		var express_id = $("#hid_express").attr("data_id");
		var express_name = $("#hid_express").attr("data_name");
		if ($("#fedexName").val() != "") {
			express_id = "0";
			express_name = $("#fedexName").val();
		}
		if (express_id == "") {
			M._alert("请选择快递！");
		}
		$.ajax({
			type: "post",
			url: php_self + "?m=order.delivery",
			data: {
				order_id: order_id_string,
				express_id: express_id,
				express_name: express_name,
				express_no: $("#fedexNum").val()
			},
			cache:false,
			success: function(data) {
				if (data.success) {
					$("#div_delivery_list").hide();
					M._alert("提交成功");
					window.location.reload();
				} else {
					M._alert(data.message);
				}
			},
			error: function(data) {
				M._alert(data.message);
			}

		});
	}

}

/**
 * 未发货订单处理
 */
var order = new function() {
	var orderSelf = this;
	this.pageDefault = 0;

	this.getOrderList = function() {
		$('#order_delivery_list').show('slow');
		var dataInfo = {};
		dataInfo.page = 1;
		dataInfo.pagesize = 100;
		dataInfo.status = 'wating_seller_delivery';
		dataInfo.address_id = address_id;
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
					$('#tbody_order_list').html(orderSelf.buildOrderList(result.data.reqdata));										
					//隐藏loading
					orderSelf.bindCheckAll();
					$("#checkAll").trigger('click');
					orderSelf.bindMergeDelivery();

					if (result.data.retHeader.totalput == 0) {
						$('#tbody_order_list').html('');
						$('#order_list_nofund').show();
					}					
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
			tbody_html += '	<td class="am-text-middle am-text-sm">' + order_info.consignee + '(' + order_info.uid + ')</td>';
			tbody_html += '	<td class="am-text-middle am-text-sm">' + order_info.mobile + '<br>' + order_info.full_address + '</td>';
			tbody_html += '	<td class="am-text-middle am-text-sm">' + order_info.order_sn + '<br>' + order_info.create_time + '</td>';
			tbody_html += '	<td class="am-text-middle am-text-sm">' + order_info.order_item_count + '</td>';																
			tbody_html += '	<td class="am-text-middle">';

			if (order_info.order_status == 2) {
				if (order_info.supplier_status == true) {					
					tbody_html += '<input type="checkbox" name="address_id" value="' + order_info.order_id + '"><br>';
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
	this.bindCheckAll = function(){
		$("#checkAll").bind('click',function() {		
		    $('[name=address_id]:checkbox').each(function () {
		        //此处用Jquery写法
		        if ($(this).prop("checked")) {
					$(this).prop("checked", false);	        
		        } else {
					$(this).prop("checked", true);	        
		        }
		        
		        //直接使用JS原生代码，简单实用
		        //this.checked = !this.checked;
		    });		
		});		
	}
	this.bindMergeDelivery = function(){
		$("#merge_delivery").bind('click',function() {				
				order_id_string = '';
                $('[name=address_id]:checkbox:checked').each(function () {
                    order_id_string += ','+$(this).val();
                })			
                order_id_string = order_id_string.substr(1);	
                console.log(order_id_string);
                var order_count = $('[name=address_id]:checkbox:checked').length;
                if ( order_count == 0 ) {
                	M._alert('没有选择发货的订单呢');
                	return false;
                }
                $('#merge_delivery_text').text('确认合并发货'+order_count+'个订单吗？');
		      $('#my-confirm').modal({
		        relatedTarget: this,
		        onConfirm: function(options) {
		          /**
		          var $link = $(this.relatedTarget).prev('a');
		          var msg = $link.length ? '你要删除的链接 ID 为 ' + $link.data('id') :
		            '确定了，但不知道要整哪样';
		          alert(msg);
		            */
		            $("#order_delivery_list").hide();
		            data_builder.get_delivery();
		            $('#div_delivery_list h2 b').html('<font color="red">'+order_count+'</font>个订单一起发货，请填写发货信息');
		          
		        },
		        // closeOnConfirm: false,
		        onCancel: function() {
		          	order_id_string = '';
		        }
		      });			
		});		
	}	
}