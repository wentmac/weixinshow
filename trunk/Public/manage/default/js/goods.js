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
function setGoodsImageId() {
	$(".js-set-image-id").bind("click", function() {
		$("#set_goods_image_id").val($(this).attr('data-image-id'));
		$('.js-set-image-id').css("background-color", "rgba(153, 153, 153, 0.6)");
		$(this).css("background-color", "#dd514c");
		$('.js-set-image-id span').addClass("am-icon-flag-o");
		$('.js-set-image-id span').removeClass("am-icon-flag-checkered");
		$(this).find('span').removeClass("am-icon-flag-o");
		$(this).find('span').addClass("am-icon-flag-checkered");
	});
}


jQuery.fn.rowspan = function(colIdx) { //封装的一个JQuery小插件
	return this.each(function() {
		var that;
		$('tr', this).each(function(row) {
			$('td:eq(' + colIdx + ')', this).filter(':visible').each(function(col) {
				if (that != null && $(this).html() == $(that).html()) {
					rowspan = $(that).attr("rowSpan");
					if (rowspan == undefined) {
						$(that).attr("rowSpan", 1);
						rowspan = $(that).attr("rowSpan");
					}
					rowspan = Number(rowspan) + 1;
					$(that).attr("rowSpan", rowspan);
					$(this).hide();
				} else {
					that = this;
				}
			});
		});
	});
}

function changeGoodsType(goods_type){
	if ( goods_type == 2 ) {
		$('#goods_member_level_div').removeClass('hide');
	} else {
		$('#goods_member_level_div').addClass('hide');
	}	
}

$(document).ready(function() {
	var goods_type = $('#goods_type').val();		
	changeGoodsType(goods_type);

	$('#goods_type').change(function(){
		var goods_type = $(this).val();		
		changeGoodsType(goods_type);
	});

	$("#goods_name").blur(function() {
		if ($("#goods_id").val() != "0") {
			goods_id_name.goods_id = $("#goods_id").val();
		}
		goods_id_name.goods_name = $(this).val();
		$.ajax({
			type: "post",
			url: php_self + "?m=goods.check_goods_name_repeat",
			data: goods_id_name,
			cache: false,
			success: function(data) {
				if (data.success == true) {
					$("#sm_goods_name").removeClass("am-text-warning").addClass("am-text-success");
					$("#sm_goods_name").html("可以使用");
				} else {
					$("#sm_goods_name").removeClass("am-text-success").addClass("am-text-warning");
					$("#sm_goods_name").html("此产品名称已经存在");
					return false;
				}
			}
		});
	}).focus(function() {
		$("#sm_goods_name").removeClass("am-text-success").removeClass("am-text-warning").addClass("am-text-primary");
		$("#sm_goods_name").html("请输入产品名称");
	});
	
	var manualuploader = $('#fine-uploader').fineUploader({
			autoUpload: true,
			template: "qq-simple-thumbnails-template",
			request: {
				endpoint: index_url + php_self + '?m=tool.upload_image_by_ajax&filename=qqfile&action=goods'
			},
			// optional feature
			deleteFile: {
				enabled: true,
				method: "POST",
				endpoint: index_url + php_self + "?m=tool.delete_image_by_ajax"
			},
			thumbnails: {
				timeBetweenThumbs: 0
			},
			validation: {
				allowedExtensions: ['jpeg', 'jpg', 'gif', 'png']
			},
			messages: {
				noFilesError: '没有需要上传的图片'
			},
			retry: {
				enableAuto: false
			}
		})
		.on('complete', function(event, id, name, responseJSON, xhr) {
			if (responseJSON.success == true) {
				//图片数组中增加新传的
				postField.image_array.push(responseJSON.newUuid);
				$(".js-set-image-id").eq(postField.image_array.length - 1).attr("data-image-id", responseJSON.newUuid);
				setGoodsImageId();
			}
		})
		.on('deleteComplete', function(event, id, xhr, isError) {
			responseJSON = eval("(" + xhr.response + ")");
			if (responseJSON.success == true) {
				//图片数组中增加新传的
				responseJSON.uuid.replace("\/", "/");
				postField.image_array.remove(responseJSON.uuid);
			}
		})
		.on('error', function(event, id, name, errorReason, xhrOrXdr) {
			//alert(qq.format("Error on file number {} - {}.  Reason: {}", id, name, errorReason));				
		})
		.on('totalProgress', function(event, totalUploadedBytes, totalBytes) {

			/**
			Math.round(uploadedBytes /
			totalBytes * 100) + '%';		
		*/
			var progressPercent = (totalUploadedBytes / totalBytes).toFixed(2);
			if (isNaN(progressPercent)) {
				$('#qq-total-progress-bar').css('width', '0%');
			} else {
				var progress = (progressPercent * 100).toFixed() + '%';
				$('#qq-total-progress-bar').css('width', progress);
			}
		});

	//  $('#triggerUpload').click(function() {
	//      manualuploader.fineUploader('uploadStoredFiles');
	//  });




	//商品规格选择下拉选择事件
	goods.spec_choice();
	//绑定商品规格类型选择时的下拉事件
	spec_event_bind.select_spec_add_autocomplete();
	//绑定商品规格值添加的popover窗口
	spec_event_bind.a_spec_value_add_popover();
	//绑定规格属性删除事件
	spec_event_bind.spec_atom_delete();
	//添加规格项目添加
	spec_event_bind.btn_add_spec_group();
	//添加规格项目删除
	spec_event_bind.btn_delete_spec_group();
	//重组数组
	goods_spec_stock.init();
	//在onClose事件，和删除事件中 重新生成商品库存的table dom
	goods_spec_stock.createNode();
	//默认展示的图片类 编辑时
	goods_image.init();

	//todo 库存stock默认数据展示

	$.each($('#sku-region div.sku-sub-group'), function(i, n) {
		var spec_id = $(n).find('.js-sku-name[name=spec_name]').val();
		if (spec_id > 0) {
			$(n).find('.js-add-sku-atom').attr('data-content', goods.createSpecValueDom(spec_id).html());
		}
	});

	/*保存商品*/
	$("#submit_i_do_item").bind("click", function() {
		$.AMUI.progress.start();
		var that = $(this);
		//提交后必选校验		
		if (!check_required_options.init($(this))) {
			$.AMUI.progress.done();
			return false;
		}
		var dataParam = postField;
		$.ajax({
			type: "POST",
			url: index_url + php_self + '?m=goods.save',
			dataType: "json",
			data: dataParam,
			cache: false,
			success: function(data) {
				//console.log(data);
				if (data.success == true) {
					M._alert('商品更新成功');
					that.removeClass("isLoading").html("提交");
					$.AMUI.progress.done();
				} else {
					$.AMUI.progress.done();
					M._alert(data.message);
					that.removeClass("isLoading").html("提交");
					$.AMUI.progress.done();

					return false;
				}
			}
		});

	});


});

