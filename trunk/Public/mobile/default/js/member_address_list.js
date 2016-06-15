$(function() {
    $(".js_defaultBtn").click(function() {	//设置默认地址
    	var _this = this;
        var address_id = $(this).attr('id');
        $.ajax({
            url: mobile_url + 'member/address.default_setting',
            type: 'POST',
            dataType: 'json',
            data: {
                address_id: address_id
            },
            cache:false,
            success: function(data) {
                if (data.success == true) {
                	$(".js_defaultBtn").removeClass('esp');
                	$(_this).addClass('esp');
                } else {
                    alert(data.message);
                }
            }
        });
    });

    $(".js_delect").click(function(){
    	var _this = this;
    	var address_id = $(this).attr('id');
        $.ajax({
            url: mobile_url + 'member/address.delete',
            type: 'GET',
            dataType: 'json',
            data: {
                id: address_id
            },
            cache:false,
            success: function(data) {
                if (data.success == true) {
                	$(_this).parents('.js_addressLength').remove();
                } else {
                    alert(data.message);
                }
            }
        });
    });
});
