var modal_dom_id = "my-alert";

// 初始化Web Uploader1
var uploader1 = WebUploader.create({
  // 选完文件后，是否自动上传。
  auto: true,
  // swf文件路径
  swf: '{STATIC_URL}js/webuploader/' + 'Uploader.swf',
  // 文件接收服务端。
  server: index_url + php_self + '?m=tool.upload_image_by_ajax&filename=file&action=idcard',
  // 选择文件的按钮。可选。
  // 内部根据当前运行是创建，可能是input元素，也可能是flash.
  pick: {
    id: '#filePicker1',
    multiple: false
  },
  multiple: false,
  // 只允许选择图片文件。
  accept: {
    title: 'Images',
    extensions: 'gif,jpg,jpeg,png',
    mimeTypes: 'image/*'
  }
});

// 初始化Web Uploader2
var uploader2 = WebUploader.create({
  // 选完文件后，是否自动上传。
  auto: true,
  // swf文件路径
  swf: '{STATIC_URL}js/webuploader/' + 'Uploader.swf',
  // 文件接收服务端。
  server: index_url + php_self + '?m=tool.upload_image_by_ajax&filename=file&action=idcard',
  // 选择文件的按钮。可选。
  // 内部根据当前运行是创建，可能是input元素，也可能是flash.
  pick: {
    id: '#filePicker2',
    multiple: false
  },
  multiple: false,
  // 只允许选择图片文件。
  accept: {
    title: 'Images',
    extensions: 'gif,jpg,jpeg,png',
    mimeTypes: 'image/*'
  }
});

// 初始化Web Uploader3
var uploader3 = WebUploader.create({
  // 选完文件后，是否自动上传。
  auto: true,
  // swf文件路径
  swf: '{STATIC_URL}js/webuploader/' + 'Uploader.swf',
  // 文件接收服务端。
  server: index_url + php_self + '?m=tool.upload_image_by_ajax&filename=file&action=idcard',
  // 选择文件的按钮。可选。
  // 内部根据当前运行是创建，可能是input元素，也可能是flash.
  pick: {
    id: '#filePicker3',
    multiple: false
  },
  multiple: false,
  // 只允许选择图片文件。
  accept: {
    title: 'Images',
    extensions: 'gif,jpg,jpeg,png',
    mimeTypes: 'image/*'
  }
});

// 当有文件添加进来的时候1
uploader1.on('fileQueued', function(file) {
  var $li = $(
      '<div id="' + file.id + '" class="file-item thumbnail">' +
      '<img>' +
      '<div class="info">' + '</div>' +
      '</div>'
    ),
    $img = $li.find('img');

  $list = $("#fileList1");
  // $list为容器jQuery实例
  $list.html($li);

  // 创建缩略图
  // 如果为非图片文件，可以不用调用此方法。
  // thumbnailWidth x thumbnailHeight 为 100 x 100
  thumbnailWidth = 100;
  thumbnailHeight = 100;
  uploader1.makeThumb(file, function(error, src) {
    if (error) {
      $img.replaceWith('<span>不能预览</span>');
      return;
    }

    $img.attr('src', src);
  }, thumbnailWidth, thumbnailHeight);
});
// 当有文件添加进来的时候2
uploader2.on('fileQueued', function(file) {
  var $li = $(
      '<div id="' + file.id + '" class="file-item thumbnail">' +
      '<img>' +
      '<div class="info">' + '</div>' +
      '</div>'
    ),
    $img = $li.find('img');

  $list = $("#fileList2");
  // $list为容器jQuery实例
  $list.html($li);

  // 创建缩略图
  // 如果为非图片文件，可以不用调用此方法。
  // thumbnailWidth x thumbnailHeight 为 100 x 100
  thumbnailWidth = 100;
  thumbnailHeight = 100;
  uploader2.makeThumb(file, function(error, src) {
    if (error) {
      $img.replaceWith('<span>不能预览</span>');
      return;
    }

    $img.attr('src', src);
  }, thumbnailWidth, thumbnailHeight);
});
// 当有文件添加进来的时候3
uploader3.on('fileQueued', function(file) {
  var $li = $(
      '<div id="' + file.id + '" class="file-item thumbnail">' +
      '<img>' +
      '<div class="info">' + '</div>' +
      '</div>'
    ),
    $img = $li.find('img');

  $list = $("#fileList3");
  // $list为容器jQuery实例
  $list.html($li);

  // 创建缩略图
  // 如果为非图片文件，可以不用调用此方法。
  // thumbnailWidth x thumbnailHeight 为 100 x 100
  thumbnailWidth = 100;
  thumbnailHeight = 100;
  uploader3.makeThumb(file, function(error, src) {
    if (error) {
      $img.replaceWith('<span>不能预览</span>');
      return;
    }

    $img.attr('src', src);
  }, thumbnailWidth, thumbnailHeight);
});

// 文件上传过程中创建进度条实时显示。
var uploadProgressHandle = function(file, percentage) {
  var $li = $('#' + file.id),
    $percent = $li.find('.progress span');
  // 避免重复创建
  if (!$percent.length) {
    $percent = $('<p class="progress"><span></span></p>')
      .appendTo($li)
      .find('span');
  }
  $percent.css('width', percentage * 100 + '%');
};
uploader1.on('uploadProgress', uploadProgressHandle);
uploader2.on('uploadProgress', uploadProgressHandle);
uploader3.on('uploadProgress', uploadProgressHandle);

