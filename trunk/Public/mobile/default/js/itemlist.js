$(function() {
    data_builder.init();
    data_builder.get_item_list();

    $(window).scroll(function() { //内容懒加载
        if ($(document).height() - 50 <= parseFloat($(window).height()) + parseFloat($(window).scrollTop()) && global_crrent_page < global_total_page) {
            data_builder.get_item_list();
        }
    });
    
});

var global_crrent_page = 0;
var global_total_page = 0;
var data_builder = {
    init: function() {
        $.template('item_template', this._item_list_template);
        global_total_page = this._get_totalpage(this.item_pagesize,global_item_count);
    },
    item_pagesize: 3,
    get_item_list: function() {
        var _this = this;
        global_crrent_page++;
        if (global_crrent_page <= global_total_page) {
            var id = global_shop_info.shop_id;
            var item_cat_id = global_item_cat_id;
            $("#scroll_loading_txt").show();
            $.ajax({
                url: mobile_url + 'shop/get_item_list',
                type: 'GET',
                dataType: 'json',
                data: {
                    id: id,
                    item_cat_id: item_cat_id,
                    pagesize: _this.item_pagesize,
                    page:global_crrent_page
                },
                cache:false,
                success: function(data) {
                    if (data.success == true) {
                    	global_total_page = data.data.retHeader.totalpg;
                        var list = data.data.reqdata;
                        $.tmpl('item_template', list).appendTo('#hot_ul');
                    } else {
                        alert(data.message);
                    }
                    $("#scroll_loading_txt").hide();
                }
            });
        }
    },
    _get_totalpage: function(pagesize, count) {
        if (count <= pagesize) {
            return 1;
        } else {
            return parseInt(count / pagesize) + (count % pagesize == 0 ? 0 : 1);
        }
    },
    _item_list_template: '<li class="i_li left">' +
        '<a href="/item/${item_id}.html"><img src="' + base_v + 'v1/images/placeholder_list.png">' +
        '  <div class="i_li_img_div abs wrap">' +
        '    <div class="i_li_img_div_inner"><span class="sellerOut"><img src="${goods_image_url}"></span></div>' +
        '  </div>' +
        '  <p class="i_txt">${item_name}</p>' +
        '  <p class="i_pri_wrap"><span class="i_pri">¥${item_price}</span><span class="i_pri_discount hide">¥${item_price}</span></p><em class="i_li_discount abs hide">7.1折</em></a>' +
        '</li>'
};
