$(function() {
    $("#order_request").click(function(){
        location.href = '/member/address.select?address_id='+global_address_id;
    });

    $("#submit_order").click(function() {
    	if($(this).hasClass("isloading")){
    		return false;
    	}
    	var _this=this;
    	$(this).addClass("isloading");
    	$(this).html("提交中...")
        var remark = $("#remark").val();
        var wxID = $("#wxID").val();
        var cart_id_string = '';
        var agent_uid=$("#agent_uid").val();
        $(".li_for_pay").each(function(i) {
            var id = $(this).attr('data-cart-id');
            cart_id_string += id;
            if (i < $(".li_for_pay").length - 1) {
                cart_id_string += ",";
            }
        });
       
        var dataParam = {
            goods_uid: global_goods_uid,
            item_uid: global_item_uid,
            address_id: global_address_id,
            postscript: remark,
            weixin_id: wxID,
            cart_id_string: cart_id_string,
            agent_uid: agent_uid
        };
        $.ajax({
            url: mobile_url + 'order/save',
            type: 'POST',
            dataType: 'json',
            data: dataParam,
            cache:false,
            success: function(data) {
                if (data.success == true) {
                	$(_this).removeClass("isloading");
                    location.href = mobile_url + 'order/payment?sn=' + data.data;
                    
                } else {
                    alert(data.message);
                    if (data.status == -2) {
                        location.href = mobile_url + 'order/cart';
                    }
                }
            }
        });
    })
});
