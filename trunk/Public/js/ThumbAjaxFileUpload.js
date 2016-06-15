function ajaxFileUpload(fid,purl,did, img_url, img_div, photo_id)
{
	if($("#"+fid+"").val()==''){
		alert('请先选择要上传的');
		$("#"+fid+"").focus();
		return false;
	}
    $(did).show();

    $.ajaxFileUpload
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
						$("#"+img_url+"").val(data.photo_url);
						$("#"+img_div).html('<img src="'+data.photo_url+'" width="150" height="120" style="margin-bottom:6px">');						
						if ( $('#'+photo_id) !== undefined ) {													
							$('#'+photo_id).val(data.photo_id);	
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

function pics_delete(i,name){
    $("#"+name+i).val("");
    image_preview(name+i,"",1);
    $("#"+name+i+"_up").css("display","none");
}

function pics_delete1(name){
    $("#pics"+name).val("");
    image_preview(name,"",1);
    $("#pics"+name+"_up").css("display","none");
}

function getuploadhtml(i,name,purl){
    cname = name;
    name = name+i;
    var code = '<div id="'+name+'_up"><span id="'+name+'_preview"></span><br><br>地址：<input name="'+name+'" id="'+name+'" value="" size="50"/> <input id="'+name+i+'_del" type="button" name="delbutton" value="删除" onclick="pics_delete("'+name+i+'").value)" style="display:none;"><br><br>'
    code += '上传：<input type="file" name="'+name+'_upload" id="'+name+'_upload" style="width:400px" onchange="image_preview(\''+name+'\',this.value,1)"/>&nbsp;&nbsp;<input type="button" name="'+name+'upload" id="'+name+'upload'+i+'" onclick="return ajaxFileUpload2(\''+name+'_upload\',\''+purl+'\',\'#'+cname+'_loading\');" value="上传" /></div>';
    return code;
}
function getuploadhtml1(i,name,purl){
	//i=Integer.valueOf(i).intValue();
	//name=Integer.valueOf(name).intValue();
	cname = name; 
    name = name+1;
    var code = '<div id="pics'+cname+'_up"><span id="pics'+cname+'_preview"></span><br>地址：<input name="pics'+cname+'" id="pics'+cname+'" value="" size="50"/> <input id="pics'+cname+'_del" type="button" name="delbutton" value="删除" onclick="pics_delete1('+cname+')" style="display:none;"><br>'
    code += '上传：<input type="file" name="pics'+cname+'_upload" id="pics'+cname+'_upload" style="width:400px" onchange="image_preview(\'pics'+cname+'\',this.value,1)"/>&nbsp;&nbsp;<input type="button" name="pics'+cname+'upload" id="pics'+cname+'upload'+i+'" onclick="return ajaxFileUpload3(\'pics'+cname+'_upload\',\''+purl+'\',\'#pics'+cname+'_loading\','+name+');" value="上传" /></div>';
    return code;
}
var filecount =1;
function ajaxFileUpload2(fid,purl,did,name)//批量上传
{
    $(did)
    .ajaxStart(function(){
        $(this).show();
    })
    .ajaxComplete(function(){
        $(this).hide();
    });

    $.ajaxFileUpload
    (
    {
        url:purl,
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
                    alert(data.msg);
                }
            }

            for(key in data) {
				
                if(data[key].code) {
                    code=data[key].code.replace(/&lt;/mg, "<");
                    eval(code);
                }
            }
            $(getuploadhtml(filecount,name,purl)).appendTo("#uploadarea");
            filecount++;
        },
        error: function (data, status, e)
        {
            alert('上传失败！请检查附件大小、格式！');
        },

        complete: function () {

        }
    }
    )

    return false;

}

function ajaxFileUpload3(fid,purl,did,name)//批量上传
{
    $(did)
    .ajaxStart(function(){
        $(this).show();
    })
    .ajaxComplete(function(){
        $(this).hide();
    });

    $.ajaxFileUpload
    (
    {
        url:purl,
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
                    alert(data.msg);
                }
            }

            for(key in data) {
				
                if(data[key].code) {
                    code=data[key].code.replace(/&lt;/mg, "<");
                    eval(code);
                }
            }
            $(getuploadhtml1(filecount,name,purl)).appendTo("#uploadarea");
            filecount++;
        },
        error: function (data, status, e)
        {
            alert('上传失败！请检查附件大小、格式！');
        },

        complete: function () {

        }
    }
    )

    return false;

}


function image_preview(name,pic,limit) {
    if(pic) {
        $("#"+name+"_del").css("display","");
        if(limit)
            $(name+'_preview').innerHTML='<img src="'+pic+'" width="150">';
        else
            $(name+'_preview').innerHTML='<img src="'+pic+'">';
    }
    else $(name+'_preview').innerHTML='';
}


function addfiletoconent() {
    var FCKoEditor = FCKeditorAPI.GetInstance('content');
    inImg  = '<p><a href="'+get('attachment_path').value+'">附件：'+get('attachment_intro').value+'</a></p>';
    FCKoEditor.InsertHtml(inImg) ;
}

