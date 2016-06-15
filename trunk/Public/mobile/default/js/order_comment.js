jQuery(function() {
	jQuery(".rank-num span").click(function() {
		for (var i = 0; i < 5; i++) {
			if (jQuery(".rank-num span").eq(i).hasClass("markVal")) {
				jQuery(".rank-num span").eq(i).removeClass("markVal");
			}
			$(this).addClass("markVal");
		}
		jQuery("#orange0").css("width", (jQuery(".rank-num span").index(this) + 1) * 20);
	});
	jQuery("#btnok").click(function() {
		init.btnok_click();
	});
});
var init = {
	btnok_click: function() {
		var items_li_json = new Object();
		var items_li = jQuery("#productList .noborder");
		var comment_num = 0;
		for (var i = 0; i < items_li.length; i++) {
			//'{"1":{"rank":2,"content":"产品很棒，用了后胸变大了不少哟"},"2":{"rank":5,"content":"老板的产品给力，用了后时间变长了"}}';
			var order_goodid = items_li.eq(i).find(".commentVal").attr("data_order_goodid");

			var _rank = init.get_rank(i);

			var _content = items_li.eq(i).find(".commentVal").val();
			if (_content == "") {
				items_li.eq(i).find(".commentVal").focus();
				alert("您还没有做任何评论！");
				return false;
			} else {
				comment_num = 1;
			}

			items_li_json[order_goodid] = {
				rank: _rank,
				content: _content
			};
		}
		
		if (comment_num == 1) {
			jQuery.ajax({
				url: mobile_url + 'member/order.comment_save',
				type: 'post',
				dataType:'json',
				data: {
					sn:global_order_sn ,
					param: JSON.stringify(items_li_json)
				},
				cache:false,
				success: function(data) {
					if (data.success == true) {
						alert("评价成功!");
						location.href=mobile_url+"member/order?status=wating_comment"
						
					} else {
						alert(data.message);
					}
					
				},
				error: function(data) {
					alert("服务器正忙请稍后再试");
				}
			});
		} else {
			alert("您还没有做任何评论！");
		}
	},
	get_rank: function(order_goods_index) {
		var rank_num = jQuery(".rank-num").eq(order_goods_index);

		for (var i = 0; i < 5; i++) {
			if (rank_num.children("span").eq(i).hasClass("markVal")) {
				
				return (parseInt(i) + 1);
			}
		}
	}
}