/**
 * 默认展示的图片类
 */
var goods_image = {
	init: function() {
		var that = this;
		var span = $('#fine-uploader .qq-upload-list-selector');

		for (var i = 0, len = postField.image_array.length; i < len; i++) {
			var goods_image_id = postField.image_array[i];
			var goods_image = goods_image_array[i];
			var li = $('<li>').addClass('upload-preview-img sort').attr({
				'goods_image': 'edit'
			});
			$('<img>').addClass('qq-thumbnail-selector').attr({
				'qq-max-size': 80,
				'src': goods_image
			}).appendTo(li);
			$('<a>').addClass('close-modal small js-remove-image  qq-upload-delete-selector').attr({
				'href': 'javascript:;',
				'data-image-id': goods_image_id
			}).html('x').appendTo(li);

			$('<a style="top: 15px;">').addClass('close-modal small js-set-image-id').attr({
				'href': 'javascript:;',
				'data-image-id': goods_image_id
			}).html('<span class="am-icon-flag-o am-text-xs"></span>').appendTo(li);


			li.appendTo(span);
		}
		$('.js-set-image-id').off('click').on('click', function(event) {

			$("#set_goods_image_id").val($(this).attr('data-image-id'));
			$('.js-set-image-id').css("background-color", "rgba(153, 153, 153, 0.6)");
			$(this).css("background-color", "#dd514c");


			$('.js-set-image-id span').addClass("am-icon-flag-o");
			$('.js-set-image-id span').removeClass("am-icon-flag-checkered");
			$(this).find('span').removeClass("am-icon-flag-o");
			$(this).find('span').addClass("am-icon-flag-checkered");

		});

		$('#fine-uploader .qq-upload-list-selector li[goods_image="edit"] .js-remove-image').off('click').on('click', function(event) {
			event.stopImmediatePropagation();
			event.preventDefault();
			var goods_image_id = $(this).attr('data-image-id');
			//ajax todo delete ImageId {goods_id,goods_image_id}//进行权限校验
			that._ajaxDeleteImage(goods_image_id, $(this).parent());
		});



	},
	_ajaxDeleteImage: function(goods_image_id, li_ele) {
		var dataParam = {
			'goods_id': $('#goods_id').val(),
			'goods_image_id': goods_image_id
		};
		$.ajax({
			type: "GET",
			url: index_url + php_self + '?m=goods.deleteGoodsImage',
			dataType: "json",
			data: dataParam,
			async: false,
			success: function(data) {
				if (data.success == true) {
					li_ele.remove();
					postField.image_array.remove(data.data.goods_image_id);
				} else {
					M._alert(data.message);
					return false;
				}
			}
		});
	}
}