// 文件上传成功，给item添加成功class, 用样式标记上传成功。
uploader1.on('uploadSuccess', function(file, response) {
  $('#' + file.id).addClass('upload-state-done');
  if (response.success == true) {
    global_idcard_positive_image_id = response.newUuid;
    global_idcard_positive_image_url = response.uploadName;
  }else {
    MODAL_HTML._alert(modal_dom_id, "错误提醒", response.message, "确定");
    $("#" + modal_dom_id).modal();
  }
});
uploader2.on('uploadSuccess', function(file, response) {
  $('#' + file.id).addClass('upload-state-done');
  if (response.success == true) {
    global_idcard_negative_image_id = response.newUuid;
    global_idcard_negative_image_url = response.uploadName;
  }else {
    MODAL_HTML._alert(modal_dom_id, "错误提醒", response.message, "确定");
    $("#" + modal_dom_id).modal();
  }
});
uploader3.on('uploadSuccess', function(file, response) {
  $('#' + file.id).addClass('upload-state-done');
  if (response.success == true) {
    global_idcard_image_id = response.newUuid;
    global_idcard_image_url = response.uploadName;
  }else {
    MODAL_HTML._alert(modal_dom_id, "错误提醒", response.message, "确定");
    $("#" + modal_dom_id).modal();
  }
});

// 文件上传失败，显示上传出错。
var uploadErrorHandle = function(file) {
  var $li = $('#' + file.id),
    $error = $li.find('div.error');

  // 避免重复创建
  if (!$error.length) {
    $error = $('<div class="error"></div>').appendTo($li);
  }
  $error.text('上传失败!');
};
uploader1.on('uploadError', uploadErrorHandle);
uploader2.on('uploadError', uploadErrorHandle);
uploader3.on('uploadError', uploadErrorHandle);

// 完成上传完了，成功或者失败，先删除进度条。
var uploadComplete = function(file) {
  $('#' + file.id).find('.progress').remove();
};
uploader1.on('uploadComplete', uploadComplete);
uploader2.on('uploadComplete', uploadComplete);
uploader3.on('uploadComplete', uploadComplete);



function init_img(id, url, objid) {
  var $li = $(
      '<div id="' + id + '" class="file-item thumbnail">' +
      '<img src="' + url + '" width="100" height="100">' +
      '<div class="info">' + '</div>' +
      '</div>'
    ),
    $list = $("#" + objid);
  $list.html($li);
}

//页面加载完执行
$(function() {
  //初始化LOGO图片
  if (global_idcard_positive_image_url) {
    init_img(global_idcard_positive_image_id, global_idcard_positive_image_url, "fileList1");
  }
  if (global_idcard_negative_image_url) {
    init_img(global_idcard_negative_image_id, global_idcard_negative_image_url, "fileList2");
  }
  if (global_idcard_image_url) {
    init_img(global_idcard_image_id, global_idcard_image_url, "fileList3");
  }

  //提交按钮
  $("#btn_submit").click(function() {
    var that = $(this);
    //提交后必选校验
    if (!check_required_options.init($(this))) {
      return false;
    }
    var dataParam = postField;
    $.ajax({
      type: "POST",
      url: index_url + php_self + '?m=seller/member.idcard_save',
      dataType: "json",
      data: dataParam,
      cache:false,
      success: function(data) {
        //console.log(data);
        var modal_dom_id = "my-alert";
        if (data.success == true) {
          MODAL_HTML._alert(modal_dom_id, "操作成功", "提交成功！请等待审核！", "确定");
          $("#" + modal_dom_id).modal();
          check_required_options._enable_btn(that);
        } else {
          MODAL_HTML._alert(modal_dom_id, "操作失败", data.message, "确定");
          $("#" + modal_dom_id).modal();
          check_required_options._enable_btn(that);
          return false;
        }
      }
    });
  });
});

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

    //姓名
    var txt_realname = $("#txt_realname").val().trim();
    if (!txt_realname) {
      MODAL_HTML._alert(modal_dom_id, "错误提醒", "请填写身份证上的姓名！", "确定");$("#" + modal_dom_id).modal();
      $('#txt_realname').focus();
      this._enable_btn(_this);
      return false;
    }
    //身份证号
    var txt_cid = $("#txt_cid").val().trim();
    if (!txt_cid) {
      MODAL_HTML._alert(modal_dom_id, "错误提醒", "请填写身份证号！", "确定");$("#" + modal_dom_id).modal();
      //M._alert("请填写商品名");
      $('#txt_cid').focus();
      this._enable_btn(_this);
      return false;
    }
    if(!global_idcard_image_id){
      MODAL_HTML._alert(modal_dom_id, "错误提醒", "请上传手持身份证照片！", "确定");$("#" + modal_dom_id).modal();
      return false;
    }
    if(!global_idcard_positive_image_id){
      MODAL_HTML._alert(modal_dom_id, "错误提醒", "请上传身份证正面照片！", "确定");$("#" + modal_dom_id).modal();
      return false;
    }
    if(!global_idcard_negative_image_id){
      MODAL_HTML._alert(modal_dom_id, "错误提醒", "请上传身份证反而照片！", "确定");$("#" + modal_dom_id).modal();
      return false;
    }
    postField.idcard = txt_cid;
    postField.realname = txt_realname;
    postField.idcard_image_id = global_idcard_image_id;
    postField.idcard_positive_image_id = global_idcard_positive_image_id;
    postField.idcard_negative_image_id = global_idcard_negative_image_id;
    return true;
  }
};
