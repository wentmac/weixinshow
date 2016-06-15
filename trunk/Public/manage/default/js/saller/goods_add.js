function remove_model_item(obj) {
    var item = $(obj).parents(".model_item");
    if ($(".model_item").length > 1) {
        item.remove();
    } else {
        var modal_dom_id = 'my-confirm';
        MODAL_HTML._confirm(modal_dom_id, '删除确认', '您确定要删除这个型号吗？', '取消', '删除');
        $('#' + modal_dom_id).modal({
            relatedTarget: obj,
            onConfirm: function(options) {
                $(this.relatedTarget).parents("tr.model_item").remove();
                $("#product_model").addClass("am-hide");
                $("#product_price,#product_stock,#product_code").removeClass("am-hide");
            },
            onCancel: function() {
                //alert('算求，不弄了');
            }
        });
    }
}

$(document).ready(function() {
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


    /*保存商品*/
    $("#submit_i_do_item").bind("click", function() {
        var that = $(this);
        //提交后必选校验
        if (!check_required_options.init($(this))) {
            return false;
        }
        var dataParam = postField;
        $.ajax({
            type: "POST",
            url: index_url + php_self + '?m=seller/goods.save',
            dataType: "json",
            data: dataParam,
            cache:false,
            success: function(data) {
                //console.log(data);
                var modal_dom_id = "my-alert";
                if (data.success == true) {
                    MODAL_HTML._alert(modal_dom_id, "操作成功", "商品更新成功！", "确定");
                    $("#" + modal_dom_id).modal();
                    //M._alert('商品更新成功');
                    check_required_options._enable_btn(that);
                } else {
                    MODAL_HTML._alert(modal_dom_id, "操作失败", data.message, "确定");
                    $("#" + modal_dom_id).modal();
                    //M._alert(data.message);
                    check_required_options._enable_btn(that);
                    return false;
                }
            }
        });
    });

    //默认展示的图片类 编辑时
    goods_image.init();
    //产品型号操作的类
    goods_model.init();

});