var isIe = navigator.userAgent.toLowerCase().match(/msie ([\d.]+)/) ? true : false;
if (isIe) {
	var position_top = 0;
} else {
	var position_top = -23;
}
var specTreeArray = $.map(global_param.spec_tree, function(n) {
	return {
		value: n.spec_name,
		data: n.spec_id
	};
});
//商品规格的事件绑定
var spec_event_bind = {
	//popover框内的dom
	dom: null,
	//sku展示块内的dom
	sku_atom_container_dom: null,
	spec_object: null,
	js_add_sku_atom_dom: null,
	//绑定增加新的商品规格
	btn_add_spec: function(spec_name) {
		var that = this;
		//ajax server save
		var dataParam = {
			'spec_name': spec_name
		};

		if (dataParam.spec_name == '') {
			return false;
		}
		$.ajax({
			type: "POST",
			url: index_url + php_self + '?m=seller/goods.add_spec',
			dataType: "json",
			data: dataParam,
			async: false,
			success: function(data) {
				if (data.success == true) {
					//global_param中增加数据
					var spec_object = {};
					var spec_id = data.data.spec_id.toString();
					spec_object.spec_id = spec_id;
					spec_object.spec_name = data.data.spec_name;
					spec_object.value_list = [];
					global_param.spec_tree.push(spec_object);
					global_value[spec_id] = new Array();
					goods.spec_complete_callback(spec_id, data.data.spec_name);
				} else {
					M._alert(data.message);
					return false;
				}
			}
		});
	},
	//绑定商品规格类型选择时的下拉事件
	select_spec_add_autocomplete: function() {
		$('#i_do_wrap .select2-focusser').autocomplete({
			// serviceUrl: '/autosuggest/service/url',		
			lookup: specTreeArray,
			minChars: 0,
			triggerSelectOnValidInput: false,
			isShowInputValue: true,
			autoSelectFirst: true,
			position: {
				top: position_top,
				left: 0
			},
			onSelect: function(suggestion) {
				goods.spec_complete_callback(suggestion.data, suggestion.value);
				console.log('You selected: ' + suggestion.value + ', ' + suggestion.data);
			},
			onHide: function(container) {
				goods.spec_close();
			}
		});
	},
	a_spec_value_add_popover: function() {
		//添加规格值
		$('#i_do_wrap .js-add-sku-atom').webuiPopover({
			placement: 'auto',
			trigger: 'click',
			width: '400px',
			cache: false,
			closeable: true,
			bodyClickHide: true,
			afterLoad: function(popover_dom) {
				popover_dom.find('.select2-input').focus();
				spec_event_bind.dom = popover_dom;
				spec_event_bind.sku_atom_container_dom = $(this).parent().find('.js-sku-atom-list');
				spec_event_bind.js_add_sku_atom_dom = $(this);
				spec_event_bind.btn_add_spec_value();
				//绑定初始化checked的事件
				spec_event_bind.spec_value_checked_reset();
				//监听取消checkbox选择的事件
				spec_event_bind.spec_value_unchecked();
			},
			onClose: function(popover_dom) {
				popover_dom.find('.select2-input').focus();
				spec_event_bind.dom = popover_dom;
				spec_event_bind.sku_atom_container_dom = $(this).parent().find('.js-sku-atom-list');
				spec_event_bind.js_add_sku_atom_dom = $(this);
				spec_event_bind.spec_value_checked();
				//绑定规格属性删除事件
				spec_event_bind.spec_atom_delete();
				goods_spec_stock.createNode();
			}
		});
	},
	//绑定商品规格值新增按钮
	btn_add_spec_value: function() {
		var that = this;
		this.dom.find('.js-btn-confirm').off('click').on('click', function() {
			//ajax server save
			var input_element = that.dom.find('.select2-input');
			var dataParam = {
				'spec_id': input_element.attr('data-spec-id'),
				'spec_value_name': input_element.val()
			};

			if (dataParam.spec_value_name == '') {
				input_element.css({
					'border': '1px solid #f00'
				}).focus();
				return false;
			}
			//ajax前判断global_param中是否已经存在
			var spec_value_name_exist = that._check_spec_value_name_exist(dataParam.spec_id, dataParam.spec_value_name);
			if (spec_value_name_exist == false) {
				$.ajax({
					type: "POST",
					url: index_url + php_self + '?m=seller/goods.add_spec_value',
					dataType: "json",
					data: dataParam,
					success: function(data) {
						if (data.success == true) {
							that._add_spec_dom(data.data.spec_id, data.data.spec_value_id, data.data.spec_value_name);
							input_element.val('');
						} else {
							M._alert(data.message);
						}
					}
				});
			} else {
				var ul = that.dom.find('.sku-list');
				ul.find("input[value='" + spec_value_name_exist.spec_id + ":" + spec_value_name_exist.spec_value_id + "']").prop('checked', true);
			}
		});
	},
	//绑定初始化checked的事件
	spec_value_checked_reset: function() {
		var that = this;
		this.dom.find('.J_Checkbox').each(function(i, n) {
			var data = $(this).val().split(':');
			var spec_id = data[0];
			var spec_value_id = data[1];
			if (global_value && global_value[spec_id] != undefined && $.inArray(spec_value_id, global_value[spec_id]) >= 0) {
				$(this).prop('checked', true);
			} else {
				$(this).prop('checked', false);
			}
		});
	},
	//检测新增加的spec_value_name是否存在
	_check_spec_value_name_exist: function(spec_id, spec_value_name) {
		var spec_object = $.grep(global_param.spec_tree, function(n, i) {
			return n.spec_id == spec_id;
		});
		this.spec_object = spec_object;
		if (spec_object[0].value_list.length == 0) {
			spec_object[0].value_list = [];
			return false;
		} else {
			var spec_value_object = null;
			$.each(spec_object[0].value_list, function(i, n) {
				if (n.spec_value_name == spec_value_name) {
					spec_value_object = n;
				}
			});
			if (spec_value_object == null) {
				return false;
			}
			return spec_value_object;
			//console.log(spec_value_object);
		}
	},
	//增加spec的LI dom
	_add_spec_dom: function(spec_id, spec_value_id, spec_value_name) {
		var that = this;
		var ul = this.dom.find('.sku-list');
		var li = $("<li>").addClass('sku-item');
		spec_id = spec_id.toString();
		spec_value_id = spec_value_id.toString();
		//<input name="cp_12304035" class="J_Checkbox" id="prop_12304035-21484" type="checkbox" checked="" value="12304035:21484">
		var input = $("<input>").addClass("J_Checkbox").attr({
			name: 'cp_' + spec_value_id,
			id: 'prop_' + spec_id + '_' + spec_value_id,
			type: 'checkbox',
			'checked': true
		}).val(spec_id + ':' + spec_value_id);
		//<label title="128MB" class="labelname" for="prop_12304035-21484">128MB</label>		 
		var label = $("<label>").addClass('labelname').attr({
			title: spec_value_name,
			'for': 'prop_' + spec_id + '_' + spec_value_id
		}).text(spec_value_name);
		input.appendTo(li);
		label.appendTo(li);
		li.appendTo(ul);
		//global_param 中spec_tree中增加新增加的对象	
		var spec_value_list_object = {
			'spec_id': spec_id,
			'spec_value_id': spec_value_id,
			'spec_value_name': spec_value_name
		}
		that.spec_object[0].value_list.push(spec_value_list_object);

		if (global_value[spec_id] == undefined) {
			global_value[spec_id] = new Array();
		}

		if ($.inArray(spec_value_id, global_value[spec_id]) < 0) {
			global_value[spec_id].push(spec_value_id);
		}
		//data-content 里的数据增加
		that.js_add_sku_atom_dom.attr('data-content', goods.createSpecValueDom(spec_id).html());
	},
	//show spec item add or less
	spec_value_checked: function() {
		var that = this;
		this.sku_atom_container_dom.html('');
		this.dom.find('.J_Checkbox:checked').each(function() {
			that._sku_atom_add($(this).val());
		});
	},
	//show spec item add
	_sku_atom_add: function(value) {
		var data = value.split(':');
		var spec_id = data[0];
		var spec_value_id = data[1];

		if (global_value && global_value[spec_id] == undefined) {
			global_value[spec_id] = new Array();
		}

		if (global_value && $.inArray(spec_value_id, global_value[spec_id]) < 0) {
			global_value[spec_id].push(spec_value_id);
		}

		var spec_object = $.grep(global_param.spec_tree, function(n, i) {
			return n.spec_id == spec_id;
		});
		var spec_value_object = null;
		$.each(spec_object[0].value_list, function(i, n) {
			if (n.spec_value_id == spec_value_id) {
				spec_value_object = n;
			}
		});
		if (spec_value_object != null) {
			var spec_value_name = spec_value_object.spec_value_name;
		} else {
			var spec_value_name = '';
		}
		var sku_atom_div = $('<div>').addClass('sku-atom');
		var span = $('<span>').attr({
			'data-atom-id': value
		}).html(spec_value_name);
		var close_div = $('<div>').addClass('close-modal small js-remove-sku-atom').html('x');
		span.appendTo(sku_atom_div);
		close_div.appendTo(sku_atom_div);

		sku_atom_div.appendTo(this.sku_atom_container_dom);

	},
	//监听取消checkbox选择的事件
	spec_value_unchecked: function() {
		this.dom.find('.J_Checkbox').click(function() {
			if (!$(this).is(':checked')) {
				var data = $(this).val().split(':');
				var spec_id = data[0];
				var spec_value_id = data[1];
				//取消 global_value里的值				
				if (global_value && global_value[spec_id] != undefined && $.inArray(spec_value_id, global_value[spec_id]) >= 0) {
					global_value[spec_id].splice(jQuery.inArray(spec_value_id, global_value[spec_id]), 1);
				}
			}
		});
	},
	//绑定规格属性删除事件
	spec_atom_delete: function() {
		var that = this;
		$('.js-sku-atom-list .js-remove-sku-atom').off('click').on('click', function() {
			var data = $(this).prev().attr('data-atom-id');
			data = data.split(':');
			var spec_id = data[0];
			var spec_value_id = data[1];
			global_value[spec_id].splice(jQuery.inArray(spec_value_id, global_value[spec_id]), 1);
			$(this).parent().remove();
			goods_spec_stock.createNode();
		});
	},
	//绑定规格项目监听和删除
	btn_add_spec_group: function() {
		var that = this;
		$('.js-sku-group-opts .js-add-sku-group').off('click').on('click', function() {
			if (that._check_btn_add_spec_group() == false) {
				return false;
			}
			var div = $('<div>').addClass('sku-sub-group');
			var h3_sku_group_title = $('<h3>').addClass('sku-group-title');
			var div_select2_container = $('<div>').addClass('select2-container js-sku-name').css('width', '100px');
			var a_select2_choice = $('<a>').addClass('select2-choice').attr({
				'onclick': 'return false;',
				'href': 'javascript:void(0)'
			});
			$('<span>').addClass('select2-chosen').appendTo(a_select2_choice);
			$('<span>').addClass('select2-search-choice-close').appendTo(a_select2_choice);
			$('<span>').addClass('select2-arrow').html('<b></b>').appendTo(a_select2_choice);
			a_select2_choice.appendTo(div_select2_container);
			$('<input>').addClass('select2-focusser select2-offscreen').attr({
				'type': 'text',
				'autocomplete': 'off'
			}).css('display', 'none').appendTo(div_select2_container);
			div_select2_container.appendTo(h3_sku_group_title);
			$('<input>').addClass('js-sku-name select2-offscreen').attr({
				'type': 'hidden',
				'autocomplete': 'off',
				'name': 'spec_name'
			}).appendTo(h3_sku_group_title);
			$('<a>').addClass('js-remove-sku-group remove-sku-group').html('x').appendTo(h3_sku_group_title);
			h3_sku_group_title.appendTo(div);

			var div_js_sku_atom_container = $('<div>').addClass('js-sku-atom-container sku-group-cont');
			var div_div = $('<div>');
			$('<div>').addClass('js-sku-atom-list sku-atom-list').appendTo(div_div);
			$('<a>').addClass('js-add-sku-atom add-sku').attr({
				'data-title': '',
				'data-content': '',
				'href': 'javascript:;'
			}).html('+添加').hide().appendTo(div_div);
			div_div.appendTo(div_js_sku_atom_container);
			div_js_sku_atom_container.appendTo(div);

			div.appendTo($('.js-sku-list-container'));
			that._check_btn_add_spec_group();
			that.btn_delete_spec_group();
			goods.spec_choice();
			that.select_spec_add_autocomplete();
			div.find('.select2-choice').click();

		});
	},
	btn_delete_spec_group: function() {
		var that = this;
		$('.js-remove-sku-group').off('click').on('click', function() {
			var sku_sub_group_dom = $(this).parent().parent();
			sku_sub_group_dom.remove();
			that._check_btn_add_spec_group();
			//删除global_value中的数据
			var spec_id = sku_sub_group_dom.find('[name=spec_name]').val();
			if (spec_id > 0) {
				delete(global_value[spec_id]);
			}
			goods_spec_stock.createNode();
		});
	},
	//检测规格项目数量是否够了
	_check_btn_add_spec_group: function() {
		var spec_group_count = $('.js-sku-list-container .sku-sub-group').length;
		if (spec_group_count > 2) {
			$('.js-sku-group-opts').hide();
			return false;
		} else {
			$('.js-sku-group-opts').show();
			return true;
		}

	}

}

