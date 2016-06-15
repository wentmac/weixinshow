$(function() {
	data_builder.get_goods_list();
	$(".subclass").click(function() {
		data_builder.get_goods_list();
	});

});
var data_builder = {
	get_goods_list: function() {
		var _this = this;
		$.ajax({
			type: "get",
			url: mobile_url + "market/get_goods_list",
			data: {
				goods_cat_id: $(_this).attr("data_id")
			},
			success: function(data) {
				if (data.success == true) {
					var list = data.data.reqdata;
					var str_html = '';
					for (var i = 0; i < list.length; i++) {
						str_html += '<a href="' + mobile_url + 'market/goods_detail?id=' + list[i].goods_id + '" class="am-u-sm-6 am-block am-padding-xs am-img-thumbnail">';
						str_html += '<div>';
						str_html += '<img class="am-u-sm-12 am-u-end" src="' + list[i].goods_image_url + '">' + list[i].goods_name.substr(0, 18) + '...' + '';
						str_html += '<br><em>佣金:￥' + list[i].commission_fee + '</em>';
						str_html += '<span class="am-icon-plus-circle am-text-danger am-fr">';
						if(list[i].wholesale==true){
							str_html+='下架';
						}else{
							str_html+='上架';
						}
						str_html = +'</span>';
						str_html += '</div>';
						str_html += '</a>';
					}
					$("#goods_list").html(str_html);
				} else {
					alert("系统繁忙请稍后再试");
				}
			},
			error: function(data) {
				alert("系统繁忙请稍后再试");
			}
		});
	}

}