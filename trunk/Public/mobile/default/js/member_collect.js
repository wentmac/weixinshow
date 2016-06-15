var global_current_page = 0;
var global_max_page = 0;

$(function() {
    data_builder.init(); //初始化dataBuilder

    var type = global_type;
    type = type == null ? 'goods' : type;
    data_builder.current_type = type;
    data_builder.get();

    $(window).scroll(function() { //内容懒加载
        if ($(document).height() - 50 <= parseFloat($(window).height()) + parseFloat($(window).scrollTop()) && global_current_page < global_max_page) {
            data_builder.get();
        }
    });
});


var data_builder = {
    init: function(c) {
        $.template('item_template', this._item_teamplate);
        $.template('shop_template', this._shop_teamplate);
    },
    current_type: 'goods',
    item_pagesize: 6, //商品一页显示多少条v
    shop_pagesize: 10, //店铺一页显示多少条
    get: function() {
        if (this.current_type == 'goods') {
            this.get_item();
        } else {
            this.get_shop();
        }
    },
    get_item: function() {
        global_current_page++;
    	var _this = this;
        _this._loading.show();
        $.ajax({
            url: mobile_url + '/member/collect.item',
            type: 'GET',
            dataType: 'json',
            data: {
                pagesize: _this.item_pagesize,
                page: global_current_page
            },
            cache:false,
            success: function(data) {
                if (data.success == true) {
                    global_max_page = data.data.retHeader.totalpg;
                    $.tmpl('item_template', data.data.reqdata).appendTo('#favproduct ul');
                } else {
                    alert(data.message);
                }
                _this._loading.hide();
            }
        })

    },
    get_shop: function() {
        global_current_page++;
        var _this = this;
         _this._loading.show();
       $.ajax({
            url: mobile_url + '/member/collect.shop',
            type: 'GET',
            dataType: 'json',
            data: {
                pagesize: _this.shop_pagesize,
                page: global_current_page
            },
            cache:false,
            success: function(data) {
                if (data.success == true) {
                    global_max_page = data.data.retHeader.totalpg;
                    $.tmpl('shop_template', data.data.reqdata).appendTo('#favshop');
                } else {
                    alert(data.message);
                }
                _this._loading.hide();
            }
        })
    },
    _loading: $("#scroll_loading_txt"),
    _item_teamplate: '<li class="i_li left">' +
        '<a href="/item/${item_id}.html"><img src="' + base_v + 'v1/images/placeholder_list.png">' +
        '  <div class="i_li_img_div abs wrap">' +
        '    <div class="i_li_img_div_inner"><span class="sellerOut"><img src="${goods_image_url}"></span></div>' +
        '  </div>' +
        '  <p class="i_txt">${item_name}</p>' +
        '  <p class="i_pri_wrap"><span class="i_pri">¥${item_price}</span></p></a>' +
        '</li>',
    _shop_teamplate: '<ul class="favshop">' +
        '  <li><a href="/shop/${item_uid}" class="shoplink">' +
        '  <span><img src="${shop_image_url}"></span><p class="">${shop_name}</p></a></li>' +
        '</ul>'
}