//校验必选
var check_required_options = {
	init: function(_this) {
		if (_this.hasClass("isLoading")) {
			return false;
		}
		_this.addClass("isLoading").html("loading...");

		//图片
		if (!postField.image_array.length) {
			M._alert("请至少上传一张商品图片");
			$("#goods_name").focus();
			_this.removeClass("isLoading").html("提交");
			return false;
		}

		//商品名
		var goods_name = $('#goods_name').val();
		if (!goods_name) {
			M._alert("请填写商品名");
			$('#goods_name').focus();
			_this.removeClass("isLoading").html("提交");
			return false;
		}
		//分类
		//      var goods_cat_id = new Array();
		//      $('#i_cate_wrap input[name="goods_cat_id"]:checked').each(function() {
		//          goods_cat_id.push($(this).val()); //向数组中添加元素
		//      });

		//var goods_cat_id = new Array();
		//goods_cat_id.push($('#goods_cat_id').val());		
		var count = 0;
		for (var i in global_value) {
			count++;
		}
		//设置主图
		if ($("#set_goods_image_id").val() != "") {
			postField.goods_image_id = $('#set_goods_image_id').val();
		}
		postField.goods_name = $('#goods_name').val();
		postField.goods_desc = $('#i_des').val();
		postField.goods_cat_id = $('#goods_cat_id').val();
		postField.goods_id = $('#goods_id').val();
		postField.goods_spec_array = global_value;
		postField.shipping_fee = $("#shipping_fee").val();
		
		postField.promote_price = $("#promote_price").val();
		postField.goods_source = $("#goods_source").val();
		postField.goods_source_id = $("#goods_source_id").val();

		var commission = $("#commission_fee").val();			
		postField.commission_type = 0;
		postField.commission_fee = commission;

		postField.commission_fee_rank = $("#commission_fee_rank").val();
		postField.goods_type = $("#goods_type").val();
		postField.goods_member_level = $("#goods_member_level").val();
		postField.goods_sort = $("#goods_sort").val();
		postField.is_integral = $('#is_integral').val();

		if (count > 0) { //有型号			
			var error_message_status = false;

			$.each($('#stock-region .table-sku-stock input[name="sku_price"]'), function(i, n) {
				var value = $(n).val();
				if (!value || isNaN(value) || value <= 0) {
					var td = $(n).parent();
					td.addClass('manual-valid-error');
					error_message_status = true;
				}
			});
			$.each($('#stock-region .table-sku-stock input[name="stock_num"]'), function(i, n) {
				var value = $(n).val();
				if (!value || isNaN(value) || value <= 0) {
					var td = $(n).parent();
					td.addClass('manual-valid-error');
					error_message_status = true;
				}
			});

			if (error_message_status) {
				M._alert("商品规格型号有没填的啊");
				_this.removeClass("isLoading").html("提交");
				return false;
			}

			//goods_sku_array
			var goods_sku_stock_object = {};
			var small_price=0;
			for (var i = 0, len = goods_spec_stock.spec_assemble.length; i < len; i++) {
				var spec_assemble = goods_spec_stock.spec_assemble[i];
				var dom = $('#stock-region .table-sku-stock tr[goods_sku_id="' + spec_assemble + '"]');
				var sku_price = dom.find('input[name="sku_price"]').val();
				if(i==0){
					small_price=sku_price;
				}else if(sku_price<small_price){
					small_price=sku_price;
				}
				
				var sku_stock = dom.find('input[name="stock_num"]').val();
				var sku_code = dom.find('input[name="code"]').val();

				var object = {};
				object.sku_price = sku_price;
				object.sku_stock = sku_stock;
				object.sku_code = sku_code;

				goods_sku_stock_object[spec_assemble] = object;
			}
			postField.goods_sku_stock = goods_sku_stock_object;

			return true;
		} else { //没有型号		
			//价格
			var i_no_sku_price = $('#i_no_sku_price').val();
			if (!i_no_sku_price) {
				M._alert("请填写商品价钱");
				$('#i_no_sku_price').focus();
				_this.removeClass("isLoading").html("提交");
				return false;
			}
			if (isNaN(i_no_sku_price)) {
				M._alert("商品价钱应该为数字");
				$('#i_no_sku_price').focus();
				_this.removeClass("isLoading").html("提交");
				return false;
			}
			if (i_no_sku_price <= 0) {
				M._alert("商品价格不能为0");
				$('#i_no_sku_price').focus();
				_this.removeClass("isLoading").html("提交");
				return false;
			}
			if (isNaN($("#shipping_fee").val())) {
				M._alert("邮费应该为数字");
				$('#shipping_fee').focus();
				_this.removeClass("isLoading").html("提交");
				return false;
			}
			if (isNaN($("#commission_fee").val())) {
				M._alert("佣金或佣金比例应该为数字");
				$('#commission_fee').focus();
				_this.removeClass("isLoading").html("提交");
				return false;
			}
						
			//库存			
			var i_no_sku_stock = $("#i_no_sku_stock").val();
			if (!i_no_sku_stock) {
				M._alert("请填写商品库存");
				$("#i_no_sku_stock").focus();
				_this.removeClass("isLoading").html("提交");
				return false;
			}
			if (isNaN(i_no_sku_stock)) {
				M._alert("商品库存应该为数字");
				$("#i_no_sku_stock").focus();
				_this.removeClass("isLoading").html("提交");
				return false;
			}
			if (i_no_sku_stock <= 0) {
				M._alert("商品库存不能低于0");
				$("#i_no_sku_stock").focus();
				_this.removeClass("isLoading").html("提交");
				return false;
			}

			var coding_val = $('#coding_val').val();

			postField.goods_price = i_no_sku_price;
			postField.goods_stock = i_no_sku_stock;
			postField.outer_code = coding_val;

			/**
			商家编码
			if(coding_val==''){
				M._alert("请填写商品编号");
				_this.removeClass("isLoading").html("提交");
				return;
			}*/
			return true;

		}
	}
}

