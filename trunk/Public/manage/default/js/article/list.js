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
		$("#search_button").click(function() {			
			searchParameter.query = $('#txt_keyword').val();							
			searchParameter.cat_id = $('#cat_id').val();
			search.getArticleList();
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
		$('#tbody_list').html('');
		$('#order_list_loading').show();
		$.ajax({
			type: "GET",
			url: php_self + "?m=article.get_list&r=" + Math.random(),
			dataType: 'json', //接受数据格式            
			data: dataInfo,
			cache: false,
			success: function(result) {
				if (result.success == false) {
					M._alert(result.message);
				} else {
					$('#order_list_loading').hide();
					$('#tbody_list').html(searchSelf.buildOrderList(result.data.reqdata));					
					searchSelf.bindArticleList(result.data);
				}
			}
		});
		return false;
	}

	/**
	 * 绑定一些 list dom生成后的操作
	 */
	this.bindArticleList = function(param) {
		var page = param.retHeader.page;
		var count = param.retHeader.totalput;
		$('html,body').animate({
			scrollTop: $('#condition_list').offset().top
		}, 'fast');
		searchSelf.pageDefault == 0 && searchSelf.pageDefault++;
		searchSelf.deleteDom();
	}

	this.getArticleList = function() {
		var dataInfo = searchParameter;
		dataInfo.page = 1;
		//菊花转
		$('#tbody_list').html('');
		$('#order_list_loading').show();
		$('#order_list_nofund').hide(); //隐藏order_list_nofund先        
		$.ajax({
			type: "GET",
			url: php_self + "?m=article.get_list",
			dataType: 'json', //接受数据格式            
			data: dataInfo,
			cache: false,
			success: function(result) {
				if (result.success == false) {
					alert(result.message);
				} else {
					$('#order_list_loading').hide();
					$('#tbody_list').html(searchSelf.buildOrderList(result.data.reqdata));					
					searchSelf.bindArticleList(result.data);
					//隐藏loading

					if (result.data.retHeader.totalput == 0) {
						$('#tbody_list').html('');
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

	this.buildOrderList = function(dataList) {
		var tbody_html = "";
		for (var i = 0; i < dataList.length; i++) {
			var data_info = dataList[i];
			tbody_html += '<tr id="article_id_' + data_info.article_id + '">';
			tbody_html += '<td class="td_left"></td>';
			tbody_html += '	<td class="am-text-middle am-text-sm">' + data_info.article_id + '</td>';
			tbody_html += '	<td class="am-text-left am-text-sm">' + data_info.title + '</td>';
			tbody_html += '	<td class="am-text-middle am-text-sm">' + data_info.channeltype + '</td>';
			tbody_html += '	<td class="am-text-middle am-text-sm">' + data_info.cat_name + '</td>';
			tbody_html += '	<td class="am-text-middle am-text-sm">' + data_info.time + '</td>';
			tbody_html += '	<td class="am-text-middle am-text-sm">' + data_info.edit_time + '</td>';
			tbody_html += ' <td>';
			tbody_html += ' <a class="am-btn am-btn-default am-btn-xs am-text-secondary am-fl am-margin-right-xs" target="_blank" href="'+php_self+'?m=article.add&id='+data_info.article_id+'"><span class="am-icon-pencil-square-o am-padding-left-xs"></span>编辑</a>';
			tbody_html += ' <a style="cursor:pointer" class="am-btn am-btn-default am-btn-xs am-text-danger am-hide-sm-only am-fl a_del delete" data-id="'+data_info.article_id+'"><span class="am-icon-trash-o am-padding-left-xs"></span>删除</a>';
			tbody_html += '</td>';			
			tbody_html += '</tr>';
		}
		return tbody_html;
	}

	this.deleteDom = function() {		
	    $(".am-icon-square-o").bind('click',function() {
	        $(this).addClass("am-hide");
	        $(this).prev(".am-icon-check-square-o").removeClass("am-hide");
	    });
	    $(".am-icon-check-square-o").bind('click',function() {
	        $(this).addClass("am-hide");
	        $(this).next(".am-icon-square-o").removeClass("am-hide");
	    });	
		$('#tbody_list .delete').bind('click',function(){
			$('#confirm_content').html('确定要删除这条记录吗？');
			$('#my-confirm').modal({
				relatedTarget: this,
				onConfirm: function(options) {					
					var id = $(this.relatedTarget).attr('data-id');					
					var dataParam = {
						'action':'del',
						'id':id
					}					
			        $.ajax({
			            url: php_self + '?m=article.batch',
			            type: 'GET',
			            dataType: 'json',
			            data: dataParam,
			            cache:false,
			            success: function(data) {
			                if (data.success == true) {	                		                    
			                    $('#article_id_'+id).remove();
			                } else {
			                    M._alert(data.message);	                    
			                }
			            }
			        });									  
				},
				// closeOnConfirm: false,
				onCancel: function() {
				  
				}
			});			
		
		});
	}
	this.batch = function() {
		$(".cbk_del_all").click(function() {			
			var ids = search.getids();					
			var id_array = ids.split(',');					
			if ( ids == false ) {
				var msg = '啥也没选择啊';
				M._alert(msg);
				return false;	
			}						
			var msg = '确认要删除' +id_array.length+'个吗?';				  							
			$('#confirm_content-batch').html(msg);
			$('#my-confirm-batch').modal({
				relatedTarget: this,
				onConfirm: function(options) {				  															
	                $.ajax({
	                    type: "post",
	                    url: php_self + "?m=article.batch",
	                    data: {
	                        'do': "del",
	                        'id_a': ids
	                    },
	                    cache: false,
	                    success: function(data) {
	                        if (data.success == true) {
	                            for ( var i=0;i<id_array.length;i++ ) {
	                            	var id = id_array[i];
									$('#uid_'+id).remove();
	                            }
	                        } else {
	                            M._alert(data.message);
	                        }
	                    }
	                });				  	
				},
				// closeOnConfirm: false,
				onCancel: function() {
				  
				}
			});
	    });		
	}
	    //获取所选中的checkbox中的 item_ID
    this.getids = function() {
    	//return '2,3,4';
        var cbk = $("table .am-icon-check-square-o");        
        var ids = "";
        for (var i = 0; i < cbk.length; i++) {
            var cbk_no = $(".am-icon-check-square-o").eq(i);
            var cbk_ed = $(".am-icon-square-o").eq(i);
            if (cbk_ed.hasClass("am-hide")) {
                ids += cbk_no.attr("data-item-id") + ",";
            }
        }
        if (ids == "") {
            M._alert("还没有选择产品");
            return false;
        }
        return ids.substr(0, ids.length - 1);
    }	
}