//校验必选
var check_required_options = {
    _disable_btn: function(_this) {
        if (_this.hasClass("isLoading")) {
            //return false;
        } else {
            _this.addClass('isLoading').html('<i class="am-icon-spinner am-icon-spin"></i> 提交中').attr('disabled', 'disabled');
        }
    },
    _enable_btn: function(_this) {
        if (_this.hasClass("isLoading")) {
            _this.removeClass('isLoading').html('<i class="am-icon-check-circle"></i> 提　交').removeAttr('disabled');
        }
    },
    init: function(_this) {
        if (_this.hasClass("isLoading")) {
            return false;
        }
        this._disable_btn(_this);

        var modal_dom_id = 'my-alert';
        //图片
        if (!postField.image_array.length) {
            MODAL_HTML._alert(modal_dom_id, "错误提醒", "请至少上传一张商品图片！", "确定");
            $("#" + modal_dom_id).modal();
            //M._alert("请至少上传一张商品图片");
            this._enable_btn(_this);
            return false;
        }
        //商品名
        var goods_name = $('#goods_name').val();
        if (!goods_name) {
            MODAL_HTML._alert(modal_dom_id, "错误提醒", "请填写商品名！", "确定");
            $("#" + modal_dom_id).modal();
            //M._alert("请填写商品名");
            $('#goods_name').focus();
            this._enable_btn(_this);
            return false;
        }
        //分类
        var item_cat_id = new Array();
        var goods_cat_selectval = "";
        goods_cat_selectval = $('#item_cat_id').val();
        if (goods_cat_selectval) {
            var ary_goods_cat_val = goods_cat_selectval.toString().split(",");
            for (var i = 0; i < ary_goods_cat_val.length; i++) {
                item_cat_id.push(ary_goods_cat_val[i]);
            }
        }

        //型号
        var count = $("tr.model_item").length;


        postField.goods_name = $('#goods_name').val();
        postField.goods_desc = $('#i_des').val();
        postField.item_cat_id = item_cat_id;
        postField.item_id = $('#item_id').val();

        var goods_sku_stock_object = {};

        if (count > 0) { //有型号
            var error_message_status = false;
            var error_message_msg = "";
            global_value = {
                "1": []
            };

            $("tr.model_item").each(function(i) {
                var modelid = $(this).find("input.model_id").val();
                var modelname = $(this).find("input.model_name").val();
                var modelcode = $(this).find("input.model_code").val();
                var modelprice = $(this).find("input.model_price").val();
                var modelstock = $(this).find("input.model_stock").val();

                var index = i + 1;

                if (modelname == "") {
                    error_message_status = true;
                    error_message_msg += "第" + index + "个型号的名称没有填写！<br>";
                }
				if ( !isNaN ( modelname ) ) {
					error_message_status = true;
					error_message_msg += "第" + index + "个型号的名称不能为数字！<br>";
				}				
                if (modelprice == "") {
                    error_message_status = true;
                    error_message_msg += "第" + index + "个型号的价格没有填写！<br>";
                } else if (!$.isNumeric(modelprice) || modelprice <= 0) {
                    error_message_status = true;
                    error_message_msg += "第" + index + "个型号的价格必须填写大于0的数字！<br>";
                }
                if (modelstock == "") {
                    error_message_status = true;
                    error_message_msg += "第" + index + "个型号的库存没有填写！<br>";
                } else if (!$.isNumeric(modelstock) || modelstock <= 0) {
                    error_message_status = true;
                    error_message_msg += "第" + index + "个型号的库存必须填写大于0的数字！<br>";
                }

                if (!error_message_status) {
                    var key = modelname;
                    if (modelid > 0) {
                        key = modelid;
                    }
                    global_value["1"].push(key);
                    goods_sku_stock_object[key] = {
                        sku_name: key,
                        sku_code: modelcode,
                        sku_price: modelprice,
                        sku_stock: modelstock
                    };
                    return true;
                } else {
                    return false;
                }
            });

            if (error_message_status) {
                MODAL_HTML._alert(modal_dom_id, "错误提醒", error_message_msg, "确定");
                $("#" + modal_dom_id).modal();
                //M._alert(error_message_msg);
                this._enable_btn(_this);
                return false;
            }

            //规格对象数组
            postField.goods_spec_array = global_value;
            postField.goods_sku_stock = goods_sku_stock_object;

        } else { //没有型号

            //价格
            var i_no_sku_price = $('#i_no_sku_price').val();
            if (!i_no_sku_price) {
                MODAL_HTML._alert(modal_dom_id, "错误提醒", "请填写商品价钱", "确定");
                $("#" + modal_dom_id).modal();
                //M._alert("请填写商品价钱");
                $('#i_no_sku_price').focus();
                this._enable_btn(_this);
                return false;
            }
            if (isNaN(i_no_sku_price)) {
                MODAL_HTML._alert(modal_dom_id, "错误提醒", "商品价钱应该为数字", "确定");
                $("#" + modal_dom_id).modal();
                //M._alert("商品价钱应该为数字");
                $('#i_no_sku_price').focus();
                this._enable_btn(_this);
                return false;
            }
            if (i_no_sku_price <= 0) {
                MODAL_HTML._alert(modal_dom_id, "错误提醒", "商品价格不能为0", "确定");
                $("#" + modal_dom_id).modal();
                //M._alert("商品价格不能为0");
                $('#i_no_sku_price').focus();
                this._enable_btn(_this);
                return false;
            }

            //库存
            var i_no_sku_stock = $("#i_no_sku_stock").val();
            if (!i_no_sku_stock) {
                MODAL_HTML._alert(modal_dom_id, "错误提醒", "请填写商品库存", "确定");
                $("#" + modal_dom_id).modal();
                //M._alert("请填写商品库存");
                $("#i_no_sku_stock").focus();
                this._enable_btn(_this);
                return false;
            }
            if (isNaN(i_no_sku_stock)) {
                MODAL_HTML._alert(modal_dom_id, "错误提醒", "商品库存应该为数字", "确定");
                $("#" + modal_dom_id).modal();
                //M._alert("商品库存应该为数字");
                $("#i_no_sku_stock").focus();
                this._enable_btn(_this);
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
        }
        return true;
    }
}

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
                //console.log(goods_image);
                var li = $('<li>').addClass('upload-preview-img sort').attr({
                    'goods_image': 'edit'
                });
                $('<img>').addClass('qq-thumbnail-selector').attr({
                    'qq-max-size': 80,
                    'src': goods_image
                }).appendTo(li);
                $('<a>').addClass('close-modal small js-remove-image qq-upload-cancel-selector').attr({
                    'href': 'javascript:;',
                    'data-image-id': goods_image_id
                }).html('x').appendTo(li);
                li.appendTo(span);
            }

            $('#fine-uploader .qq-upload-list-selector li[goods_image="edit"] a').off('click').on('click', function(event) {
                event.stopImmediatePropagation();
                event.preventDefault();
                var goods_image_id = $(this).attr('data-image-id');
                //ajax todo delete ImageId {item_id,goods_image_id}//进行权限校验
                that._ajaxDeleteImage(goods_image_id, $(this).parent());
            });
        },
        _ajaxDeleteImage: function(goods_image_id, li_ele) {
            var dataParam = {
                'item_id': $('#item_id').val(),
                'goods_image_id': goods_image_id
            };
            $.ajax({
                type: "GET",
                url: index_url + php_self + '?m=seller/goods.deleteGoodsImage',
                dataType: "json",
                data: dataParam,
                async: false,
                cache:false,
                success: function(data) {
                    if (data.success == true) {
                        li_ele.remove();
                    } else {
                        M._alert(data.message);
                        return false;
                    }
                }
            });
        }
    }
    //产品型号操作的类