var goods = {
	current_choice_dom: null,
	initialize: function() {
		var that = this;
	},
	spec_choice: function() {
		var that = this;
		$('#i_do_wrap .select2-choice').off('click').on('click', function(event) {
			var choice_dom = $(this).parent().parent().parent();
			that.current_choice_dom = choice_dom;
			that.current_choice_dom.find('.select2-focusser').show().val('').focus();
			$(this).hide();
			//$('#i_do_wrap .js-add-sku-atom .close').click();
			event.stopImmediatePropagation();
			event.preventDefault();

			$('div.webui-popover').not('.webui-popover-fixed').removeClass('in').hide();
		});
	},
	spec_close: function() {
		var that = this;
		that.current_choice_dom.find('.select2-choice').show();
		that.current_choice_dom.find('.select2-focusser').hide();
	},
	spec_complete_callback: function(data, value) {
		var that = this;
		//判断是否是新增加的
		if (data == 0) {
			//新增加
			spec_event_bind.btn_add_spec(value);
			return true;
		}
		//判断是否已经选择过
		var this_spec_id = that.current_choice_dom.find('.select2-offscreen[name="spec_name"]').val();
		var _spec_id_exist = false;
		if (this_spec_id != data) {
			$.each($('.sku-sub-group [name="spec_name"]'), function(i, n) {
				if ($(n).val() == data) {
					M._alert('规格名重复了 :-)');
					_spec_id_exist = true;
				}
			});
		}
		if (_spec_id_exist) {
			_this.removeClass("isLoading").html("提交");
			return false;
		}
		that.current_choice_dom.find('.select2-choice').show();
		that.current_choice_dom.find('.js-add-sku-atom').attr('data-title', value).show();
		that.current_choice_dom.find('.select2-chosen').html(value);
		that.current_choice_dom.find('.select2-focusser').val(data).hide();
		that.current_choice_dom.find('.select2-offscreen[name="spec_name"]').val(data);
		that.current_choice_dom.find('.js-add-sku-atom').attr('data-content', that.createSpecValueDom(data).html());
		//重新生成sku-atom 项目		
		spec_event_bind.sku_atom_container_dom = that.current_choice_dom.find('.js-sku-atom-list');
		spec_event_bind.sku_atom_container_dom.html('');


		if (global_value && global_value[data] == undefined) {
			global_value[data] = new Array();
		}

		if (global_value && global_value[data].length > 0) {
			$.each(global_value[data], function(i, n) {
				spec_event_bind._sku_atom_add(data + ':' + n);
			});
		}
		//绑定添加商品规格属性值的事件
		spec_event_bind.a_spec_value_add_popover();
		//单品的 价格 库存 商家编码 隐藏
		//      $('#i_no_sku_price_wrap').hide();
		//      $('#i_no_sku_stock_wrap').hide();
		//      $('#coding').hide();
		$("#product_price,#product_stock,#product_code").addClass("am-hide");

	},
	//当选择了规格后，开始生成规格值
	createSpecValueDom: function(spec_id) {
		var spec_object = $.grep(global_param.spec_tree, function(n, i) {
			return n.spec_id == spec_id;
		});
		var div = $("<div>");
		var ul = $("<ul>").addClass('sku-list');

		//<input type="text" class="select2-input select2-default" id="s2id_autogen12" tabindex="-1" style="width: 230px;">
		var insert_input_div = $('<div>').attr({
			'class': 'insert_input_div'
		});
		insert_input_div.appendTo(div);
		var insert_input = $("<input>").attr({
			'type': 'text',
			'class': 'select2-input select2-default',
			'id': 'insert_input_' + spec_id,
			'data-spec-id': spec_id
		});
		var insert_input_button = $("<input>").attr({
			'type': 'button',
			'class': 'btn btn-primary js-btn-confirm',
			'id': 'insert_button_' + spec_id,
			'value': '新增',
			'data-loading-text'　: '新增'
		});
		insert_input.appendTo(insert_input_div);
		insert_input_button.appendTo(insert_input_div);
		if (spec_object[0].value_list.length > 0) {
			$.each(spec_object[0].value_list, function(i, n) {
				var li = $("<li>").addClass('sku-item');
				//<input name="cp_12304035" class="J_Checkbox" id="prop_12304035-21484" type="checkbox" checked="" value="12304035:21484">
				var input = $("<input>").addClass("J_Checkbox").attr({
					name: 'cp_' + n.spec_value_id,
					id: 'prop_' + n.spec_id + '_' + n.spec_value_id,
					type: 'checkbox'
				}).val(n.spec_id + ':' + n.spec_value_id);
				//判断选中状态								
				if (global_value && global_value[n.spec_id] != undefined && $.inArray(n.spec_value_id, global_value[n.spec_id]) >= 0) {
					input.attr('checked', true);
				}
				//<label title="128MB" class="labelname" for="prop_12304035-21484">128MB</label>		 
				var label = $("<label>").addClass('labelname').attr({
					title: n.spec_value_name,
					'for': 'prop_' + n.spec_id + '_' + n.spec_value_id
				}).text(n.spec_value_name);
				input.appendTo(li);
				label.appendTo(li);
				li.appendTo(ul);

			});
		}

		ul.appendTo(div);
		return div;
	}
}

