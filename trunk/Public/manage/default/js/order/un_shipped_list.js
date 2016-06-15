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
			url: php_self + "?m=order.get_un_shipped&r=" + Math.random(),
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

	this.getOrderList = function() {
		var dataInfo = searchParameter;
		dataInfo.page = 1;
		//菊花转
		$('#tbody_order_list').html('');
		$('#order_list_loading').show();
		$('#order_list_nofund').hide(); //隐藏order_list_nofund先        
		$.ajax({
			type: "GET",
			url: php_self + "?m=order.get_un_shipped",
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
			var order_info = orderList[i];
			tbody_html += '<tr>'
			tbody_html += '	<td class="am-text-middle am-text-sm">' + order_info.goods_id + '</td>';
			tbody_html += '	<td class="am-text-left am-text-sm"><a href="'+mobile_url+'goods/'+order_info.goods_id+'.html" target="_blank">' + order_info.item_name + '</a></td>';
			tbody_html += '	<td class="am-text-left am-text-sm">' + order_info.goods_sku_name + '</td>';
			tbody_html += '	<td class="am-text-middle am-text-sm">' + order_info.goods_type + '</td>';
			tbody_html += '	<td class="am-text-middle am-text-sm">' + order_info.item_count + '</td>';			
			tbody_html += '	</tr>';
		}
		return tbody_html;
	}
}