$(function() {
    data_builder.init();
    data_builder.get_goods_list();

    $(window).scroll(function() { //内容懒加载
        if ($(document).height() <= parseFloat($(window).height()) + parseFloat($(window).scrollTop()) && global_crrent_page < global_total_page) {     
            data_builder.get_goods_list();

        }
    });    
    
});

var global_crrent_page = 0;
var global_total_page = 1; //总页数
var data_builder = {
    init: function() {
        $.template('goods_template', this._goods_list_template);
        global_total_page = this._get_totalpage(this.goods_pagesize,global_goods_count);
    },
    goods_pagesize: pagesize,
    get_goods_list: function() {
        global_crrent_page++;
        //处理后退功能
        var title = '银品惠';
        var url = mobile_url+'shop/goodslist?id=46&goods_cat_id='+global_goods_cat_id;
        var state = {title:title,url:url};      
        if ( global_crrent_page == 1 ) {
            //history.pushState(state, title, url);         
        } else {
            global_crrent_page = global_crrent_page < p ? p : global_crrent_page;
            var url = mobile_url+'shop/goodslist?id=46&goods_cat_id='+global_goods_cat_id+'&p='+global_crrent_page;
            var state = {title:title,url:url};      
            history.replaceState(state, title, url);
        }

        if (global_crrent_page > global_total_page) {                        
            $("#scroll_loading_txt").hide();
            return true;
        }        
        var _this = this;        
        
        var id = global_shop_info.shop_id;
        var goods_cat_id = global_goods_cat_id;
        $("#scroll_loading_txt").show();
        $.ajax({
            url: mobile_url + 'shop/get_goods_list',
            type: 'GET',
            dataType: 'json',
            data: {
                id: id,
                goods_cat_id: goods_cat_id,
                pagesize: global_crrent_page>1 ? 10 : _this.goods_pagesize,                
                page:global_crrent_page,
                query: global_query
            },
            cache:false,
            success: function(data) {
                if (data.success == true) {
                    if ( data.data.retHeader.totalput == 0 ) {
                        $("#scroll_loading_txt").hide();
                        return true;
                    }                    
                	global_total_page = data.data.retHeader.totalpg;
                    var list = data.data.reqdata;
                    $.tmpl('goods_template', list).appendTo('#hot_ul');
                    //处理后退的位置
                    data_builder._bindAhref();
                    if ( global_crrent_page == 1 ) {
                        //location.hash="li_"+last_id;      
                        if ( y > 0 ) {
                            $("html,body").animate({scrollTop: y}, 300);                        
                        }
                        if ( p > 1 ) {
                            global_crrent_page = p;
                        }
                    }                    
                } else {
                    alert(data.message);
                }
                $("#scroll_loading_txt").hide();
            }
        });        
    },
    _bindAhref: function(){     
        $('#hot_ul a').bind('click',function(){            
            var top = $(document).scrollTop();//$(this).offset().top;
            //console.log(top);
            var title = '银品惠';            
            var url = mobile_url+'shop/goodslist?id=46&goods_cat_id='+global_goods_cat_id+'&p='+global_crrent_page+'&y='+top;
            var state = {title:title,url:url};      
            history.replaceState(state, title, url);        
            return true;
        });
    },    
    _get_totalpage: function(pagesize, count) {
        if (count <= pagesize) {
            return 1;
        } else {
            return parseInt(count / pagesize) + (count % pagesize == 0 ? 0 : 1);
        }
    },
    _goods_list_template: '<li class="i_li left">' +
        '<a href="'+mobile_url+'goods/${goods_id}.html">' +
        '  <div class="i_li_img_div abs wrap">' +
        '    <div class="i_li_img_div_inner"><span class="sellerOut"><img src="${goods_image_url}"></span></div>' +
        '  </div>' +
        '  <p class="i_txt">${goods_name}</p>' +
        '  <p class="i_pri_wrap"><span class="i_pri">¥${goods_price}</span><span class="i_pri_discount hide">¥${price_source}</span></p><em class="i_li_discount abs hide">7.1折</em></a>' +
        '</li>'
};