/**
 * 商品规格选择
 */
var goods_spec_stock = {
	tbody: $('<tbody>'),
	global_value_array: new Array(),
	global_spec_array: new Array(),
	global_spec_stock_array: new Array(),
	spec_assemble: new Array(),
	//重组数据
	init: function() {
		for (var i in goods_sku_array) {
			goods_sku_key_value_array[i] = i.split('-');
		}
	},
	createNode: function() {
		var that = this;
		var table = $('<table>').addClass('table-sku-stock');
		var thead = $('<thead>');
		var tr = $('<tr>');

		var count = 0;
		for (var i in global_value) {
			count++;
		}
		if (count == 0) {
			//同时在删除完后把单品的 价格，库存，商品编码 再展示出来				
			//          $('#i_no_sku_price_wrap').show();
			//          $('#i_no_sku_stock_wrap').show();
			//          $('#coding').show();
			$("#product_price,#product_stock,#product_code").removeClass("am-hide");
			$('#goods_sku_stock').hide();
			return false;
		} else {
			$('#goods_sku_stock').show();
		}
		for (var spec_id in global_value) {
			//判断是否有规格属性值
			if (global_value[spec_id].length == 0) {
				continue;
			}
			var spec_object = $.grep(global_param.spec_tree, function(n, i) {
				return n.spec_id == spec_id;
			});
			$('<th>').addClass('text-center').html(spec_object[0].spec_name).appendTo(tr);
		}

		$('<th>').addClass('th-price').html('价格（元）').appendTo(tr);
		$('<th>').addClass('th-stock').html('库存').appendTo(tr);
		$('<th>').addClass('th-code').html('商家编码').appendTo(tr);
		$('<th>').addClass('text-right').html('销量').appendTo(tr);
		tr.appendTo(thead);

		//开始生成tbody
		that.tbody = $('<tbody>');

		var i = 0;
		that.global_spec_array = [];
		that.global_value_array = [];
		for (var spec_id in global_value) {
			//判断是否有规格属性值
			if (global_value[spec_id].length == 0) {
				continue;
			}
			that.global_spec_array[i] = spec_id;
			that.global_value_array[i] = global_value[spec_id];
			i++;
		}
		that.spec_assemble = that.getSpecAssemble(that.global_value_array);

		var tr_dom = that.createTrDom(that.spec_assemble);

		//生成tfoot
		var tfoot = $('<tfoot>');
		var tr = $('<tr>');
		var td = $('<td>').attr({
			'colspan': 6
		});
		var div = $('<div>').addClass('batch-opts').attr({
			'id': 'batch-opts'
		}).html('批量设置：');
		var span = $('<span>').addClass('js-batch-type');

		div.appendTo(td);
		td.appendTo(tr);
		tr.appendTo(tfoot);

		$('<a>').addClass('js-batch-price').attr({
			'href': 'javascript:;'
		}).html('价格').appendTo(span);
		span.append("&nbsp;&nbsp;");
		$('<a>').addClass('js-batch-stock').attr({
			'href': 'javascript:;'
		}).html('库存').appendTo(span);

		span.appendTo(div);

		var span = $('<span>').addClass('js-batch-form').css('display', 'none');
		$('<input>').addClass('js-batch-txt input-mini').attr({
			'type': 'text',
			'placeholder': ''
		}).appendTo(span);
		$('<a>').addClass('js-batch-save').attr({
			'href': 'javascript:;'
		}).html('保存').appendTo(span);
		$('<a>').addClass('js-batch-cancel').attr({
			'href': 'javascript:;'
		}).html('取消').appendTo(span);
		$('<p>').addClass('help-desc').appendTo(span);

		span.appendTo(div);

		thead.appendTo(table);
		that.tbody.appendTo(table);
		tfoot.appendTo(table);

		$('#stock-region').empty();
		table.appendTo($('#stock-region'));

		//绑定批量设置的事件
		that._bind_batch_opts();

		var i = 0;
		for (var spec_id in global_value) {
			//判断是否有规格属性值
			if (global_value[spec_id].length == 0) {
				continue;
			}
			$('#stock-region').rowspan(i); //传入的参数是对应的列数从0开始，哪一列有相同的内容就输入对应的列数值				
			i++;
		}
	},
	//绑定批量设置的事件
	_bind_batch_opts: function() {
		//批量设置价格
		$('#batch-opts .js-batch-price').off('click').on('click', function() {
			$('#batch-opts .js-batch-type').hide();
			$('#batch-opts .js-batch-form').show();
			$('#batch-opts .js-batch-txt').attr({
				'placeholder': '请输入价格'
			});
		});
		//批量设置库存
		$('#batch-opts .js-batch-stock').off('click').on('click', function() {
			$('#batch-opts .js-batch-type').hide();
			$('#batch-opts .js-batch-form').show();
			$('#batch-opts .js-batch-txt').attr({
				'placeholder': '请输入库存'
			});
		});
		//保存
		$('#batch-opts .js-batch-save').off('click').on('click', function() {
			$('#batch-opts .js-batch-type').show();
			$('#batch-opts .js-batch-form').hide();
			var value = $('#batch-opts .js-batch-txt').val();
			var type = $('#batch-opts .js-batch-txt').attr('placeholder');
			//库存 和 价格的 warning message 取消
			if (type == '请输入价格') {
				//批量输入价格
				$.each($('#stock-region .table-sku-stock input[name="sku_price"]'), function(i, n) {
					$(n).val(value);
					var td = $(n).parent();
					td.removeClass('manual-valid-error');
				});
			} else if (type == '请输入库存') {
				//批量输入库存
				$.each($('#stock-region .table-sku-stock input[name="stock_num"]'), function(i, n) {
					$(n).val(value);
					var td = $(n).parent();
					td.removeClass('manual-valid-error');
				});
			}
		});
		//取消
		$('#batch-opts .js-batch-cancel').off('click').on('click', function() {
			$('#batch-opts .js-batch-type').show();
			$('#batch-opts .js-batch-form').hide();
		});
		//监听blur事件
		$('#stock-region .table-sku-stock input[name="sku_price"]').off('blur').on('blur', function() {
			var value = $(this).val();
			if (value) {
				var td = $(this).parent();
				td.removeClass('manual-valid-error');
			}
		});
		$('#stock-region .table-sku-stock input[name="stock_num"]').off('blur').on('blur', function() {
			var value = $(this).val();
			if (value) {
				var td = $(this).parent();
				td.removeClass('manual-valid-error');
			}
		});
	},
	//取spec sku的组合
	getSpecAssemble: function(spec_array) {
		var that = this;
		var level_head = spec_array[0];
		var len = spec_array.length;
		for (var i = 1; i < len; i++) {
			level_head = that.addSpecAssemble(level_head, spec_array[i]);
		}
		return level_head;
	},
	//在原有组合结果的基础上添加一种新的规格
	addSpecAssemble: function(level_head, level_next_head) {
		var that = this;
		var result = [];
		for (var i = 0, len = level_head.length; i < len; i++) {
			for (var j = 0, lenj = level_next_head.length; j < lenj; j++) {
				result.push(level_head[i] + '-' + level_next_head[j]);
			}
		}
		return result;
	},
	createTrDom: function(spec_assemble) {
		var that = this;
		var spec_value_array = [];
		$.each(global_param.spec_tree, function(i, n) {
			if (n.value_list.length > 0) {
				$.each(n.value_list, function(ii, nn) {
					nn.spec_name = n.spec_name;
					spec_value_array[nn.spec_value_id] = nn;
				});
			}
		});

		for (var i = 0, len = spec_assemble.length; i < len; i++) {
			var spec_value_id_string = spec_assemble[i];
			var spec_value_id_array = spec_value_id_string.split('-');

			var tr = $('<tr>').attr('goods_sku_id', spec_value_id_string);
			for (var j = 0, lenj = spec_value_id_array.length; j < lenj; j++) {
				var spec_value_id = spec_value_id_array[j];

				var spec_value_object = spec_value_array[spec_value_id];
				//<td data-atom-id="187" rowspan="1">白色</td>					
				var td = $('<td>').attr({
					'data-atom-id': spec_value_id
				}).html(spec_value_object.spec_value_name);
				td.appendTo(tr);
			}

			//已经存在的库存数据加载
			var goods_sku_object = that.getGoodsSkuArray(spec_value_id_string);
			if (!goods_sku_object) {
				var goods_sku_object = {
					'goods_sku': '',
					'price': '',
					'stock': '',
					'outer_code': '',
					'sales_volume': 0
				}
			}

			//var td = $('<td>').addClass('manual-valid-error');
			var td = $('<td>');
			$('<input>').addClass('js-price input-mini').attr({
				'data-stock-id': 0,
				'type': 'text',
				'name': 'sku_price',
				'maxlength': 10
			}).val(goods_sku_object.price).appendTo(td);
			$('<div>').addClass('error-message').html('价格最小为 0.01').appendTo(td);
			td.appendTo(tr);

			//var td = $('<td>').addClass('manual-valid-error');
			var td = $('<td>');
			$('<input>').addClass('js-price input-mini').attr({
				'type': 'text',
				'name': 'stock_num',
				'maxlength': 9
			}).val(goods_sku_object.stock).appendTo(td);
			$('<div>').addClass('error-message').html('库存不能为空').appendTo(td);
			td.appendTo(tr);

			var td = $('<td>');
			$('<input>').addClass('js-code input-small').attr({
				'type': 'text',
				'name': 'code'
			}).val(goods_sku_object.outer_code).appendTo(td);
			td.appendTo(tr);

			var td = $('<td>').addClass('text-right').html(goods_sku_object.sales_volume);
			td.appendTo(tr);
			tr.appendTo(that.tbody);
		}
	},
	//get Goods Sku Array For Goods edit
	getGoodsSkuArray: function(spec_value_id_string) {
		var spec_value_id_array = spec_value_id_string.split('-');
		var result = null;

		for (var i in goods_sku_key_value_array) {
			var status = true;
			for (var j = 0, lenj = spec_value_id_array.length; j < lenj; j++) {
				var spec_value_id = spec_value_id_array[j];
				if ($.inArray(spec_value_id, goods_sku_key_value_array[i]) < 0) {
					status = false;
					break;
				}
			}
			if (status == true) {
				result = goods_sku_array[i];
				break;
			}
		}
		return result;
	}
}