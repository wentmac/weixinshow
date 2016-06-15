$(function() {
	data_builder.get_bill_list();
	$("#nav_show a").click(function() {
		$(".bill_type_info").html($(this).html());
		data_builder.get_bill_list(this);
	});
});
var pageDefault = 0;
var data_builder = {
	get_bill_list: function(_this) {
		$("#bill_list_loading").show();
		$("#bill_list_nofund").hide();
		$("#tbody_html").html("");
		param.status = $(_this).attr("data_key");
		$.ajax({
			type: "get",
			url: php_self + "?m=bill.get_bill_list",
			data: param,
			cache:false,
			success: function(data) {
				if (data.success == true) {
					if (data.data.reqdata.length == 0) {
						$("#bill_list_nofund").show();
						$("#bill_list_loading").hide();
					} else {
						$("#bill_list_nofund").hide();
						$("#tbody_html").html(tmpl.bill_list(data.data.reqdata));
						$("#bill_list_loading").hide();
					}
					var optInit = {
						items_per_page: param.pagesize,
						num_display_entries: param.pagesize,
						num_edge_entries: 1,
						link_to: "#",
						prev_text: "上一页",
						next_text: "下一页",
						callback: data_builder.pageselectCallback
					};
					$("#roomListPages").pagination(data.data.retHeader.totalput, optInit);
				} else {
					M._alert(data.message);
				}
			}
		});
	},
	/**
	 * 绑定一些 list dom生成后的操作
	 */
	bindSearchList: function(param) {
		var page = param.retHeader.page;
		var count = param.retHeader.totalput;
		$('html,body').animate({
			scrollTop: $('.admin-content').offset().top
		}, 'fast');
		searchSelf.pageDefault == 0 && searchSelf.pageDefault++;
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
		if (dataInfo.page == 0 && pageDefault == 0) {
			return false;
		}
		$('#tbody_html').html('');
		$('#bill_list_loading').show();
		$.ajax({
			type: "GET",
			url: php_self + "?m=bill.get_bill_list",
			dataType: 'json', //接受数据格式            
			data: dataInfo,
			cache:false,
			success: function(result) {
				if (result.success == false) {
					M._alert(result.message);
				} else {
					$('#bill_list_loading').hide();
					$('#tbody_html').html(tmpl.bill_list(result.data.reqdata));
				}
			}
		});
		return false;
	}


}

var tmpl = {
	bill_list: function(list) {

		var tbody_html = "";
		if (list.length > 0) {
			for (var i = 0; i < list.length; i++) {
				var info = list[i];
				tbody_html += '<tr>';
				tbody_html += '<td class="am-text-center"><img class="am-comment-avatar" src="' + info.bill_image_id + '" alt=""></td>';
				tbody_html += '<td class="am-text-middle">' + info.bill_realname + info.bill_note + '</td>';
				tbody_html += '<td class="am-text-middle">' + info.bill_time + '</td>';
				tbody_html += '<td class="am-text-middle">￥' + info.money + '</td>';
				tbody_html += '<td class="am-text-middle">' + info.bill_status + '</td>';
				tbody_html += '<td class="am-text-middle">';
				if (info.order_id != 0) {
					tbody_html += '<a href="' + php_self + '?m=order.detail&order_id=' + info.order_id + '">查看详细</a></td>';
				}
				tbody_html += '</tr>';
			}
		}
		return tbody_html;
	}
};