var goods_model = {
    init: function() {
        var that = this;
        $("#btn_add_model").click(function() { //添加型号按钮事件
            var modelitem = new Object();
            modelitem.id = 0;
            modelitem.name = "";
            modelitem.price = "";
            modelitem.stock = "";
            modelitem.outercode = "";
            modelitem.sales = "";
            that.additem(modelitem);
        });
        console.log(global_value["1"]);
        if ($.isArray(global_value["1"])) {
            for (var i = 0; i < global_value["1"].length; i++) {
                var _key = global_value["1"][i];
                var _modelname = "";
                for (var j = 0; j < global_param.spec_tree.length; j++) {
                    if (global_param.spec_tree[j].spec_id == "1") {
                        var valuelist = global_param.spec_tree[j].value_list;
                        for (var h = 0; h < valuelist.length; h++) {
                            if (valuelist[h].spec_value_id == _key) {
                                _modelname = valuelist[h].spec_value_name;
                                break;
                            }
                        }
                        break;
                    }
                }
                var _skuitem = goods_sku_array[_key];
                //if ($.isArray(_skuitem)) {
                    var modelitem = new Object();
                    modelitem.id = _key;
                    modelitem.name = _modelname;
                    modelitem.price = _skuitem.price;
                    modelitem.stock = _skuitem.stock;
                    modelitem.outercode = _skuitem.outer_code;
                    modelitem.sales = _skuitem.sales_volume;
                    this.additem(modelitem);
                //}
            }
        }
    },
    additem: function(item) {
        var template = '<tr class="model_item"><td width="140" class="input_td"><input type="hidden" class="model_id" value="[model_id]"><input type="text" class="model_name invisible_input am-text-center" value="[model_name]"></td><td width="140" class="input_td"><input type="text" class="model_code invisible_input am-text-center" value="[model_code]"></td><td width="120" class="input_td"><input type="text" class="model_price invisible_input am-text-center" value="[model_price]"></td><td width="110" class="input_td"><input type="text" class="model_stock invisible_input am-text-center" value="[model_stock]"></td><td class="am-text-center"><a href="javascript:void(0)" onclick="remove_model_item(this)" class="am-text-danger">删除</a></td></tr>';
        template = template.replace("[model_id]", item.id);
        template = template.replace("[model_name]", item.name);
        template = template.replace("[model_code]", item.outercode);
        template = template.replace("[model_price]", item.price);
        template = template.replace("[model_stock]", item.stock);
        $("#product_price,#product_stock,#product_code").addClass("am-hide");
        $("#product_model").removeClass("am-hide");
        $("#product_model table tr").last().after(template);
    }
}
