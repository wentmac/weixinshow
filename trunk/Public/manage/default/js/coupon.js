var formValidate = new formValidate();
formValidate.init({});

$(document).ready(function() {		
	search.bindParam();
	search.getCouponList();

	$('#coupon_create_button').click(function(){
		var coupon_num=$('#coupon_num').val();
		var coupon_value=$('#coupon_value').val();
		//判断int

		
		var check_coupon_num = formValidate.check(coupon_num,'number');
		var check_coupon_value = formValidate.check(coupon_value,'number');
		
		if (check_coupon_num !== true){
			M._alert(check_coupon_num);
			$('#coupon_num').focus();
			return false;
		}
		if (check_coupon_value !== true){
			M._alert(check_coupon_value);
			$('#coupon_value').focus();
			return false;
		}
		//判断是否大于余额
		var coupon_money = coupon_num * coupon_value;
		if ( coupon_money > coupon_money_credits ) {
			M._alert('余额不足');			
			$('#coupon_value').focus();
			return false;
		}		

		$('#coupon_create_button').attr('disabled',true);
		var dataParam = {
			'coupon_num':coupon_num,
			'coupon_value':coupon_value
		};
		$.ajax({
			type: "POST",
			url: index_url + php_self + '?m=coupon.create',
			dataType: "json",
			data: dataParam,
			cache: false,
			success: function(data) {
				//console.log(data);
				$('#coupon_create_button').removeAttr("disabled");
				if (data.success == true) {
					M._alert('代金券生成成功');														  	
					search.getCouponList();
				} else {					
					M._alert(data.message);					
					return false;
				}
			}
		});		
	});

});




var search = new function() {
	var searchSelf = this;
	this.pageDefault = 0;

	this.bindParam = function() {		
		$("#condition_list button").click(function() {
			searchParameter.coupon_status = $(this).attr("data_status");
			search.getCouponList();
			$("#condition_list button").removeClass("am-btn-primary").addClass("am-btn-default");
			$(this).addClass("am-btn-primary");

		});
		$("#coupon_code_submit").click(function() {
			var coupon_code = $('#txt_keyword').val();
			if ( coupon_code != "") {
				searchParameter.coupon_code = coupon_code;
				search.getCouponList();
			}
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
		$('#tbody_order_list').html('');
		$('#order_list_loading').show();
		$.ajax({
			type: "GET",
			url: php_self + "?m=coupon.get_list&r=" + Math.random(),
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

	this.getCouponList = function() {
		var dataInfo = searchParameter;
		dataInfo.page = 1;
		//菊花转
		$('#tbody_order_list').html('');
		$('#order_list_loading').show();
		$('#order_list_nofund').hide(); //隐藏order_list_nofund先        
		$.ajax({
			type: "get",
			url: index_url+php_self + "?m=coupon.get_list",
			dataType: 'json', //接受数据格式            
			data: dataInfo,
			cache: false,
			success: function(result) {
				if (result.success == false) {
					alert(result.message);
				} else {
					$('#order_list_loading').hide();
					$('#tbody_order_list').html(searchSelf.buildOrderList(result.data.reqdata));					
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
			var coupon_info = orderList[i];
        	tbody_html += '<tr>';
            tbody_html += '<td class="am-text-middle">'+coupon_info.coupon_id+'</td>';
            tbody_html += '<td class="am-text-middle">'+coupon_info.coupon_code+'</td>';
            tbody_html += '<td class="am-text-middle">￥'+coupon_info.coupon_money+'</td>';
            tbody_html += '<td class="am-text-middle">'+coupon_info.coupon_status+'</td>';                
            tbody_html += '<td class="am-text-middle">'+coupon_info.create_time+'</td>';
            tbody_html += '<td class="am-text-middle">'+coupon_info.order_sn+'</td>';
            tbody_html += '<td class="am-text-middle">'+coupon_info.use_time+'</td>';
          	tbody_html += '</tr>';						
		}
		return tbody_html;
	}
}