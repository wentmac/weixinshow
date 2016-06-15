function ajaxUpload(fid,purl,did, file_url, file_div, file_id)
{
	if($("#"+fid+"").val()==''){
		alert('请先选择要上传的');
		$("#"+fid+"").focus();
		return false;
	}
    $(did).show();

    jq.ajaxFileUpload
    (
		{
			url:purl,
			//data: 'catid='+$('#catid'),
			secureuri:false,
			fileElementId:fid,
			dataType: 'json',
			type: 'POST',
			success: function (data, status)
			{					
				if(typeof(data.error) != 'undefined')
				{
					if(data.error != '')
					{
						alert(data.error);
					}else
					{
						//alert(data.url);
						$("#"+file_url+"").val(data.url);
						$("#"+file_div).html('<a href="'+data.url+'" target="_blank">下载附件</a>');						
						if ( $('#'+file_id) !== undefined ) {													
							$('#'+file_id).val(data.id);	
						}
					}
				}

				for(key in data) {
					
					if(data[key].code) {
						code=data[key].code.replace(/&lt;/mg, "<");
						eval(code);
					}
				}
				
			},
			error: function (data, status, e)
			{						
				alert(allPrpos(data));
				alert(status);
				alert(allPrpos(e));
				alert('上传失败！请检查附件大小、格式！');
			},

			complete: function () {
				$(did).hide();
			}
		}
    )

    return false;

}

/**
 * 选择文件后自动上传
 */
function auto_upload(name,pic) {
    if(pic) {
        $("#"+name+"_del").css("display","");        
        $(name+'_preview').innerHTML='<img src="'+pic+'">';
		$('#thumbupload').click();
    }
    else $(name+'_preview').innerHTML='';
}