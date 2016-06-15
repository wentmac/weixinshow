var param = {};
var manualuploader = $('#fine-uploader-signboard').fineUploader({
		autoUpload: false,
		template: "qq-simple-thumbnails-template",
		request: {
			endpoint: index_url + php_self + '?m=tool.upload_image_by_ajax&filename=qqfile&action=shop&size=640x330'
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
		},
		//单选上传
		multiple: false
	})
	.on('complete', function(event, id, name, responseJSON, xhr) {
		if (responseJSON.success == true) {
			//图片数组中增加新传的
			var shop_signboard_image_id = responseJSON.newUuid;
			//postField.image_array.push(responseJSON.newUuid);

			$("#hid_signboard").val(shop_signboard_image_id);

			$("#dianzhao").attr("src", responseJSON.uploadName);
		}
	})
	.on('deleteComplete', function(event, id, xhr, isError) {
		responseJSON = eval("(" + xhr.response + ")");
		if (responseJSON.success == true) {
			//图片数组中增加新传的
			responseJSON.uuid.replace("\/", "/");
			var shop_signboard_image_id = responseJSON.newUuid;
			console.log('删除店招图片ID：' + responseJSON);
			//postField.image_array.remove(responseJSON.uuid);
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

var manualuploader_logo = $('#fine-uploader-logo').fineUploader({
		autoUpload: false,
		template: "logo-template",
		request: {
			endpoint: index_url + php_self + '?m=tool.upload_image_by_ajax&filename=qqfile&action=shop'
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
		},
		//单选上传
		multiple: false
	})
	.on('complete', function(event, id, name, responseJSON, xhr) {
		if (responseJSON.success == true) {
			//图片数组中增加新传的
			var shop_signboard_image_id = responseJSON.newUuid;
			//postField.image_array.push(responseJSON.newUuid);

			$("#hid_logo").val(shop_signboard_image_id);

			$("#shop_logo").attr("src", responseJSON.uploadName);
		}
	})
	.on('deleteComplete', function(event, id, xhr, isError) {
		responseJSON = eval("(" + xhr.response + ")");
		if (responseJSON.success == true) {
			//图片数组中增加新传的
			responseJSON.uuid.replace("\/", "/");
			var shop_signboard_image_id = responseJSON.newUuid;
			console.log('删除店招图片ID：' + responseJSON);
			//postField.image_array.remove(responseJSON.uuid);
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



$(function() {
	$('#triggerUpload').click(function() {
		manualuploader.fineUploader('uploadStoredFiles');
	});
	$('#triggerUpload_logo').click(function() {
		manualuploader_logo.fineUploader('uploadStoredFiles');
	});




	if (payment_type == "1") {
		$("#rad_payment_type_yes").attr("checked", "checked");
	} else if (payment_type == "0") {
		$("#rad_payment_type_no").attr("checked", "checked");
	}
	if (refund_type == "1") {
		$("#rad_refund_type2").attr("checked", "checked");
	} else if (refund_type == "0") {
		$("#rad_refund_type1").attr("checked", "checked");
	}

	if (stock_setting == "1") {
		$("#rad_stock_setting1").attr("checked", "checked");
	} else if (stock_setting == "0") {
		$("#rad_stock_setting2").attr("checked", "checked");
	}

	if (goods_show_type == "1") {
		$("#rad_goods_show_type0").attr("checked", "checked");
	} else if (goods_show_type == "2") {
		$("#rad_goods_show_type1").attr("checked", "checked");
	}


	$("#btn_submit").click(function() {
		param.shop_name = $("#txt_shopname").val();
		param.shop_intro = $("#shop_intro").val();
		param.weixin_id = $("#txt_weixin").val();
		param.shop_template_id = shop_template_id;
		param.shop_address = $("#shop_address").val();
		param.shop_image_id = $("#hid_logo").val();
		param.shop_signboard_image_id = $("#hid_signboard").val();
		if ($("#rad_goods_show_type0").is(":checked")) {
			param.goods_show_type = 1;
		} else {
			param.goods_show_type = 2;
		}
		if ($("#rad_payment_type_yes").is(":checked")) {
			param.payment_type = 1;
		} else {
			param.payment_type = 0;
		}
		if ($("#rad_refund_type2").is(":checked")) {
			param.refund_type = 1;
		} else {
			param.refund_type = 0;
		}
		param.is_guarantee_transaction = is_guarantee_transaction;
		if ($("#rad_stock_setting1").is(":checked")) {
			param.stock_setting = 1;
		} else {
			param.stock_setting = 2;
		}

		$.ajax({
			type: "post",
			url: php_self + "?m=shop.modify",
			data: param,
			success: function(data) {
				if (data.success == true) {
					M._alert("店铺设置成功");
				} else {
					M._alert(data.messgae);
				}
			}
		});
	});


});