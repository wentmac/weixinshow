var testTemplate = [
	'<div id="upload_div_${id}">',
		'<div class="am-input-group">',
			'<span id="thumb_preview${id}"></span>',
		'</div>',
		'<div class="am-input-group">',
			'<span class="am-input-group-label">链&nbsp;&nbsp;&nbsp;接：</span>',
			'<input type="text" class="am-form-field" size="96" value="" name="thumburl[]" placeholder="goods/12314.html">',
		'</div>',
		'<div class="am-input-group">',
			'<span class="am-input-group-label">描&nbsp;&nbsp;&nbsp;述：</span>',
			'<input type="text" class="am-form-field" size="96" value="" name="thumbdes[]" placeholder="广告图片的描述内容">',
		'</div>',
		'<div class="am-input-group">',
			'<span class="am-input-group-label">地&nbsp;&nbsp;&nbsp;址：</span>',
			'<input type="text" class="am-form-field" size="96" value="" id="thumb${id}" readonly="readonly">',
		'</div>',
		'<div class="am-input-group">',
			'<span class="am-input-group-label">自定义：</span>',
			'<input type="text" class="am-form-field" size="96" value="" name="self_field[]">',
		'</div>',
		'<div class="am-input-group">',
			'<span class="am-input-group-label">排&nbsp;&nbsp;&nbsp;序：</span>',
			'<input type="text" class="am-form-field" size="96" value="" name="sort[]" id="sort${id}" placeholder="越大越靠前">',
		'</div>',
		'<input type="hidden" value="" name="${fileName}" id="imgid_${id}">',
		'<div class="am-input-group">',
			'<span class="am-input-group-label">上&nbsp;&nbsp;&nbsp;传：</span>',
			'<input type="file" class="am-btn am-btn-default" onchange="image_preview(\'thumb${id}\',this.value,1)" id="thumb_upload${id}" name="thumb_upload${id}">',
			'<input type="button" class="am-btn am-btn-success" value="上传" onclick="return ajaxFileUpload(\'thumb_upload${id}\',\'/${php_self}?m=tool.uploadImageByAjax&filename=thumb_upload${id}&action=poster&size=600x200\',\'#thumb_loading${id}\', \'thumb${id}\', \'thumb_preview${id}\',\'imgid_${id}\');" id="thumbupload${id}" name="thumbupload${id}">',
			'<input type="button" class="am-btn am-btn-danger" value="删除" onclick="delimg(\'upload_div_${id}\')" />',
		'</div>',
		'<img style="display:none;" src="${static_url}js/loading.gif" id="thumb_loading${id}">',
	'</div>'
	].join('');

function add_seriesInit(){	
	//获取了页面所有的file
	 var file = $('input:file').get();                        
	 var fileNum = file.length; 	
	 var id = fileNum+1;
	 var fileName = 'thumb[]'; 
	 
	 var data = {		
		'id':id,
		'fileName':fileName,
		'static_url':static_url,
		'php_self':php_self
	 };
  
	var thumbStr = $.tmpl( testTemplate, data );
	$("#add_seriesArea").before(thumbStr);
};
	
function delimg(id){
	$("#"+id+"").remove();
}	
$(document).ready(function() {
	$('#add').click(function(){
		add_seriesInit();
	});
});